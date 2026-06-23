<?php
// File overview: Handles patient accept/reject actions for doctor lab-test requests.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$labTestID = trim($_POST['testid'] ?? '');
$decision = trim($_POST['decision'] ?? '');

if ($labTestID === '' || !in_array($decision, ['accept', 'reject'], true)) {
    header("Location: patient_lab_tests.php?error=Invalid lab-test action.");
    exit;
}

ensureLabTestApprovalColumns($pdo);

$stmt = $pdo->prepare(
    "SELECT LabTestID, PatientID, LabID, TestName, PatientApprovalStatus, RequestedByDoctorID
     FROM lab_tests
     WHERE LabTestID = :tid
       AND PatientID = :pid"
);
$stmt->execute([
    ':tid' => $labTestID,
    ':pid' => $patientID
]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$test) {
    header("Location: patient_lab_tests.php?error=Lab test not found.");
    exit;
}

$approvalState = (string)($test['PatientApprovalStatus'] ?? 'Accepted');
if ($approvalState !== 'Pending') {
    header("Location: patient_lab_tests.php?error=This lab test is not waiting for your decision.");
    exit;
}

if ($decision === 'accept') {
    $upd = $pdo->prepare(
        "UPDATE lab_tests
         SET PatientApprovalStatus = 'Accepted'
         WHERE LabTestID = :tid
           AND PatientID = :pid"
    );
    $upd->execute([
        ':tid' => $labTestID,
        ':pid' => $patientID
    ]);

    $labtechStmt = $pdo->prepare(
        "SELECT LabTechID
         FROM labtechs
         WHERE LabID = :labid"
    );
    $labtechStmt->execute([':labid' => $test['LabID']]);
    $labtechIDs = $labtechStmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($labtechIDs as $labtechID) {
        addNotification(
            $pdo,
            $labtechID,
            'Labtech',
            'New Lab Test Request',
            "A patient approved '{$test['TestName']}' and it is ready for processing.",
            "/Telehealth_system/labtech/labtech_lab_tests.php"
        );
    }

    if (!empty($test['RequestedByDoctorID'])) {
        addNotification(
            $pdo,
            $test['RequestedByDoctorID'],
            'Doctor',
            'Lab Test Approved',
            "Your patient approved lab test request '{$test['TestName']}'.",
            "/Telehealth_system/doctor/doctor_lab_results.php"
        );
    }

    header("Location: patient_lab_tests.php?success=approved");
    exit;
}

$upd = $pdo->prepare(
    "UPDATE lab_tests
     SET PatientApprovalStatus = 'Rejected'
     WHERE LabTestID = :tid
       AND PatientID = :pid"
);
$upd->execute([
    ':tid' => $labTestID,
    ':pid' => $patientID
]);

if (!empty($test['RequestedByDoctorID'])) {
    addNotification(
        $pdo,
        $test['RequestedByDoctorID'],
        'Doctor',
        'Lab Test Rejected',
        "Your patient rejected lab test request '{$test['TestName']}'.",
        "/Telehealth_system/doctor/doctor_lab_results.php"
    );
}

header("Location: patient_lab_tests.php?success=rejected");
exit;

