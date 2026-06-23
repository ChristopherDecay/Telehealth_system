<?php
// File overview: Handles doctor view patient functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$patientID = $_GET['pid'] ?? '';

if ($patientID === '') {
    echo "Patient not specified.";
    exit;
}

// Access control: Ensure the doctor has an active appointment with the patient
$accessStmt = $pdo->prepare(
    "SELECT 1
     FROM appointments
     WHERE DoctorID = :did
       AND PatientID = :pid
       AND LOWER(TRIM(Status)) NOT LIKE '%cancelled%'
       AND LOWER(TRIM(Status)) NOT LIKE '%canceled%'
     LIMIT 1"
);
$accessStmt->execute([':did' => $doctorID, ':pid' => $patientID]);
if (!$accessStmt->fetchColumn()) {
    echo "Patient details are unavailable because the appointment was cancelled by the patient.";
    exit;
}

// Fetch patient basic info
$stmt = $pdo->prepare(
    "SELECT u.UserID, u.Uname, p.Gender, p.DOB, p.PhoneNum, p.Email, p.NatID_PP
     FROM users u
     JOIN patients p ON u.UserID = p.PatientID
     WHERE u.UserID = :id"
);
$stmt->execute([':id' => $patientID]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found.";
    exit;
}

// Calculate age and format DOB for display
$dob = new DateTime($patient['DOB']);
$today = new DateTime();
$age = $today->diff($dob)->y;
$dobDisplay = ymdToDmy($patient['DOB']) ?? $patient['DOB'];

// Fetch patient medical history
$stmt = $pdo->prepare(
    "SELECT Allergens, MajorSurgeries, ChronicConditions, LongTermMedications
     FROM history
     WHERE PatientID = :id"
);
$stmt->execute([':id' => $patientID]);
$history = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
$backUrl = 'doctor_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>Patient Profile</h2>

<!-- BASIC INFO -->
<div class="profile-row"><strong>Name</strong><?= htmlspecialchars($patient['Uname']) ?></div>
<div class="profile-row"><strong>Gender</strong><?= htmlspecialchars($patient['Gender']) ?></div>
<div class="profile-row"><strong>Date of Birth</strong><?= htmlspecialchars($dobDisplay) ?> (<?= $age ?> yrs)</div>
<div class="profile-row"><strong>Phone</strong><?= htmlspecialchars($patient['PhoneNum']) ?></div>
<div class="profile-row"><strong>Email</strong><?= htmlspecialchars($patient['Email']) ?></div>
<div class="profile-row"><strong>National ID / Passport</strong><?= htmlspecialchars($patient['NatID_PP']) ?></div>

<hr>

<!-- MEDICAL INFO -->
<h3>Medical Information</h3>

<div class="profile-row">
    <strong>Allergens</strong>
    <?= nl2br(htmlspecialchars($history['Allergens'] ?? 'None reported')) ?>
</div>

<div class="profile-row">
    <strong>Major Surgeries</strong>
    <?= nl2br(htmlspecialchars($history['MajorSurgeries'] ?? 'None reported')) ?>
</div>

<div class="profile-row">
    <strong>Chronic Conditions</strong>
    <?= nl2br(htmlspecialchars($history['ChronicConditions'] ?? 'None reported')) ?>
</div>

<div class="profile-row">
    <strong>Long Term Medications</strong>
    <?= nl2br(htmlspecialchars($history['LongTermMedications'] ?? 'None reported')) ?>
</div>

<hr>

<!-- ACTIONS -->
<div class="centered-action-row">
    <a href="doctor_patient_history.php?pid=<?= $patientID ?>" class="btn btn-view">
        Medical History
    </a>

    <a href="doctor_lab_results.php?pid=<?= $patientID ?>" class="btn btn-view">
        Lab Results
    </a>

    <a href="doctor_appointments.php?pid=<?= $patientID ?>" class="btn btn-view">
        Appointments
    </a>
</div>

<br>
<a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>



