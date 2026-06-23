<?php
// File overview: Handles patient approval of specialist appointments.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$appointmentID = trim($_POST['appid'] ?? '');

if ($appointmentID === '') {
    header("Location: patient_my_appointments.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT AppID, DoctorID, AppointmentDate, DurationMinutes, Status
     FROM appointments
     WHERE AppID = :aid
       AND PatientID = :pid"
);
$stmt->execute([
    ':aid' => $appointmentID,
    ':pid' => $patientID
]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appt || (string)$appt['Status'] !== 'AwaitingPatientApproval' || empty($appt['DoctorID'])) {
    header("Location: patient_my_appointments.php?error=Appointment is not awaiting approval.");
    exit;
}

$update = $pdo->prepare(
    "UPDATE appointments
     SET Status = 'Pending'
     WHERE AppID = :aid
       AND PatientID = :pid
       AND Status = 'AwaitingPatientApproval'"
);
$update->execute([
    ':aid' => $appointmentID,
    ':pid' => $patientID
]);

$dateDisplay = date('d-m-Y H:i', strtotime($appt['AppointmentDate']));
$durationValue = (int)($appt['DurationMinutes'] ?? 20);

addNotification(
    $pdo,
    $appt['DoctorID'],
    'Doctor',
    'Specialist Appointment Approved',
    "A patient approved the specialist appointment on $dateDisplay ($durationValue min).",
    "/Telehealth_system/doctor/doctor_appointments.php"
);

addNotification(
    $pdo,
    $patientID,
    'Patient',
    'Appointment Approved',
    "You approved your specialist appointment on $dateDisplay.",
    "/Telehealth_system/patient/patient_my_appointments.php"
);

header("Location: patient_my_appointments.php?success=approved");
exit;

