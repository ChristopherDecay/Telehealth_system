<?php
// File overview: Handles doctor patients functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    "SELECT DISTINCT a.PatientID, p.FName AS PatientFullName, u.Uname
     FROM appointments a
     JOIN users u ON a.PatientID = u.UserID
     LEFT JOIN patients p ON a.PatientID = p.PatientID
     WHERE a.DoctorID = :did
       AND LOWER(TRIM(a.Status)) NOT LIKE '%cancelled%'
       AND LOWER(TRIM(a.Status)) NOT LIKE '%canceled%'
     ORDER BY p.FName ASC, u.Uname ASC"
);
$stmt->execute([':did' => $doctorID]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
$backUrl = 'doctor_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>My Patients</h2>
    <p class="dashboard-subtitle">Patients you have appointments with.</p>

    <table class="user-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
if (!$patients): ?>
            <tr><td colspan="2">No patients found.</td></tr>
        <?php
else: ?>
            <?php
foreach ($patients as $p): ?>
                <tr>
                    <td><?= htmlspecialchars(trim((string)($p['PatientFullName'] ?? '')) !== '' ? $p['PatientFullName'] : $p['Uname']) ?></td>
                    <td>
                        <a href="doctor_view_patient.php?pid=<?= $p['PatientID'] ?>" class="btn btn-view">
                            View Profile
                        </a>
                        <a href="doctor_patient_history.php?pid=<?= $p['PatientID'] ?>" class="btn btn-view">
                            Medical History
                        </a>
                    </td>
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





