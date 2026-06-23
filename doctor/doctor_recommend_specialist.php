<?php
// File overview: Handles doctor recommend specialist functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$appointmentID = trim($_POST['appid'] ?? '');
$specialization = trim($_POST['specialization'] ?? '');
$date = trim($_POST['appointment_date'] ?? '');
$time = trim($_POST['appointment_time'] ?? '');
$duration = trim($_POST['duration_minutes'] ?? '');

if ($appointmentID === '' || $specialization === '' || $date === '' || $time === '' || $duration === '') {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=All fields are required for specialist referral.");
    exit;
}

if (!isDateDmy($date) || !isTimeHm($time)) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Invalid date or time format.");
    exit;
}
if (!isValidDurationMinutes($duration)) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Invalid duration. Use 20-120 minutes in 10-minute steps.");
    exit;
}

$dateDb = dmyToYmd($date);
if ($dateDb === null) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Invalid date or time format.");
    exit;
}
if ($dateDb < date('Y-m-d')) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Appointment date cannot be in the past.");
    exit;
}

$appointmentDateTime = $dateDb . ' ' . $time . ':00';
$durationMinutes = (int)$duration;
$startDt = new DateTime($appointmentDateTime);
$endDt = (clone $startDt)->modify('+' . $durationMinutes . ' minutes');
$appointmentEnd = $endDt->format('Y-m-d H:i:s');
if (!isAtLeastLeadTimeFromNow($appointmentDateTime, 60)) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Appointment must be at least 1 hour from now.");
    exit;
}
if (!isWithinHospitalHours($appointmentDateTime, $durationMinutes)) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Appointments are only allowed during hospital hours (08:00-17:00).");
    exit;
}

/* Ensure the appointment belongs to this doctor */
$apptStmt = $pdo->prepare(
    "SELECT PatientID, NurseID, AppointmentDate
     FROM appointments
     WHERE AppID = :aid AND DoctorID = :doc"
);
$apptStmt->execute([
    ':aid' => $appointmentID,
    ':doc' => $doctorID
]);
$appt = $apptStmt->fetch(PDO::FETCH_ASSOC);
if (!$appt) {
    header("Location: doctor_appointments.php");
    exit;
}

/* Verify specialist specialization exists in system */
$specExistsStmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM doctors
     WHERE Specialization = :spec
       AND Specialization NOT IN ('General Practitioner','Family Medicine')"
);
$specExistsStmt->execute([':spec' => $specialization]);
if ((int)$specExistsStmt->fetchColumn() <= 0) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=No doctor exists in the selected specialization.");
    exit;
}

/* Ensure referral has a nurse queue owner */
$nurseID = $appt['NurseID'] ?? null;
if (empty($nurseID)) {
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
}

if (empty($nurseID)) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=No nurse is available to process this referral.");
    exit;
}

/* Find doctors in requested specialization */
$specDoctorsStmt = $pdo->prepare(
    "SELECT DoctorID
     FROM doctors
     WHERE Specialization = :spec
     ORDER BY DoctorID ASC"
);
$specDoctorsStmt->execute([':spec' => $specialization]);
$specialistDoctorIDs = $specDoctorsStmt->fetchAll(PDO::FETCH_COLUMN);

/* Prevent patient overlap */
$checkPatient = $pdo->prepare(
    "SELECT COUNT(*)
     FROM appointments
     WHERE PatientID = :pid
       AND Status IN ('Pending','Confirmed','AwaitingPatientApproval')
       AND NOT (
           DATE_ADD(AppointmentDate, INTERVAL DurationMinutes MINUTE) <= :new_start
           OR AppointmentDate >= :new_end
       )"
);
$checkPatient->execute([
    ':pid' => $appt['PatientID'],
    ':new_start' => $appointmentDateTime,
    ':new_end' => $appointmentEnd
]);
if ($checkPatient->fetchColumn() > 0) {
    header("Location: doctor_view_appointment.php?id=$appointmentID&error=Patient is already booked for this time.");
    exit;
}

/* Auto-assign when exactly one specialist exists and is available */
$assignedDoctorID = null;
$status = 'Pending';
if (count($specialistDoctorIDs) === 1) {
    $candidateDoctorID = (string)$specialistDoctorIDs[0];
    $checkDoctor = $pdo->prepare(
        "SELECT COUNT(*)
         FROM appointments
         WHERE DoctorID = :doc
           AND Status IN ('Pending','Confirmed','AwaitingPatientApproval')
           AND NOT (
               DATE_ADD(AppointmentDate, INTERVAL DurationMinutes MINUTE) <= :new_start
               OR AppointmentDate >= :new_end
           )"
    );
    $checkDoctor->execute([
        ':doc' => $candidateDoctorID,
        ':new_start' => $appointmentDateTime,
        ':new_end' => $appointmentEnd
    ]);

    if ((int)$checkDoctor->fetchColumn() === 0) {
        $assignedDoctorID = $candidateDoctorID;
        $status = 'AwaitingPatientApproval';
    }
}

$reasonText = "Specialist referral requested by doctor.";

$insert = $pdo->prepare(
    "INSERT INTO appointments
     (PatientID, DoctorID, NurseID, AppointmentDate, DurationMinutes, Status, ReasonCategory, ReasonText)
     VALUES (:pid, :doc, :nid, :appt, :dur, :status, :rcat, :rtext)"
);
$insert->execute([
    ':pid' => $appt['PatientID'],
    ':doc' => $assignedDoctorID,
    ':nid' => $nurseID,
    ':appt' => $appointmentDateTime,
    ':dur' => $durationMinutes,
    ':status' => $status,
    ':rcat' => $specialization,
    ':rtext' => $reasonText
]);

if ($assignedDoctorID !== null) {
    addNotification(
        $pdo,
        $appt['PatientID'],
        'Patient',
        'Specialist Approval Needed',
        "A specialist referral was prepared for $date $time ($durationMinutes min). Please approve it.",
        "/Telehealth_system/patient/patient_my_appointments.php"
    );
    addNotification(
        $pdo,
        $nurseID,
        'Nurse',
        'Specialist Auto-Assigned',
        "A specialist referral was auto-assigned and is waiting for patient approval.",
        "/Telehealth_system/nurse/nurse_assign_doctor.php"
    );
} else {
    addNotification(
        $pdo,
        $nurseID,
        'Nurse',
        'Specialist Referral Needs Assignment',
        "A specialist referral for $specialization was requested for $date $time ($durationMinutes min).",
        "/Telehealth_system/nurse/nurse_assign_doctor.php"
    );
}

header("Location: doctor_view_appointment.php?id=$appointmentID&success=1");
exit;



