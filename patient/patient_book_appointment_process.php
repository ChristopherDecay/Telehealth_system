<?php
// File overview: Handles patient book appointment process functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$date      = trim($_POST['appointment_date'] ?? '');
$time      = trim($_POST['appointment_time'] ?? '');
$duration  = trim($_POST['duration_minutes'] ?? '');
$reasonText = trim($_POST['reason'] ?? '');

if ($date === '' || $time === '' || $duration === '' || $reasonText === '') {
    header("Location: patient_book_appointments.php?error=All fields are required");
    exit;
}

if (!isDateDmy($date) || !isTimeHm($time)) {
    header("Location: patient_book_appointments.php?error=Invalid date or time format");
    exit;
}
if (!isValidDurationMinutes($duration)) {
    header("Location: patient_book_appointments.php?error=Invalid duration. Use 20-120 minutes in 10-minute steps");
    exit;
}

$dateDb = dmyToYmd($date);
if ($dateDb === null) {
    header("Location: patient_book_appointments.php?error=Invalid date or time format");
    exit;
}
if ($dateDb < date('Y-m-d')) {
    header("Location: patient_book_appointments.php?error=Appointment date cannot be in the past");
    exit;
}

/* Combine date + time into DATETIME for AppointmentDate */
$appointmentDateTime = $dateDb . ' ' . $time . ':00';
$durationMinutes = (int)$duration;
$startDt = new DateTime($appointmentDateTime);
$endDt = (clone $startDt)->modify('+' . $durationMinutes . ' minutes');
$appointmentEnd = $endDt->format('Y-m-d H:i:s');
if (!isAtLeastLeadTimeFromNow($appointmentDateTime, 60)) {
    header("Location: patient_book_appointments.php?error=Appointment must be at least 1 hour from now");
    exit;
}
if (!isWithinHospitalHours($appointmentDateTime, $durationMinutes)) {
    header("Location: patient_book_appointments.php?error=Appointments are only allowed during hospital hours (08:00-17:00).");
    exit;
}

/* Prevent double booking for patient */
$check = $pdo->prepare(
    "SELECT COUNT(*) 
     FROM appointments
     WHERE PatientID = :pid
       AND Status IN ('Pending','Confirmed','AwaitingPatientApproval')
       AND NOT (
           DATE_ADD(AppointmentDate, INTERVAL DurationMinutes MINUTE) <= :new_start
           OR AppointmentDate >= :new_end
       )"
);

$check->execute([
    ':pid'  => $patientID,
    ':new_start' => $appointmentDateTime,
    ':new_end' => $appointmentEnd
]);

if ($check->fetchColumn() > 0) {
    header("Location: patient_book_appointments.php?error=You already have an appointment at this time");
    exit;
}

/* Auto-assign an available triage nurse with the lowest active load */
$nurseStmt = $pdo->prepare(
    "SELECT n.NurseID
     FROM nurses n
     LEFT JOIN appointments a_load
       ON a_load.NurseID = n.NurseID
      AND a_load.Status IN ('Pending','Confirmed','AwaitingPatientApproval')
     WHERE NOT EXISTS (
         SELECT 1
         FROM appointments a_busy
         WHERE a_busy.NurseID = n.NurseID
           AND a_busy.Status IN ('Pending','Confirmed','AwaitingPatientApproval')
           AND NOT (
               DATE_ADD(a_busy.AppointmentDate, INTERVAL a_busy.DurationMinutes MINUTE) <= :new_start
               OR a_busy.AppointmentDate >= :new_end
           )
     )
     GROUP BY n.NurseID
     ORDER BY COUNT(a_load.AppID) ASC, n.NurseID ASC
     LIMIT 1"
);
$nurseStmt->execute([
    ':new_start' => $appointmentDateTime,
    ':new_end' => $appointmentEnd
]);
$nurseID = $nurseStmt->fetchColumn() ?: null;

if (!$nurseID) {
    header("Location: patient_book_appointments.php?error=No triage nurse is available at that time. Please choose another slot");
    exit;
}

/* Insert appointment (Doctor assigned later by triage nurse) */
$stmt = $pdo->prepare(
    "INSERT INTO appointments
     (PatientID, DoctorID, NurseID, AppointmentDate, DurationMinutes, Status, ReasonCategory, ReasonText)
     VALUES (:pid, NULL, :nid, :appt, :dur, 'Pending', 'General', :rtext)"
);

$stmt->execute([
    ':pid'  => $patientID,
    ':nid'  => $nurseID,
    ':appt' => $appointmentDateTime,
    ':dur'  => $durationMinutes,
    ':rtext'=> $reasonText
]);

if ($nurseID) {
    addNotification(
        $pdo,
        $nurseID,
        'Nurse',
        'New Appointment Request',
        "A patient requested an appointment for $date $time ($durationMinutes min).",
        "/Telehealth_system/nurse/nurse_assign_doctor.php"
    );
}

header("Location: patient_my_appointments.php?success=booked");
exit;



