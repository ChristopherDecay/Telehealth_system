<?php
// File overview: Handles labtech lab tests functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in lab technicians can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Labtech') {
    header("Location: ../login.php");
    exit;
}
ensureLabTestApprovalColumns($pdo);

// Get optional patient filter from query parameters
$patientFilter = $_GET['patientid'] ?? '';

/* Optional: Get patient name for display */
$patientName = '';
if ($patientFilter) {
    $stmtName = $pdo->prepare(
        "SELECT p.FName, u.Uname
         FROM patients p
         JOIN users u ON p.PatientID = u.UserID
         WHERE p.PatientID = :val OR u.Uname = :val
         LIMIT 1"
    );
    $stmtName->execute([':val' => $patientFilter]);
    $patientRow = $stmtName->fetch(PDO::FETCH_ASSOC);
    if ($patientRow) {
        $patientName = $patientRow['FName'] ?: $patientRow['Uname'];
    } else {
        $patientName = 'Unknown Patient';
    }
}

// Fetch lab details for this lab technician
$stmtLab = $pdo->prepare(
    "SELECT LabID FROM labtechs WHERE LabTechID = :id"
);
$stmtLab->execute([':id' => $_SESSION['user_id']]);
$lab = $stmtLab->fetch(PDO::FETCH_ASSOC);

if (!$lab) {
    die("Lab not found for this lab technician.");
}

$labID = $lab['LabID'];
$backUrl = 'labtech_dashboard.php';

// Fetch lab tests assigned to this lab with patient details for chat listing, applying patient filter if provided
if ($patientFilter) {
    $stmt = $pdo->prepare(
        "SELECT lt.LabTestID, lt.PatientID, lt.TestName, lt.TestDate, lt.Status, lt.Result,
                p.FName AS PatientName, u.Uname AS PatientUname
         FROM lab_tests lt
         JOIN patients p ON lt.PatientID = p.PatientID
         JOIN users u ON p.PatientID = u.UserID
         WHERE lt.LabID = :labid
           AND (lt.PatientID = :patient OR u.Uname = :patient)
           AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'
         ORDER BY lt.TestDate DESC"
    );
    $stmt->execute([
        ':labid' => $labID,
        ':patient' => $patientFilter
    ]);
} else {
    $stmt = $pdo->prepare(
        "SELECT lt.LabTestID, lt.PatientID, lt.TestName, lt.TestDate, lt.Status, lt.Result,
                p.FName AS PatientName, u.Uname AS PatientUname
         FROM lab_tests lt
         JOIN patients p ON lt.PatientID = p.PatientID
         JOIN users u ON p.PatientID = u.UserID
         WHERE lt.LabID = :labid
           AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'
         ORDER BY lt.TestDate DESC"
    );
    $stmt->execute([':labid' => $labID]);
}

$labTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Lab Tests</h2>

    <!-- FILTER FORM -->
    <form method="get" class="filter-form">
        <input type="text" name="patientid" placeholder="Enter Patient Username" value="<?= htmlspecialchars($patientFilter) ?>">
        <input type="submit" value="Filter" class="btn btn-view">
        <a href="labtech_lab_tests.php" class="btn btn-view">Reset</a>
    </form>

    <?php
if ($patientFilter): ?>
        <p class="dashboard-subtitle">
            Showing lab tests for patient: <strong><?= htmlspecialchars($patientName) ?></strong>
        </p>
    <?php
else: ?>
        <p class="dashboard-subtitle">
            All lab tests assigned to you.
        </p>
    <?php
endif; ?>

    <!-- LAB TESTS TABLE -->
    <table class="user-table">
        <thead>
            <tr>
                <th>Test ID</th>
                <th>Patient</th>
                <th>Test Name</th>
                <th>Test Date</th>
                <th>Status</th>
                <th>Result</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
if ($labTests): ?>
            <?php
foreach ($labTests as $lt): ?>
            <tr>
                <td><?= $lt['LabTestID'] ?></td>
                <td><?= htmlspecialchars($lt['PatientName'] ?: $lt['PatientUname']) ?></td>
                <td><?= htmlspecialchars($lt['TestName']) ?></td>
                <td><?= $lt['TestDate'] ?></td>
                <td>
                    <span class="status <?= strtolower($lt['Status']) ?>">
                        <?= $lt['Status'] ?>
                    </span>
                </td>
                <td>
                    <?= htmlspecialchars($lt['Result'] ?? '-') ?>
                </td>
                <td>
                    <?php
if ($lt['Status'] !== 'Completed'): ?>
                        <a href="labtech_upload_results.php?testid=<?= $lt['LabTestID'] ?>" class="btn btn-approve">
                            Upload Result
                        </a>
                    <?php
else: ?>
                        <span class="status completed">Completed</span>
                    <?php
endif; ?>
                </td>
            </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="7">No lab tests found.</td></tr>
        <?php
endif; ?>
        </tbody>
    </table>

    <?php
if (!$labTests): ?>
        <div class="empty-state">
            <h4>No lab tests assigned</h4>
            <p>Once tests are assigned to your lab, they will appear here.</p>
            <div class="empty-actions">
                <a href="labtech_dashboard.php" class="btn btn-view">Go to Dashboard</a>
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






