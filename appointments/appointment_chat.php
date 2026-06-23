<?php
// File overview: Handles appointment chat functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only allow doctors, patients, and nurses
if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Doctor','Patient','Nurse'])) {
    header("Location: ../login.php");
    exit;
}

// Get appointment ID from query string and fetch chat context (participants, lab info, appointment details).
$appointmentID = $_GET['aid'] ?? '';
$ctx = getAppointmentChatContext($pdo, $appointmentID, $_SESSION['user_id'], $_SESSION['role']);
if (!$ctx['allowed']) {
    echo "Appointment not found or access denied.";
    exit;
}
$participants = $ctx['participants'];
$labInfo = $ctx['lab'];
$appt = $ctx['appointment'];
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$labRatings = getEntityRatingsMap($pdo, 'Lab');
$isDoctor = $_SESSION['role'] === 'Doctor';
$role = $_SESSION['role'];
$backUrl = '../doctor/doctor_appointments.php';
if ($role === 'Patient') {
    $backUrl = '../patient/patient_my_appointments.php';
} elseif ($role === 'Nurse') {
    $backUrl = '../nurse/nurse_appointments.php';
}

$sessionDraft = [
    'Diagnosis' => '',
    'Prescription' => '',
    'FutureCare' => '',
    'FollowupDate' => ''
];
if ($isDoctor) {
    $sessionStmt = $pdo->prepare(
        "SELECT Diagnosis, Prescription, FutureCare, SpecialistRecommended, FollowupDate
         FROM sessions
         WHERE AppID = :aid AND DoctorID = :doc
         LIMIT 1"
    );
    $sessionStmt->execute([
        ':aid' => $appointmentID,
        ':doc' => $_SESSION['user_id']
    ]);
    $sessionRow = $sessionStmt->fetch(PDO::FETCH_ASSOC);
    if ($sessionRow) {
        $sessionDraft['Diagnosis'] = $sessionRow['Diagnosis'] ?? '';
        $sessionDraft['Prescription'] = $sessionRow['Prescription'] ?? '';
        $sessionDraft['FutureCare'] = $sessionRow['FutureCare'] ?? '';
        $sessionDraft['FollowupDate'] = isset($sessionRow['FollowupDate']) ? (ymdToDmy($sessionRow['FollowupDate']) ?? '') : '';
    }
}

$flashError = trim($_GET['error'] ?? '');
$saved = isset($_GET['saved']);

ensureChatReadsTable($pdo);
$participantIds = array_values(array_filter([
    $appt['PatientID'],
    $appt['DoctorID'],
    $appt['NurseID']
]));
$readsMap = getChatReads($pdo, 'appointment', (int)$appointmentID, $participantIds);

function formatLastSeen($ts) {
    if (!$ts) return 'Never';
    return date('d-m-Y H:i', strtotime($ts));
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">
    <h2>Appointment Chat</h2>
    <?php
if ($flashError !== ''): ?>
        <div class="error"><?= htmlspecialchars($flashError) ?></div>
    <?php
elseif ($saved): ?>
        <div class="success">Clinical summary saved and session completed.</div>
    <?php
endif; ?>
    <div class="note-box">
        <strong>Participants:</strong>
        Patient: <?= htmlspecialchars($participants['Patient']) ?> |
        Doctor: <?= htmlspecialchars($participants['Doctor']) ?>
        <?php if (!empty($appt['DoctorID'])): ?>
            (<?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$appt['DoctorID']] ?? null)) ?>)
        <?php endif; ?> |
        Nurse: <?= htmlspecialchars($participants['Nurse']) ?>
        <?php
if ($labInfo): ?>
            | Lab: <?= htmlspecialchars($labInfo['LabName']) ?>
            (<?= htmlspecialchars(formatEntityRatingLabel($labRatings[$labInfo['LabID']] ?? null)) ?>)
        <?php
endif; ?>
        <div class="hint chat-last-seen">
            Last seen &mdash;
            Patient: <?= htmlspecialchars(formatLastSeen($readsMap[$appt['PatientID']] ?? null)) ?>,
            Doctor: <?= htmlspecialchars(formatLastSeen($readsMap[$appt['DoctorID']] ?? null)) ?>,
            Nurse: <?= htmlspecialchars(formatLastSeen($readsMap[$appt['NurseID']] ?? null)) ?>
        </div>
    </div>

    <div id="chatBox" class="chat-messages"></div>

    <form id="chatForm" class="chat-compose-form">
        <input type="hidden" name="appointmentID" value="<?= $appointmentID ?>">
        <textarea name="message" rows="3" class="message-input" data-capitalize="sentences" placeholder="Type your message..."></textarea>
        <input type="submit" value="Send" class="send-message-button">
    </form>

    <?php
if ($isDoctor): ?>
        <hr>
        <h3>End Session & Save Clinical Summary</h3>
        <form method="post" action="../doctor/doctor_save_prescription.php">
            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars((string)$appointmentID) ?>">

            <label>Diagnosis</label>
            <textarea name="diagnosis" rows="3" data-capitalize="sentences" placeholder="Final diagnosis for this session..."><?= htmlspecialchars($sessionDraft['Diagnosis']) ?></textarea>

            <label>Prescription (optional)</label>
            <textarea name="prescription" rows="4" data-capitalize="sentences" placeholder="Medicine and dosage instructions..."><?= htmlspecialchars($sessionDraft['Prescription']) ?></textarea>

            <label>Follow-up Date (optional, DD-MM-YYYY)</label>
            <input type="text" name="followup_date" value="<?= htmlspecialchars($sessionDraft['FollowupDate']) ?>" placeholder="DD-MM-YYYY">

            <label>Future Care Notes (optional)</label>
            <textarea name="future_care" rows="3" data-capitalize="sentences" placeholder="Follow-up plan and care instructions..."><?= htmlspecialchars($sessionDraft['FutureCare']) ?></textarea>

            <div class="inline-action-group summary-action-row">
                <input type="submit" class="btn btn-approve" value="Save Summary & End Session"
                       onclick="return confirm('Save diagnosis/care plan and mark the appointment as completed?');">
                <a href="../doctor/doctor_view_appointment.php?id=<?= urlencode((string)$appointmentID) ?>#request-actions"
                   class="btn btn-approve">Request Specialist/Labtest</a>
            </div>
        </form>
    <?php
endif; ?>
    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>
</div>
</main>

<script>
const chatBox = document.getElementById('chatBox');
const chatForm = document.getElementById('chatForm');

function fetchMessages() {
    const aid = "<?= $appointmentID ?>";
    fetch('fetch_messages.php?aid=' + aid)
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Poll every 2 seconds
setInterval(fetchMessages, 2000);
fetchMessages(); // initial load

chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(chatForm);

    fetch('send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        chatForm.message.value = '';
        fetchMessages();
    });
});
</script>

<?php
include "../footer.php"; ?>




