<?php
// File overview: Handles doctor complete appointment functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$appointmentID = $_POST['appid'] ?? '';
if ($appointmentID !== '') {
    $stmt = $pdo->prepare(
        "SELECT PatientID, AppointmentDate
         FROM appointments
         WHERE AppID = :id
           AND DoctorID = :doc"
    );
    $stmt->execute([
        ':id' => $appointmentID,
        ':doc' => $doctorID
    ]);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($appt) {
        $update = $pdo->prepare(
            "UPDATE appointments
             SET Status = 'Completed'
             WHERE AppID = :id
               AND DoctorID = :doc"
        );
        $update->execute([
            ':id' => $appointmentID,
            ':doc' => $doctorID
        ]);

        $dateDisplay = date('d-m-Y H:i', strtotime($appt['AppointmentDate']));
        addNotification(
            $pdo,
            $appt['PatientID'],
            'Patient',
            'Appointment Completed',
            "Your appointment on $dateDisplay was completed.",
            "/Telehealth_system/patient/patient_my_appointments.php"
        );
    }
}

header("Location: doctor_appointments.php");
exit;



