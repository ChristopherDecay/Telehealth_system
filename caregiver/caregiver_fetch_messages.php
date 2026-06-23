<?php
// File overview: Handles caregiver fetch messages functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Caregiver','Patient','Doctor'])) {
    exit('Unauthorized');
}

$appointmentID = $_GET['aid'] ?? '';
if ($appointmentID === '') exit('Invalid');

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

ensureChatReadsTable($pdo);
upsertChatRead($pdo, 'caregiver', (int)$appointmentID, $_SESSION['user_id'], $_SESSION['role']);

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

$stmt = $pdo->prepare(
    "SELECT m.*, u.Uname
     FROM caregiver_messages m
     JOIN users u ON m.SenderID = u.UserID
     WHERE m.AppID = :aid
     ORDER BY m.MsgDate ASC"
);
$stmt->execute([':aid' => $appointmentID]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $msg) {
    $align = ($_SESSION['user_id'] === $msg['SenderID']) ? 'right' : 'left';
    echo '<div style="text-align:'.$align.'; margin-bottom:8px;">';
    echo '<strong>'.htmlspecialchars($msg['Uname']).':</strong> ';
    echo nl2br(htmlspecialchars($msg['Message']));
    echo '<br><small style="font-size:0.8em; color:#555;">'.$msg['MsgDate'].'</small>';
    if ($_SESSION['user_id'] === $msg['SenderID']) {
        $seenByOther = false;
        foreach ($participantIds as $pid) {
            if ($pid === $_SESSION['user_id']) continue;
            $lastRead = $readsMap[$pid] ?? null;
            if ($lastRead && strtotime($lastRead) >= strtotime($msg['MsgDate'])) {
                $seenByOther = true;
                break;
            }
        }
        if ($seenByOther) {
            echo '<div style="font-size:0.75em; color:#2f855a;">Seen</div>';
        }
    }
    echo '</div>';
}
