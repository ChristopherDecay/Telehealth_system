<?php
// File overview: Handles labtech labtest send message functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Labtech','Doctor'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid');
}

$testID = $_POST['testid'] ?? '';
$message = trim($_POST['message'] ?? '');

if ($testID === '' || $message === '') {
    exit('Invalid');
}

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

$stmt = $pdo->prepare(
    "INSERT INTO lab_test_messages (LabTestID, SenderRole, SenderID, Message)
     VALUES (:tid, :role, :sid, :msg)"
);
$stmt->execute([
    ':tid' => $testID,
    ':role' => $role,
    ':sid' => $userID,
    ':msg' => $message
]);

// Notify doctors and labtechs on this test
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

$recipients = [];
foreach ($doctorIds as $did) {
    $recipients[] = ['id' => $did, 'role' => 'Doctor'];
}
foreach ($labtechIds as $lid) {
    $recipients[] = ['id' => $lid, 'role' => 'Labtech'];
}

foreach ($recipients as $r) {
    if ($r['id'] === $userID) continue;
    addNotification(
        $pdo,
        $r['id'],
        $r['role'],
        'New Lab Test Chat Message',
        'You received a new lab test chat message.',
        "/Telehealth_system/labtech/labtech_labtest_chat.php?testid=$testID"
    );
}

echo 'Sent';



