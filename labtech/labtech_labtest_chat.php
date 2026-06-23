<?php
// File overview: Handles labtech labtest chat functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Labtech','Doctor'])) {
    header("Location: ../login.php");
    exit;
}

$testID = $_GET['testid'] ?? '';
if ($testID === '') {
    echo "Invalid lab test.";
    exit;
}

ensureLabTestMessagesTable($pdo);
ensureLabTestApprovalColumns($pdo);

// Fetch lab test + patient + lab
$stmt = $pdo->prepare(
    "SELECT lt.LabTestID, lt.PatientID, lt.LabID, lt.TestName, lt.TestDate,
            p.FName AS PatientName,
            lab.LabName
     FROM lab_tests lt
     JOIN patients p ON lt.PatientID = p.PatientID
     JOIN laboratories lab ON lt.LabID = lab.LabID
     WHERE lt.LabTestID = :tid
       AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'"
);
$stmt->execute([':tid' => $testID]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$test) {
    echo "Lab test not found.";
    exit;
}
$labRatings = getEntityRatingsMap($pdo, 'Lab');

$userID = $_SESSION['user_id'];
$role = $_SESSION['role'];
$allowed = false;
$backUrl = 'labtech_chats.php';
if ($role === 'Doctor') {
    $backUrl = '../doctor/doctor_lab_results.php';
}

if ($role === 'Labtech') {
    $check = $pdo->prepare(
        "SELECT 1
         FROM labtechs l
         JOIN lab_tests lt ON lt.LabID = l.LabID
         WHERE l.LabTechID = :lid
           AND lt.LabTestID = :tid"
    );
    $check->execute([
        ':lid' => $userID,
        ':tid' => $testID
    ]);
    if ($check->fetchColumn()) {
        $allowed = true;
    }
} elseif ($role === 'Doctor') {
    $check = $pdo->prepare(
        "SELECT 1
         FROM appointments a
         WHERE a.PatientID = :pid
           AND a.DoctorID = :did
         LIMIT 1"
    );
    $check->execute([
        ':pid' => $test['PatientID'],
        ':did' => $userID
    ]);
    if ($check->fetchColumn()) {
        $allowed = true;
    }
}

if (!$allowed) {
    echo "Access denied.";
    exit;
}

ensureChatReadsTable($pdo);

$docStmt = $pdo->prepare(
    "SELECT DISTINCT DoctorID
     FROM appointments
     WHERE PatientID = :pid"
);
$docStmt->execute([':pid' => $test['PatientID']]);
$doctorIds = $docStmt->fetchAll(PDO::FETCH_COLUMN);

$labtechStmt = $pdo->prepare(
    "SELECT LabTechID
     FROM labtechs
     WHERE LabID = (SELECT LabID FROM lab_tests WHERE LabTestID = :tid)"
);
$labtechStmt->execute([':tid' => $testID]);
$labtechIds = $labtechStmt->fetchAll(PDO::FETCH_COLUMN);

$participantIds = array_values(array_filter(array_unique(array_merge($doctorIds, $labtechIds))));
$readsMap = getChatReads($pdo, 'labtest', (int)$testID, $participantIds);

function formatLastSeen($ts) {
    if (!$ts) return 'Never';
    return date('d-m-Y H:i', strtotime($ts));
}

function maxSeen($readsMap, $ids) {
    $max = null;
    foreach ($ids as $id) {
        if (!isset($readsMap[$id])) continue;
        $ts = $readsMap[$id];
        if ($max === null || strtotime($ts) > strtotime($max)) {
            $max = $ts;
        }
    }
    return $max;
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">
    <h2>Lab Test Chat</h2>
    <div class="note-box">
        <strong>Lab Test:</strong> <?= htmlspecialchars($test['TestName']) ?> |
        Patient: <?= htmlspecialchars($test['PatientName']) ?> |
        Lab: <?= htmlspecialchars($test['LabName']) ?>
        (<?= htmlspecialchars(formatEntityRatingLabel($labRatings[$test['LabID']] ?? null)) ?>)
        <div class="hint chat-last-seen">
            Last seen &mdash;
            Doctors: <?= htmlspecialchars(formatLastSeen(maxSeen($readsMap, $doctorIds))) ?>,
            Labtechs: <?= htmlspecialchars(formatLastSeen(maxSeen($readsMap, $labtechIds))) ?>
        </div>
    </div>

    <div id="chatBox" class="chat-messages"></div>

    <form id="chatForm" class="chat-compose-form">
        <input type="hidden" name="testid" value="<?= htmlspecialchars($testID) ?>">
        <textarea name="message" rows="3" class="message-input" data-capitalize="sentences" placeholder="Type your message..."></textarea>
        <input type="submit" value="Send" class="send-message-button">
    </form>
    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>
</div>
</main>

<script>
const chatBox = document.getElementById('chatBox');
const chatForm = document.getElementById('chatForm');

function fetchMessages() {
    const tid = "<?= htmlspecialchars($testID) ?>";
    fetch('labtech_labtest_fetch_messages.php?testid=' + tid)
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

setInterval(fetchMessages, 2000);
fetchMessages();

chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(chatForm);
    fetch('labtech_labtest_send_message.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(() => {
            chatForm.message.value = '';
            fetchMessages();
        });
});
</script>

<?php
include "../footer.php"; ?>








