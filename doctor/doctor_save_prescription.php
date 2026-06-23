<?php
// File overview: Save end-of-session clinical summary from appointment chat and close session.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$appointmentID = trim($_POST['appointment_id'] ?? '');
$diagnosis = trim($_POST['diagnosis'] ?? '');
$prescription = trim($_POST['prescription'] ?? '');
$futureCare = trim($_POST['future_care'] ?? '');
$specialistRecommended = trim($_POST['specialist_recommended'] ?? '');
$followupDateInput = trim($_POST['followup_date'] ?? '');

if ($appointmentID === '' || $diagnosis === '') {
    header("Location: ../appointments/appointment_chat.php?aid=$appointmentID&error=Diagnosis is required.");
    exit;
}

$ctx = getAppointmentChatContext($pdo, $appointmentID, $doctorID, 'Doctor');
if (!$ctx['allowed']) {
    header("Location: ../doctor/doctor_appointments.php");
    exit;
}
$appt = $ctx['appointment'];

if (empty($appt['NurseID'])) {
    header("Location: ../appointments/appointment_chat.php?aid=$appointmentID&error=Cannot save session without an assigned nurse.");
    exit;
}

$followupDate = null;
if ($followupDateInput !== '') {
    if (!isDateDmy($followupDateInput)) {
        header("Location: ../appointments/appointment_chat.php?aid=$appointmentID&error=Invalid follow-up date format. Use DD-MM-YYYY.");
        exit;
    }
    $followupDate = dmyToYmd($followupDateInput);
}

$existingStmt = $pdo->prepare(
    "SELECT SessionID
     FROM sessions
     WHERE AppID = :aid AND DoctorID = :doc
     LIMIT 1"
);
$existingStmt->execute([
    ':aid' => $appointmentID,
    ':doc' => $doctorID
]);
$existingSessionID = $existingStmt->fetchColumn();

if ($existingSessionID) {
    $update = $pdo->prepare(
        "UPDATE sessions
         SET Diagnosis = :diag,
             Prescription = :rx,
             FollowupDate = :fdate,
             SpecialistRecommended = :srec,
             FutureCare = :fcare
         WHERE SessionID = :sid"
    );
    $update->execute([
        ':diag' => $diagnosis,
        ':rx' => $prescription,
        ':fdate' => $followupDate,
        ':srec' => $specialistRecommended !== '' ? $specialistRecommended : null,
        ':fcare' => $futureCare !== '' ? $futureCare : null,
        ':sid' => $existingSessionID
    ]);
} else {
    $newSessionID = generateSessionID($pdo);
    $insert = $pdo->prepare(
        "INSERT INTO sessions
         (SessionID, PatientID, DoctorID, NurseID, AppID, Diagnosis, Prescription, FollowupDate, SpecialistRecommended, FutureCare)
         VALUES
         (:sid, :pid, :doc, :nid, :aid, :diag, :rx, :fdate, :srec, :fcare)"
    );
    $insert->execute([
        ':sid' => $newSessionID,
        ':pid' => $appt['PatientID'],
        ':doc' => $doctorID,
        ':nid' => $appt['NurseID'],
        ':aid' => $appointmentID,
        ':diag' => $diagnosis,
        ':rx' => $prescription,
        ':fdate' => $followupDate,
        ':srec' => $specialistRecommended !== '' ? $specialistRecommended : null,
        ':fcare' => $futureCare !== '' ? $futureCare : null
    ]);
}

$statusUpdate = $pdo->prepare(
    "UPDATE appointments
     SET Status = 'Completed'
     WHERE AppID = :aid AND DoctorID = :doc"
);
$statusUpdate->execute([
    ':aid' => $appointmentID,
    ':doc' => $doctorID
]);

$dateDisplay = date('d-m-Y H:i', strtotime($appt['AppointmentDate']));
addNotification(
    $pdo,
    $appt['PatientID'],
    'Patient',
    'Clinical Summary Ready',
    "Your doctor has posted diagnosis and care guidance for the appointment on $dateDisplay.",
    "/Telehealth_system/patient/patient_prescriptions.php"
);

header("Location: ../appointments/appointment_chat.php?aid=$appointmentID&saved=1");
exit;
