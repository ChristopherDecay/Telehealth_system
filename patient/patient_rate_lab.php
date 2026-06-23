<?php
// File overview: Lets patients rate laboratories from completed lab tests.
session_start();
require "../db.php";
require "../functions.php";

// Access control: only logged-in patients can rate laboratories.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$error = $_GET['error'] ?? '';

// Completed-test laboratories available for rating.
$stmt = $pdo->prepare(
    "SELECT DISTINCT lab.LabID, lab.LabName
     FROM lab_tests lt
     JOIN laboratories lab ON lt.LabID = lab.LabID
     WHERE lt.PatientID = :pid
       AND lt.LabID IS NOT NULL
       AND (lt.Status = 'Completed' OR lt.Result IS NOT NULL)"
);
$stmt->execute([':pid' => $patientID]);
$labs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Rate a Laboratory</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$labs): ?>
        <div class="empty-state">
            <h4>No completed lab tests yet</h4>
            <p>Once you complete a lab test, you can rate the lab here.</p>
            <div class="empty-actions">
                <a href="patient_ratings.php" class="btn btn-view">&larr; Back</a>
            </div>
        </div>
    <?php else: ?>
        <form method="post" action="patient_rate_process.php" onsubmit="return validateRateForm(this);">
            <input type="hidden" name="type" value="lab">

            <label>Laboratory</label>
            <select name="entity_id">
                <option value="">-- Select --</option>
                <?php foreach ($labs as $l): ?>
                    <option value="<?= $l['LabID'] ?>">
                        <?= htmlspecialchars($l['LabName']) ?> - Current: <?= htmlspecialchars(formatEntityRatingLabel($labRatings[$l['LabID']] ?? null)) ?>
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



