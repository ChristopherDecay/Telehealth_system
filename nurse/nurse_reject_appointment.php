<?php
// File overview: Handles nurse reject/reassign appointment functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Nurse") {
    header("Location: ../login.php");
    exit;
}

$nurseID = $_SESSION['user_id'];
$appointmentID = $_POST['appid'] ?? '';
$reason = trim($_POST['reason'] ?? '');
$returnToList = isset($_POST['return_to_list']);
$returnUrl = $returnToList ? "nurse_appointments.php" : "nurse_assign_doctor.php";
$returnSep = strpos($returnUrl, '?') === false ? '?' : '&';

if ($appointmentID === '') {
    header("Location: " . $returnUrl);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT AppID, AppointmentDate, DurationMinutes, PatientID, DoctorID, NurseID, Status
     FROM appointments
     WHERE AppID = :aid
       AND NurseID = :nid
       AND Status NOT IN ('Completed','Cancelled')"
);
$stmt->execute([
    ':aid' => $appointmentID,
    ':nid' => $nurseID
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

/* Reassign to another available nurse with the lowest active load. */
$nurseStmt = $pdo->prepare(
    "SELECT n.NurseID
     FROM nurses n
     LEFT JOIN appointments a_load
       ON a_load.NurseID = n.NurseID
      AND a_load.Status IN ('Pending','Confirmed','AwaitingPatientApproval')
     WHERE n.NurseID <> :current_nid
       AND NOT EXISTS (
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
    ':current_nid' => $nurseID,
    ':new_start' => $appointmentDateTime,
    ':new_end' => $appointmentEnd
]);
$newNurseID = $nurseStmt->fetchColumn() ?: null;

if (empty($newNurseID)) {
    header("Location: " . $returnUrl . $returnSep . "error=No other triage nurse is available for reassignment at this time.");
    exit;
}

$reassignReason = $reason === '' ? null : $reason;
$update = $pdo->prepare(
    "UPDATE appointments
     SET NurseID = :new_nid,
         DoctorID = NULL,
         Status = 'Pending',
         DoctorRejectedAt = NOW(),
         DoctorRejectionReason = :reason
     WHERE AppID = :aid
       AND NurseID = :current_nid"
);
$update->execute([
    ':new_nid' => $newNurseID,
    ':reason' => $reassignReason,
    ':aid' => $appointmentID,
    ':current_nid' => $nurseID
]);

$dateDisplay = date('d-m-Y H:i', strtotime($appt['AppointmentDate']));
addNotification(
    $pdo,
    $appt['PatientID'],
    'Patient',
    'Appointment Reassignment',
    "Your appointment scheduled for $dateDisplay is being reassigned.",
    "/Telehealth_system/patient/patient_my_appointments.php"
);
if (!empty($appt['DoctorID'])) {
    addNotification(
        $pdo,
        $appt['DoctorID'],
        'Doctor',
        'Appointment Reassigned',
        "A nurse rejected and reassigned an appointment scheduled for $dateDisplay.",
        "/Telehealth_system/doctor/doctor_appointments.php"
    );
}
addNotification(
    $pdo,
    $newNurseID,
    'Nurse',
    'Appointment Needs Assignment',
    "An appointment scheduled for $dateDisplay has been reassigned to you.",
    "/Telehealth_system/nurse/nurse_assign_doctor.php"
);

header("Location: " . $returnUrl . $returnSep . "success=rejected");
exit;
