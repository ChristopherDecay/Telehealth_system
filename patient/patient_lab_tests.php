<?php
// File overview: Handles patient lab tests functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in patients can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
ensureLabTestApprovalColumns($pdo);

// Check if 'ResultDate' column exists in 'lab_tests' table
$hasResultDate = false;
try {
    $check = $pdo->query("SHOW COLUMNS FROM lab_tests LIKE 'ResultDate'");
    $hasResultDate = (bool)$check->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $hasResultDate = false;
}

$resultDateSelect = $hasResultDate ? ", lt.ResultDate" : ", NULL AS ResultDate";

// Fetch patient's lab tests with laboratory details
$sql = "SELECT lt.LabTestID, lt.TestName, lt.TestDate, lt.Status, lt.Result,
               lt.PatientApprovalStatus,
               lab.LabID, lab.LabName
               $resultDateSelect
        FROM lab_tests lt
        LEFT JOIN laboratories lab ON lt.LabID = lab.LabID
        WHERE lt.PatientID = :pid
        ORDER BY lt.TestDate DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':pid' => $patientID]);
$labTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');
$backUrl = 'patient_dashboard.php';

function getStatusClass($status) {
    $normalized = strtolower(trim((string)$status));

    if ($normalized === 'completed') return 'completed';
    if ($normalized === 'pending') return 'pending';
    if ($normalized === 'rejected') return 'rejected';
    if ($normalized === 'processing' || $normalized === 'in progress') return 'processing';

    $compact = '';
    $len = strlen($normalized);
    for ($i = 0; $i < $len; $i++) {
        $ch = $normalized[$i];
        if ($ch !== ' ' && $ch !== "\t" && $ch !== "\n" && $ch !== "\r") {
            $compact .= $ch;
        }
    }

    return $compact;
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>My Lab Tests</h2>
    <p class="dashboard-subtitle">Track your lab test status and view available results</p>
    <?php
$error = trim((string)($_GET['error'] ?? ''));
$success = trim((string)($_GET['success'] ?? ''));
if ($error !== ''): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php
elseif ($success === 'approved'): ?>
        <div class="success">Lab test request approved and sent to lab.</div>
    <?php
elseif ($success === 'rejected'): ?>
        <div class="success">Lab test request rejected.</div>
    <?php
endif; ?>

    <?php
if ($labTests): ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Test ID</th>
                    <th>Test Name</th>
                    <th>Laboratory</th>
                    <th>Test Date</th>
                    <th>Status</th>
                    <th>Result</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
foreach ($labTests as $t): ?>
                    <?php
                        $approvalStatus = (string)($t['PatientApprovalStatus'] ?? 'Accepted');
                        if ($approvalStatus === 'Pending') {
                            $statusText = 'Awaiting Your Approval';
                        } elseif ($approvalStatus === 'Rejected') {
                            $statusText = 'Rejected by Patient';
                        } else {
                            $statusText = $t['Status'] ?? 'Pending';
                        }
                        $statusClass = getStatusClass($statusText);
                        $testDate = !empty($t['TestDate']) ? date('d-m-Y H:i', strtotime($t['TestDate'])) : '-';
                    ?>
                    <tr>
                        <td><?= (int)$t['LabTestID'] ?></td>
                        <td><?= htmlspecialchars($t['TestName'] ?? '-') ?></td>
                        <td>
                            <?= htmlspecialchars($t['LabName'] ?? 'Not assigned') ?>
                            <?php if (!empty($t['LabID'])): ?>
                                <br><small><?= htmlspecialchars(formatEntityRatingLabel($labRatings[$t['LabID']] ?? null)) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($testDate) ?></td>
                        <td>
                            <span class="status <?= htmlspecialchars($statusClass) ?>">
                                <?= htmlspecialchars($statusText) ?>
                            </span>
                        </td>
                        <td>
                            <?php
if (!empty($t['Result']) && $approvalStatus !== 'Pending'): ?>
                                <?= nl2br(htmlspecialchars($t['Result'])) ?>
                            <?php
else: ?>
                                <span>-</span>
                            <?php
endif; ?>
                        </td>
                        <td>
                            <?php
if ($approvalStatus === 'Pending'): ?>
                                <form method="post" action="patient_process_lab_test.php" class="inline-action-group">
                                    <input type="hidden" name="testid" value="<?= (int)$t['LabTestID'] ?>">
                                    <input type="hidden" name="decision" value="accept">
                                    <button type="submit" class="btn btn-approve">Accept</button>
                                </form>
                                <form method="post" action="patient_process_lab_test.php" class="inline-action-group">
                                    <input type="hidden" name="testid" value="<?= (int)$t['LabTestID'] ?>">
                                    <input type="hidden" name="decision" value="reject">
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            <?php
else: ?>
                                <span>-</span>
                            <?php
endif; ?>
                        </td>
                    </tr>
                <?php
endforeach; ?>
            </tbody>
        </table>
    <?php
else: ?>
        <div class="empty-state">
            <h4>No lab tests found</h4>
            <p>Your lab requests will appear here once a test is assigned.</p>
            <div class="empty-actions">
                <a href="patient_dashboard.php" class="btn btn-view">Go to Dashboard</a>
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

