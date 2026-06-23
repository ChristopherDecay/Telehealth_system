<?php
// File overview: Handles nurse view patient functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Nurse') {
    header("Location: ../login.php");
    exit;
}

$nurseID = $_SESSION['user_id'];
$appointmentID = $_GET['appointment'] ?? null;

if (!$appointmentID) {
    die("Invalid appointment.");
}

/* Verify assignment via sessions */
$check = $pdo->prepare(
    "SELECT a.PatientID
     FROM appointments a
     WHERE a.AppID = :aid AND a.NurseID = :nid"
);
$check->execute([
    ':aid' => $appointmentID,
    ':nid' => $nurseID
]);
$appointment = $check->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    die("Unauthorized access.");
}

/* Fetch patient info */
$stmt = $pdo->prepare(
    "SELECT p.PatientID, p.FName, p.DOB, p.Gender, p.PhoneNum, p.Email
     FROM appointments a
     JOIN patients p ON a.PatientID = p.PatientID
     WHERE a.AppID = :aid"
);
$stmt->execute([':aid' => $appointmentID]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die("Patient not found.");
}

$dobDisplay = ymdToDmy($patient['DOB']) ?? $patient['DOB'];

/* Fetch all vitals recorded for this appointment */
$stmt = $pdo->prepare(
    "SELECT Temperature, BloodPressure, HeartRate, RespiratoryRate,
            OxygenSaturation, RecordedAt, UploadedByID, UploadedByRole,
            COALESCE(
                CASE UploadedByRole
                    WHEN 'Patient' THEN p.FName
                    WHEN 'Doctor' THEN d.FName
                    WHEN 'Nurse' THEN n.FName
                    WHEN 'Caregiver' THEN c.FName
                    WHEN 'Labtech' THEN l.FName
                    WHEN 'Admin' THEN a.FName
                    ELSE NULL
                END,
                u.Uname,
                UploadedByID
            ) AS UploadedByName
     FROM patient_vitals
     LEFT JOIN users u ON u.UserID = patient_vitals.UploadedByID
     LEFT JOIN patients p ON p.PatientID = patient_vitals.UploadedByID
     LEFT JOIN doctors d ON d.DoctorID = patient_vitals.UploadedByID
     LEFT JOIN nurses n ON n.NurseID = patient_vitals.UploadedByID
     LEFT JOIN caregivers c ON c.CaregiverID = patient_vitals.UploadedByID
     LEFT JOIN labtechs l ON l.LabTechID = patient_vitals.UploadedByID
     LEFT JOIN admin a ON a.AdminID = patient_vitals.UploadedByID
     WHERE AppID = :aid
     ORDER BY RecordedAt DESC"
);
$stmt->execute([':aid' => $appointmentID]);
$vitals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Patient Details</h2>

    <div class="profile-row"><strong>Name</strong><?= htmlspecialchars($patient['FName']) ?></div>
    <div class="profile-row"><strong>DOB</strong><?= htmlspecialchars($dobDisplay) ?></div>
    <div class="profile-row"><strong>Gender</strong><?= htmlspecialchars($patient['Gender']) ?></div>
    <div class="profile-row"><strong>Phone</strong><?= htmlspecialchars($patient['PhoneNum']) ?></div>
    <div class="profile-row"><strong>Email</strong><?= htmlspecialchars($patient['Email']) ?></div>

    <hr>

    <h3>Recorded Vitals</h3>

    <?php
if ($vitals): ?>
        <?php
foreach ($vitals as $v): ?>
            <div class="profile-container notes-card">
                <strong>Date:</strong> <?= $v['RecordedAt'] ?><br>
                <strong>Temperature:</strong> <?= htmlspecialchars($v['Temperature'] ?? '-') ?><br>
                <strong>Blood Pressure:</strong> <?= htmlspecialchars($v['BloodPressure'] ?? '-') ?><br>
                <strong>Heart Rate:</strong> <?= htmlspecialchars($v['HeartRate'] ?? '-') ?><br>
                <strong>Respiratory Rate:</strong> <?= htmlspecialchars($v['RespiratoryRate'] ?? '-') ?><br>
                <strong>Oxygen Saturation:</strong> <?= htmlspecialchars($v['OxygenSaturation'] ?? '-') ?><br>
                <strong>Uploader Role:</strong> <?= htmlspecialchars($v['UploadedByRole'] ?? '-') ?><br>
                <strong>Uploaded By:</strong> <?= htmlspecialchars($v['UploadedByName'] ?? '-') ?>
            </div>
        <?php
endforeach; ?>
    <?php
else: ?>
        <p>No vitals recorded yet for this appointment.</p>
    <?php
endif; ?>

    <a href="nurse_appointments.php" class="btn btn-view">Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>



