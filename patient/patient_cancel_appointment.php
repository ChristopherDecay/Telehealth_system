<?php
// File overview: Handles patient cancel appointment functionality.
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$appointmentID = $_POST['appointment_id'] ?? '';
$patientID = $_SESSION['user_id'];

if ($appointmentID === '') {
    header("Location: patient_my_appointments.php");
    exit;
}

/* Cancel only patient's own pending appointment */
$stmt = $pdo->prepare(
    "UPDATE appointments
     SET Status = 'Cancelled'
     WHERE AppID = :aid
       AND PatientID = :pid
       AND Status = 'Pending'"
);

$stmt->execute([
    ':aid' => $appointmentID,
    ':pid' => $patientID
]);

header("Location: patient_my_appointments.php");
exit;



