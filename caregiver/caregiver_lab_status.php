<?php
// File overview: Handles caregiver lab status functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in caregivers can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Caregiver') {
    header("Location: ../login.php");
    exit;
}
ensureLabTestApprovalColumns($pdo);

$caregiverID = $_SESSION['user_id'];

// Fetch lab tests for patients under this caregiver with laboratory details
$stmt = $pdo->prepare(
    "SELECT l.LabTestID, l.TestName, l.Status, l.Result, l.TestDate,
            p.PatientID, p.FName AS PatientName,
            lab.LabID, lab.LabName
     FROM lab_tests l
     JOIN patients p ON l.PatientID = p.PatientID
     JOIN caregiver_patients cp ON cp.PatientID = p.PatientID
     JOIN laboratories lab ON l.LabID = lab.LabID
     WHERE cp.CaregiverID = :cid
       AND cp.Status = 'Accepted'
       AND COALESCE(l.PatientApprovalStatus, 'Accepted') = 'Accepted'
     ORDER BY l.TestDate DESC"
);
$stmt->execute([':cid' => $caregiverID]);
$labTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

<h2>Patient Lab Tests</h2>
<p class="dashboard-subtitle">View the status and results of lab tests for your patients</p>

<?php
if (!$labTests): ?>
    <p>No lab tests found for your patients.</p>
<?php
else: ?>
    <table class="user-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Test Name</th>
                <th>Lab</th>
                <th>Status</th>
                <th>Result</th>
                <th>Test Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
foreach ($labTests as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['PatientName']) ?></td>
                    <td><?= htmlspecialchars($t['TestName']) ?></td>
                    <td>
                        <?= htmlspecialchars($t['LabName']) ?>
                        <br><small><?= htmlspecialchars(formatEntityRatingLabel($labRatings[$t['LabID']] ?? null)) ?></small>
                    </td>
                    <td>
                        <span class="status <?= strtolower($t['Status']) ?>">
                            <?= $t['Status'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($t['Result'] ?? '-') ?></td>
                    <td><?= $t['TestDate'] ?></td>
                </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
<?php
endif; ?>

<a href="caregiver_dashboard.php" class="btn btn-view">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>
