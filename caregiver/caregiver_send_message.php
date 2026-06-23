<?php
// File overview: Handles caregiver send message functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Caregiver','Patient','Doctor'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid');
}

$appointmentID = $_POST['appointmentID'] ?? '';
$message = trim($_POST['message'] ?? '');

if ($appointmentID === '' || $message === '') {
    exit('Invalid');
}

ensureCaregiverMessagesTable($pdo);

$stmt = $pdo->prepare(
    "SELECT a.AppID, a.PatientID, a.DoctorID
     FROM appointments a
     WHERE a.AppID = :aid"
);
$stmt->execute([':aid' => $appointmentID]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$appt) exit('Unauthorized');

$userID = $_SESSION['user_id'];
$role = $_SESSION['role'];
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

if (!$allowed) exit('Unauthorized');

$stmt = $pdo->prepare(
    "INSERT INTO caregiver_messages (AppID, SenderRole, SenderID, Message)
     VALUES (:aid, :role, :sid, :msg)"
);
$stmt->execute([
    ':aid' => $appointmentID,
    ':role' => $role,
    ':sid' => $userID,
    ':msg' => $message
]);

// Notify patient, doctor, and other caregivers
$recipients = [];
if (!empty($appt['PatientID'])) {
    $recipients[] = ['id' => $appt['PatientID'], 'role' => 'Patient'];
}
if (!empty($appt['DoctorID'])) {
    $recipients[] = ['id' => $appt['DoctorID'], 'role' => 'Doctor'];
}
$careStmt = $pdo->prepare(
    "SELECT CaregiverID
     FROM caregiver_patients
     WHERE PatientID = :pid AND Status = 'Accepted'"
);
$careStmt->execute([':pid' => $appt['PatientID']]);
$caregiverIds = $careStmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($caregiverIds as $cid) {
    $recipients[] = ['id' => $cid, 'role' => 'Caregiver'];
}

foreach ($recipients as $r) {
    if ($r['id'] === $userID) continue;
    addNotification(
        $pdo,
        $r['id'],
        $r['role'],
        'New Caregiver Chat Message',
        'You received a new caregiver chat message.',
        "/Telehealth_system/caregiver/caregiver_appointment_chat.php?aid=$appointmentID"
    );
}

echo 'Sent';
