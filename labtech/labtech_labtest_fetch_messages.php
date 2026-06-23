<?php
// File overview: Handles labtech labtest fetch messages functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Labtech','Doctor'])) {
    exit('Unauthorized');
}

$testID = $_GET['testid'] ?? '';
if ($testID === '') exit('Invalid');

ensureLabTestMessagesTable($pdo);
ensureLabTestApprovalColumns($pdo);

$stmt = $pdo->prepare(
    "SELECT lt.LabTestID, lt.PatientID, lt.LabID
     FROM lab_tests lt
     WHERE lt.LabTestID = :tid
       AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'"
);
$stmt->execute([':tid' => $testID]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$test) exit('Unauthorized');

$userID = $_SESSION['user_id'];
$role = $_SESSION['role'];
$allowed = false;

if ($role === 'Labtech') {
    $check = $pdo->prepare(
        "SELECT 1
         FROM labtechs
         WHERE LabTechID = :lid AND LabID = :labid"
    );
    $check->execute([
        ':lid' => $userID,
        ':labid' => $test['LabID']
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

if (!$allowed) exit('Unauthorized');

ensureChatReadsTable($pdo);
upsertChatRead($pdo, 'labtest', (int)$testID, $_SESSION['user_id'], $_SESSION['role']);

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
     WHERE LabID = :labid"
);
$labtechStmt->execute([':labid' => $test['LabID']]);
$labtechIds = $labtechStmt->fetchAll(PDO::FETCH_COLUMN);

$participantIds = array_values(array_filter(array_unique(array_merge($doctorIds, $labtechIds))));
$readsMap = getChatReads($pdo, 'labtest', (int)$testID, $participantIds);

$stmt = $pdo->prepare(
    "SELECT m.*, u.Uname
     FROM lab_test_messages m
     JOIN users u ON m.SenderID = u.UserID
     WHERE m.LabTestID = :tid
     ORDER BY m.MsgDate ASC"
);
$stmt->execute([':tid' => $testID]);
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
