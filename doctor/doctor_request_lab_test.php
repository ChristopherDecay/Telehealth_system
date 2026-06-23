<?php
// File overview: Handles doctor lab-test request creation with patient approval gate.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$appointmentID = trim($_POST['appid'] ?? '');
$labID = trim($_POST['lab_id'] ?? '');
$testName = trim($_POST['test_name'] ?? '');
$requestNote = trim($_POST['request_note'] ?? '');

if ($appointmentID === '' || $labID === '' || $testName === '') {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Lab, test name, and appointment are required.");
    exit;
}

/* Ensure workflow columns are available on older DBs. */
ensureLabTestApprovalColumns($pdo);
ensureLabTestResultDateColumn($pdo);

$apptStmt = $pdo->prepare(
    "SELECT AppID, PatientID
     FROM appointments
     WHERE AppID = :aid AND DoctorID = :did"
);
$apptStmt->execute([
    ':aid' => $appointmentID,
    ':did' => $doctorID
]);
$appt = $apptStmt->fetch(PDO::FETCH_ASSOC);
if (!$appt) {
    header("Location: doctor_appointments.php");
    exit;
}

$labStmt = $pdo->prepare(
    "SELECT LabID, LabName
     FROM laboratories
     WHERE LabID = :lid"
);
$labStmt->execute([':lid' => $labID]);
$lab = $labStmt->fetch(PDO::FETCH_ASSOC);
if (!$lab) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Selected lab does not exist.");
    exit;
}

$insert = $pdo->prepare(
    "INSERT INTO lab_tests
     (PatientID, LabID, TestName, TestDate, Status, Result, ResultDate, PatientApprovalStatus, RequestedByDoctorID, RequestNote)
     VALUES (:pid, :labid, :tname, NOW(), 'Pending', NULL, NULL, 'Pending', :docid, :note)"
);
$insert->execute([
    ':pid' => $appt['PatientID'],
    ':labid' => $labID,
    ':tname' => $testName,
    ':docid' => $doctorID,
    ':note' => $requestNote !== '' ? $requestNote : null
]);

addNotification(
    $pdo,
    $appt['PatientID'],
    'Patient',
    'Lab Test Approval Needed',
    "Your doctor requested '$testName' at {$lab['LabName']}. Please accept or reject.",
    "/Telehealth_system/patient/patient_lab_tests.php"
);

header("Location: doctor_view_appointment.php?id=$appointmentID&success=labreq");
exit;

