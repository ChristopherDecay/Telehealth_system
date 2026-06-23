<?php
// File overview: Displays prescriptions created from completed clinical sessions.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    "SELECT s.Diagnosis, s.Prescription, s.FollowupDate, s.SpecialistRecommended, s.FutureCare,
            a.AppointmentDate, s.DoctorID, d.FName AS DoctorName
     FROM sessions s
     JOIN appointments a ON s.AppID = a.AppID
     LEFT JOIN doctors d ON s.DoctorID = d.DoctorID
     WHERE s.PatientID = :pid
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':pid' => $patientID]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$backUrl = 'patient_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">
    <h2>My Prescriptions</h2>
    <p class="dashboard-subtitle">Prescriptions shared by your doctor after session completion.</p>

    <table class="user-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Doctor</th>
                <th>Diagnosis</th>
                <th>Prescription</th>
                <th>Follow-up</th>
                <th>Care Plan</th>
            </tr>
        </thead>
        <tbody>
            <?php
if (!$rows): ?>
                <tr><td colspan="6">No prescriptions available yet.</td></tr>
            <?php
else: ?>
                <?php
foreach ($rows as $r): ?>
                    <?php
                        $dateDisplay = date('d-m-Y H:i', strtotime($r['AppointmentDate']));
                        $followupDisplay = $r['FollowupDate'] ? date('d-m-Y', strtotime($r['FollowupDate'])) : '-';
                        $doctorName = $r['DoctorName'] ?? 'Assigned doctor';
                        $carePlan = trim(($r['SpecialistRecommended'] ?? '') . ' ' . ($r['FutureCare'] ?? ''));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($dateDisplay) ?></td>
                        <td>
                            <?= htmlspecialchars($doctorName) ?>
                            <?php if (!empty($r['DoctorID'])): ?>
                                <br><small><?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$r['DoctorID']] ?? null)) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($r['Diagnosis'] ?? '')) ?></td>
                        <td><?= nl2br(htmlspecialchars($r['Prescription'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($followupDisplay) ?></td>
                        <td><?= nl2br(htmlspecialchars($carePlan !== '' ? $carePlan : '-')) ?></td>
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


