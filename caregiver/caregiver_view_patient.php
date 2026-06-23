<?php
// File overview: Handles caregiver view patient functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in caregivers can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Caregiver') {
    header("Location: ../login.php");
    exit;
}

$caregiverID = $_SESSION['user_id'];

// Get patient ID from query parameter and validate
$stmt = $pdo->prepare(
    "SELECT p.PatientID, p.FName, p.DOB, p.Gender, p.PhoneNum,
            u.Uname
     FROM caregiver_patients cp
     JOIN patients p ON cp.PatientID = p.PatientID
     JOIN users u ON p.PatientID = u.UserID
     WHERE cp.CaregiverID = :cid
       AND cp.Status = 'Accepted'
     ORDER BY p.FName"
);
$stmt->execute([':cid' => $caregiverID]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
$backUrl = 'caregiver_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>My Patients</h2>
    <p class="dashboard-subtitle">
        Patients who have approved you as their caregiver.
    </p>

    <?php
if (!$patients): ?>
        <p>No patients assigned yet.</p>
    <?php
else: ?>

    <table class="user-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php
foreach ($patients as $p): ?>
            <?php
$dobDisplay = ymdToDmy($p['DOB']) ?? $p['DOB']; ?>
            <tr>
                <td><?= htmlspecialchars($p['FName']) ?></td>
                <td><?= htmlspecialchars($p['Uname']) ?></td>
                <td><?= $p['Gender'] ?></td>
                <td><?= htmlspecialchars($dobDisplay) ?></td>
                <td><?= htmlspecialchars($p['PhoneNum']) ?></td>
                <td>
                    <a href="caregiver_view_patient.php?pid=<?= $p['PatientID'] ?>"
                       class="btn btn-view">
                       View Patient
                    </a>
                </td>
            </tr>
        <?php
endforeach; ?>

        </tbody>
    </table>

    <?php
endif; ?>

    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>





