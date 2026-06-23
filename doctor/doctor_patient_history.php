<?php
// File overview: Handles doctor patient history functionality.
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

// Access control: Verify doctor has access to this patient's details through an active appointment
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

// Fetch patient details for display
$stmt = $pdo->prepare("SELECT Uname FROM users WHERE UserID = :id");
$stmt->execute([':id' => $patientID]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found.";
    exit;
}

// Fetch clinical sessions (diagnosis and prescription) for this patient
$stmt = $pdo->prepare(
    "SELECT s.Diagnosis, s.Prescription, a.AppointmentDate
     FROM sessions s
     JOIN appointments a ON s.AppID = a.AppID
     WHERE s.PatientID = :pid
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':pid' => $patientID]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$backUrl = getSafeBackUrl('doctor_appointments.php');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

<h2>Medical History</h2>
<p class="dashboard-subtitle">
    Patient: <strong><?= htmlspecialchars($patient['Uname']) ?></strong>
</p>

<!--            SESSION HISTORY            -->
<h3>Clinical History</h3>

<table class="user-table">
<thead>
<tr>
    <th>Date</th>
    <th>Diagnosis</th>
    <th>Prescription</th>
</tr>
</thead>
<tbody>
<?php
if ($sessions): ?>
    <?php
foreach ($sessions as $s): ?>
    <?php
$dateDisplay = date('d-m-Y', strtotime($s['AppointmentDate'])); ?>
    <tr>
        <td><?= htmlspecialchars($dateDisplay) ?></td>
        <td><?= nl2br(htmlspecialchars($s['Diagnosis'] ?? '')) ?></td>
        <td><?= nl2br(htmlspecialchars($s['Prescription'] ?? '')) ?></td>
    </tr>
    <?php
endforeach; ?>
<?php
else: ?>
    <tr><td colspan="3">No clinical sessions recorded.</td></tr>
<?php
endif; ?>
</tbody>
</table>

<br>
<a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view"
   onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>
