<?php
// File overview: Handles labtech chats functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in lab technicians can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Labtech') {
    header("Location: ../login.php");
    exit;
}
ensureLabTestApprovalColumns($pdo);

// Fetch lab details for this lab technician
$stmtLab = $pdo->prepare(
    "SELECT l.LabID, lab.LabName
     FROM labtechs l
     JOIN laboratories lab ON lab.LabID = l.LabID
     WHERE l.LabTechID = :id"
);
$stmtLab->execute([':id' => $_SESSION['user_id']]);
$lab = $stmtLab->fetch(PDO::FETCH_ASSOC);

if (!$lab) {
    die("Lab not found for this lab technician.");
}

$labID = $lab['LabID'];
$labRatings = getEntityRatingsMap($pdo, 'Lab');
$backUrl = 'labtech_dashboard.php';

// Fetch lab tests assigned to this lab with patient details for chat listing
$stmt = $pdo->prepare(
    "SELECT lt.LabTestID, lt.PatientID, lt.TestName, lt.TestDate, lt.Status,
            p.FName AS PatientName
     FROM lab_tests lt
     JOIN patients p ON lt.PatientID = p.PatientID
     WHERE lt.LabID = :labid
       AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'
     ORDER BY lt.TestDate DESC"
);
$stmt->execute([':labid' => $labID]);
$labTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Lab Chats</h2>
    <p class="dashboard-subtitle">
        Chats are tied to the specific lab test. Lab: <?= htmlspecialchars($lab['LabName']) ?>
        (<?= htmlspecialchars(formatEntityRatingLabel($labRatings[$labID] ?? null)) ?>)
    </p>

    <table class="user-table">
        <thead>
            <tr>
                <th>Test ID</th>
                <th>Patient</th>
                <th>Test Name</th>
                <th>Test Date</th>
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
                <td><?= htmlspecialchars($lt['PatientName']) ?></td>
                <td><?= htmlspecialchars($lt['TestName']) ?></td>
                <td><?= $lt['TestDate'] ?? '-' ?></td>
                <td>
                    <a href="labtech_labtest_chat.php?testid=<?= $lt['LabTestID'] ?>" class="btn btn-view">
                        Open Chat
                    </a>
                </td>
            </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="6">No lab tests found.</td></tr>
        <?php
endif; ?>
        </tbody>
    </table>

    <?php
if (!$labTests): ?>
        <div class="empty-state">
            <h4>No lab tests available for chat</h4>
            <p>Chats appear when your lab has assigned tests.</p>
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






