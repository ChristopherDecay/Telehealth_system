<?php
// File overview: Handles doctor lab results functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in doctors can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}
ensureLabTestApprovalColumns($pdo);

$doctorID = $_SESSION['user_id'];

// Fetch lab tests for patients under this doctor with laboratory details
$sql = "
    SELECT 
        lt.LabTestID,
        lt.TestName,
        lt.TestDate,
        lt.Status,
        lt.PatientApprovalStatus,
        lt.Result,
        u.Uname AS PatientName,
        lab.LabID, lab.LabName
    FROM lab_tests lt
    JOIN patients p ON lt.PatientID = p.PatientID
    JOIN appointments a ON a.PatientID = p.PatientID
    JOIN users u ON u.UserID = p.PatientID
    JOIN laboratories lab ON lt.LabID = lab.LabID
    WHERE a.DoctorID = :did
      AND LOWER(TRIM(a.Status)) NOT LIKE '%cancelled%'
      AND LOWER(TRIM(a.Status)) NOT LIKE '%canceled%'
      AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'
    ORDER BY lt.TestDate DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':did' => $doctorID]);
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');
$backUrl = 'doctor_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>Lab Results</h2>
<p class="dashboard-subtitle">Lab investigations for your patients</p>

<?php
if ($tests): ?>
<table class="admin-table">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Test Name</th>
            <th>Lab</th>
            <th>Test Date</th>
            <th>Results</th>
            <th>Chat</th>
        </tr>
    </thead>
    <tbody>

    <?php
foreach ($tests as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['PatientName']) ?></td>
            <td><?= htmlspecialchars($row['TestName']) ?></td>
            <td>
                <?= htmlspecialchars($row['LabName']) ?>
                <br><small><?= htmlspecialchars(formatEntityRatingLabel($labRatings[$row['LabID']] ?? null)) ?></small>
            </td>
            <td><?= $row['TestDate'] ?? 'Pending' ?></td>
            <td>
                <?php
if (($row['Result'] ?? '') !== ''): ?>
                    <details>
                        <summary class="btn btn-view">View Results</summary>
                        <div class="content-box-padded">
                            <?= nl2br(htmlspecialchars($row['Result'])) ?>
                        </div>
                    </details>
                <?php
else: ?>
                    <span class="status <?= strtolower((string)($row['Status'] ?? 'pending')) ?>">
                        <?= htmlspecialchars((string)($row['Status'] ?? 'Pending')) ?>
                    </span>
                <?php
endif; ?>
            </td>
            <td>
                <a href="../labtech/labtech_labtest_chat.php?testid=<?= $row['LabTestID'] ?>" class="btn btn-view">
                    Open Chat
                </a>
            </td>
        </tr>
    <?php
endforeach; ?>

    </tbody>
</table>
<?php
else: ?>
    <div class="empty-state">
        <h4>No lab results available</h4>
        <p>Results will appear once labs publish tests for your patients.</p>
        <div class="empty-actions">
            <a href="doctor_dashboard.php" class="btn btn-view">Go to Dashboard</a>
        </div>
    </div>
<?php
endif; ?>

<br>
<a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>






