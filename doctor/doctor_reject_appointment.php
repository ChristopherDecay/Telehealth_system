<?php
// File overview: Handles doctor reject appointment functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$appointmentID = $_POST['appid'] ?? '';
$reason = trim($_POST['reason'] ?? '');
$returnToView = isset($_POST['return_to_view']);
$returnUrl = $returnToView && $appointmentID !== ''
    ? "doctor_view_appointment.php?id=" . urlencode($appointmentID)
    : "doctor_appointments.php";
$returnSep = strpos($returnUrl, '?') === false ? '?' : '&';

if ($appointmentID === '') {
    header("Location: doctor_appointments.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT AppID, AppointmentDate, DurationMinutes, PatientID, NurseID, Status
     FROM appointments
     WHERE AppID = :aid
       AND DoctorID = :doc
       AND Status NOT IN ('Completed','Cancelled')"
);
$stmt->execute([
    ':aid' => $appointmentID,
    ':doc' => $doctorID
]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$appt) {
    header("Location: " . $returnUrl . $returnSep . "error=Appointment cannot be rejected at this stage.");
    exit;
}

$apptTime = new DateTime($appt['AppointmentDate']);
$now = new DateTime();
$limit = (clone $now)->modify('+1 hour');
if ($apptTime <= $limit) {
    header("Location: " . $returnUrl . $returnSep . "error=Cannot reject within 1 hour of start time.");
    exit;
}

$durationMinutes = max(20, (int)($appt['DurationMinutes'] ?? 20));
$endDt = (clone $apptTime)->modify('+' . $durationMinutes . ' minutes');
$appointmentDateTime = $apptTime->format('Y-m-d H:i:s');
$appointmentEnd = $endDt->format('Y-m-d H:i:s');

/* Ensure the appointment has a nurse to handle reassignment. */
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
        header("Location: " . $returnUrl . $returnSep . "error=No triage nurse is available for reassignment at this time.");
        exit;
    }
}

$update = $pdo->prepare(
    "UPDATE appointments
     SET DoctorID = NULL,
         NurseID = :nid,
         Status = 'Pending',
         DoctorRejectedAt = NOW(),
         DoctorRejectionReason = :reason
     WHERE AppID = :aid"
);
$update->execute([
    ':nid' => $nurseID,
    ':reason' => $reason === '' ? null : $reason,
    ':aid' => $appointmentID
]);

$dateDisplay = date('d-m-Y H:i', strtotime($appt['AppointmentDate']));
addNotification(
    $pdo,
    $appt['PatientID'],
    'Patient',
    'Appointment Rejected',
    "The doctor rejected your appointment scheduled for $dateDisplay.",
    "/Telehealth_system/patient/patient_my_appointments.php"
);
addNotification(
    $pdo,
    $nurseID,
    'Nurse',
    'Appointment Needs Reassignment',
    "A doctor rejected an appointment scheduled for $dateDisplay.",
    "/Telehealth_system/nurse/nurse_assign_doctor.php"
);

if ($returnToView) {
    header("Location: " . $returnUrl . $returnSep . "success=1&rejected=1");
    exit;
}

header("Location: " . $returnUrl . $returnSep . "success=1");
exit;



