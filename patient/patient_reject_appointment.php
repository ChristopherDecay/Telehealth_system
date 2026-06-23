<?php
// File overview: Handles patient reject/reassign appointment functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$appointmentID = $_POST['appid'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if ($appointmentID === '') {
    header("Location: patient_my_appointments.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT AppID, AppointmentDate, DurationMinutes, DoctorID, NurseID, Status
     FROM appointments
     WHERE AppID = :aid
       AND PatientID = :pid
       AND Status NOT IN ('Completed','Cancelled')"
);
$stmt->execute([
    ':aid' => $appointmentID,
    ':pid' => $patientID
]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appt) {
    header("Location: patient_my_appointments.php?error=Appointment cannot be rejected at this stage.");
    exit;
}

if (empty($appt['DoctorID'])) {
    header("Location: patient_my_appointments.php?error=No doctor is assigned yet for this appointment.");
    exit;
}

$apptTime = new DateTime($appt['AppointmentDate']);
$now = new DateTime();
$limit = (clone $now)->modify('+1 hour');
if ($apptTime <= $limit) {
    header("Location: patient_my_appointments.php?error=Cannot reject within 1 hour of start time.");
    exit;
}

$durationMinutes = max(20, (int)($appt['DurationMinutes'] ?? 20));
$endDt = (clone $apptTime)->modify('+' . $durationMinutes . ' minutes');
$appointmentDateTime = $apptTime->format('Y-m-d H:i:s');
$appointmentEnd = $endDt->format('Y-m-d H:i:s');
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

    if (empty($nurseID)) {
        header("Location: patient_my_appointments.php?error=No triage nurse is available for reassignment at this time.");
        exit;
    }
}

$reasonText = $reason === '' ? 'Patient requested reassignment.' : $reason;
$update = $pdo->prepare(
    "UPDATE appointments
     SET DoctorID = NULL,
         NurseID = :nid,
         Status = 'Pending',
         DoctorRejectedAt = NOW(),
         DoctorRejectionReason = :reason
     WHERE AppID = :aid
       AND PatientID = :pid"
);
$update->execute([
    ':nid' => $nurseID,
    ':reason' => $reasonText,
    ':aid' => $appointmentID,
    ':pid' => $patientID
]);

$dateDisplay = date('d-m-Y H:i', strtotime($appt['AppointmentDate']));
addNotification(
    $pdo,
    $patientID,
    'Patient',
    'Reassignment Requested',
    "Your appointment scheduled for $dateDisplay has been sent for reassignment.",
    "/Telehealth_system/patient/patient_my_appointments.php"
);
addNotification(
    $pdo,
    $nurseID,
    'Nurse',
    'Appointment Needs Reassignment',
    "A patient requested reassignment for an appointment scheduled for $dateDisplay.",
    "/Telehealth_system/nurse/nurse_assign_doctor.php"
);
addNotification(
    $pdo,
    $appt['DoctorID'],
    'Doctor',
    'Appointment Reassigned',
    "A patient requested reassignment for an appointment scheduled for $dateDisplay.",
    "/Telehealth_system/doctor/doctor_appointments.php"
);

header("Location: patient_my_appointments.php?success=1");
exit;
