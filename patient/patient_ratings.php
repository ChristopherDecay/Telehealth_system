<?php
// File overview: Shows patient rating options for completed doctor and lab services.
session_start();
require "../db.php";

// Access control: only logged-in patients can rate services.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}
$error = $_GET['error'] ?? '';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Ratings</h2>
    <p class="dashboard-subtitle">Rate doctors and laboratories you have used</p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <a href="patient_rate_doctor.php" class="dashboard-card">
            <h3>Rate Doctor</h3>
            <p>Review doctors from completed appointments</p>
        </a>

        <a href="patient_rate_lab.php" class="dashboard-card">
            <h3>Rate Laboratory</h3>
            <p>Review laboratories from completed tests</p>
        </a>

    </div>

</div>
</main>

<?php
include "../footer.php"; ?>



