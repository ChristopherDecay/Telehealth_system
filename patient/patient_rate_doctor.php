<?php
// File overview: Lets patients rate doctors from completed appointments.
session_start();
require "../db.php";
require "../functions.php";

// Access control: only logged-in patients can rate doctors.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$error = $_GET['error'] ?? '';

// Completed appointment doctors available for rating.
$stmt = $pdo->prepare(
    "SELECT DISTINCT d.DoctorID, d.FName, h.HospitalName
     FROM appointments a
     JOIN doctors d ON a.DoctorID = d.DoctorID
     LEFT JOIN hospitals h ON d.HospitalID = h.HospitalID
     WHERE a.PatientID = :pid
       AND a.DoctorID IS NOT NULL
       AND a.Status = 'Completed'
     ORDER BY d.FName"
);
$stmt->execute([':pid' => $patientID]);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Rate a Doctor</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$doctors): ?>
        <div class="empty-state">
            <h4>No completed appointments yet</h4>
            <p>Once you complete an appointment, you can rate the doctor here.</p>
            <div class="empty-actions">
                <a href="patient_ratings.php" class="btn btn-view">&larr; Back</a>
            </div>
        </div>
    <?php else: ?>
        <form method="post" action="patient_rate_process.php" onsubmit="return validateRateForm(this);">
            <input type="hidden" name="type" value="doctor">

            <label>Doctor</label>
            <select name="entity_id">
                <option value="">-- Select --</option>
                <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['DoctorID'] ?>">
                        <?= htmlspecialchars($d['FName']) ?> (<?= htmlspecialchars($d['HospitalName'] ?? '-') ?>) - Current: <?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$d['DoctorID']] ?? null)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Rating (1&ndash;5)</label>
            <input type="text" name="rating" placeholder="1 to 5">

            <input type="submit" value="Submit Rating">
        </form>
    <?php endif; ?>

</div>
</main>

<?php
include "../footer.php"; ?>



