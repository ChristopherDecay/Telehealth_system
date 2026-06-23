<?php
// File overview: Handles labtech patients functionality.
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
    "SELECT LabID FROM labtechs WHERE LabTechID = :id"
);
$stmtLab->execute([':id' => $_SESSION['user_id']]);
$lab = $stmtLab->fetch(PDO::FETCH_ASSOC);

if (!$lab) {
    die("Lab not found for this lab technician.");
}
$backUrl = 'labtech_dashboard.php';

$stmt = $pdo->prepare(
    "SELECT DISTINCT p.PatientID, p.FName, p.DOB, p.Gender, p.PhoneNum, p.Email, u.Uname
     FROM patients p
     JOIN users u ON p.PatientID = u.UserID
     JOIN lab_tests lt ON p.PatientID = lt.PatientID
     WHERE lt.LabID = :labid
       AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'
     ORDER BY p.FName ASC"
);
$stmt->execute([':labid' => $lab['LabID']]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Patients with Lab Tests</h2>
    <p class="dashboard-subtitle">
        View all patients who have lab tests assigned to you.
    </p>

    <table class="user-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Lab Tests</th>
            </tr>
        </thead>
        <tbody>
        <?php
if ($patients): ?>
            <?php
foreach ($patients as $p): ?>
            <?php
$dobDisplay = ymdToDmy($p['DOB']) ?? $p['DOB']; ?>
            <tr>
                <td><?= htmlspecialchars($p['FName']) ?></td>
                <td><?= htmlspecialchars($p['Uname']) ?></td>
                <td><?= htmlspecialchars($dobDisplay) ?></td>
                <td><?= $p['Gender'] ?></td>
                <td><?= htmlspecialchars($p['PhoneNum']) ?></td>
                <td><?= htmlspecialchars($p['Email']) ?></td>
                <td>
                    <a href="labtech_lab_tests.php?patientid=<?= $p['PatientID'] ?>" class="btn btn-view">
                        View Tests
                    </a>
                </td>
            </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="7">No patients assigned.</td></tr>
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





