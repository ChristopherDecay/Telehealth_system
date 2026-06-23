<?php
// File overview: Handles fetch messages functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Doctor','Patient','Nurse'])) {
    exit('Unauthorized');
}

$appointmentID = $_GET['aid'] ?? '';
if (!$appointmentID) exit('Invalid');

$ctx = getAppointmentChatContext($pdo, $appointmentID, $_SESSION['user_id'], $_SESSION['role']);
if (!$ctx['allowed']) {
    exit('Unauthorized');
}

ensureChatReadsTable($pdo);
upsertChatRead($pdo, 'appointment', (int)$appointmentID, $_SESSION['user_id'], $_SESSION['role']);

$appt = $ctx['appointment'];
$participantIds = array_values(array_filter([
    $appt['PatientID'],
    $appt['DoctorID'],
    $appt['NurseID']
]));
$readsMap = getChatReads($pdo, 'appointment', (int)$appointmentID, $participantIds);

$stmt = $pdo->prepare("
    SELECT m.*, u.Uname
    FROM messages m
    JOIN users u ON m.SenderID = u.UserID
    WHERE m.AppID = :aid
    ORDER BY m.MsgDate ASC
");
$stmt->execute([':aid'=>$appointmentID]);
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
