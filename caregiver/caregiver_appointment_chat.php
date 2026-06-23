<?php
// File overview: Handles caregiver appointment chat functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Caregiver','Patient','Doctor'])) {
    header("Location: ../login.php");
    exit;
}

$appointmentID = $_GET['aid'] ?? '';
if (!$appointmentID) {
    echo "Invalid appointment.";
    exit;
}

ensureCaregiverMessagesTable($pdo);

// Fetch appointment + names
$stmt = $pdo->prepare(
    "SELECT a.AppID, a.PatientID, a.DoctorID, a.NurseID,
            p.FName AS PatientName,
            up.Uname AS PatientUname,
            d.FName AS DoctorName,
            ud.Uname AS DoctorUname
     FROM appointments a
     LEFT JOIN patients p ON a.PatientID = p.PatientID
     LEFT JOIN users up ON a.PatientID = up.UserID
     LEFT JOIN doctors d ON a.DoctorID = d.DoctorID
     LEFT JOIN users ud ON a.DoctorID = ud.UserID
     WHERE a.AppID = :aid"
);
$stmt->execute([':aid' => $appointmentID]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appt) {
    echo "Appointment not found.";
    exit;
}

$userID = $_SESSION['user_id'];
$role = $_SESSION['role'];
$backUrl = 'caregiver_chats.php';
if ($role === 'Patient') {
    $backUrl = '../patient/patient_my_appointments.php';
} elseif ($role === 'Doctor') {
    $backUrl = '../doctor/doctor_appointments.php';
}

// Access rules
$allowed = false;
if ($role === 'Patient' && $appt['PatientID'] === $userID) {
    $allowed = true;
} elseif ($role === 'Doctor' && $appt['DoctorID'] === $userID) {
    $allowed = true;
} elseif ($role === 'Caregiver') {
    $check = $pdo->prepare(
        "SELECT 1
         FROM caregiver_patients
         WHERE CaregiverID = :cid
           AND PatientID = :pid
           AND Status = 'Accepted'"
    );
    $check->execute([
        ':cid' => $userID,
        ':pid' => $appt['PatientID']
    ]);
    if ($check->fetchColumn()) {
        $allowed = true;
    }
}

if (!$allowed) {
    echo "Access denied.";
    exit;
}
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');

ensureChatReadsTable($pdo);
$careStmt = $pdo->prepare(
    "SELECT CaregiverID
     FROM caregiver_patients
     WHERE PatientID = :pid AND Status = 'Accepted'"
);
$careStmt->execute([':pid' => $appt['PatientID']]);
$caregiverIds = $careStmt->fetchAll(PDO::FETCH_COLUMN);
$participantIds = array_values(array_filter(array_merge(
    [$appt['PatientID'], $appt['DoctorID']],
    $caregiverIds
)));
$readsMap = getChatReads($pdo, 'caregiver', (int)$appointmentID, $participantIds);

function formatLastSeen($ts) {
    if (!$ts) return 'Never';
    return date('d-m-Y H:i', strtotime($ts));
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">
    <h2>Caregiver Appointment Chat</h2>
    <div class="note-box">
        <strong>Participants:</strong>
        Patient: <?= htmlspecialchars($appt['PatientName'] ?: ($appt['PatientUname'] ?: 'Patient')) ?> |
        Doctor: <?= htmlspecialchars($appt['DoctorName'] ?: ($appt['DoctorUname'] ?: 'Pending')) ?>
        <?php if (!empty($appt['DoctorID'])): ?>
            (<?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$appt['DoctorID']] ?? null)) ?>)
        <?php endif; ?>
        <div class="hint chat-last-seen">
            Last seen &mdash;
            Patient: <?= htmlspecialchars(formatLastSeen($readsMap[$appt['PatientID']] ?? null)) ?>,
            Doctor: <?= htmlspecialchars(formatLastSeen($readsMap[$appt['DoctorID']] ?? null)) ?>,
            Caregivers: <?= count($caregiverIds) ?>
        </div>
    </div>

    <div id="chatBox" class="chat-messages"></div>

    <form id="chatForm" class="chat-compose-form">
        <input type="hidden" name="appointmentID" value="<?= htmlspecialchars($appointmentID) ?>">
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
    const aid = "<?= htmlspecialchars($appointmentID) ?>";
    fetch('caregiver_fetch_messages.php?aid=' + aid)
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
    fetch('caregiver_send_message.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(() => {
            chatForm.message.value = '';
            fetchMessages();
        });
});
</script>

<?php
include "../footer.php"; ?>





