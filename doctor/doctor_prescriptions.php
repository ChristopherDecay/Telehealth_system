<?php
// File overview: Handles doctor prescriptions functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    "SELECT s.Prescription, s.Diagnosis, a.AppointmentDate, a.PatientID, u.Uname AS PatientName
     FROM sessions s
     JOIN appointments a ON s.AppID = a.AppID
     JOIN users u ON a.PatientID = u.UserID
     WHERE a.DoctorID = :did
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':did' => $doctorID]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$backUrl = 'doctor_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Prescriptions</h2>
    <p class="dashboard-subtitle">Prescriptions recorded from your sessions.</p>

    <table class="user-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Patient</th>
            <th>Diagnosis</th>
            <th>Prescription</th>
        </tr>
        </thead>
        <tbody>
        <?php
if (!$rows): ?>
            <tr><td colspan="4">No prescriptions recorded.</td></tr>
        <?php
else: ?>
            <?php
foreach ($rows as $r): ?>
                <?php
$dateDisplay = date('d-m-Y', strtotime($r['AppointmentDate'])); ?>
                <tr>
                    <td><?= htmlspecialchars($dateDisplay) ?></td>
                    <td><?= htmlspecialchars($r['PatientName']) ?></td>
                    <td><?= nl2br(htmlspecialchars($r['Diagnosis'] ?? '')) ?></td>
                    <td><?= nl2br(htmlspecialchars($r['Prescription'] ?? '')) ?></td>
                </tr>
            <?php
endforeach; ?>
        <?php
endif; ?>
        </tbody>
    </table>

    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>


