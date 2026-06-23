<?php
// File overview: Handles doctor dashboard functionality.
session_start();
require "../db.php";

//auth check: only allow logged-in doctors to access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
// Fetch doctor's name for personalized greeting
$stmt = $pdo->prepare(
    "SELECT Uname FROM users WHERE UserID = :id"
);
$stmt->execute([':id' => $doctorID]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Doctor Dashboard</h2>
    <p class="dashboard-subtitle">
        Welcome, Dr. <?= htmlspecialchars($doctor['Uname']) ?>
    </p>

    <div class="dashboard-grid">

        <!-- My Appointments -->
        <a href="doctor_appointments.php" class="dashboard-card">
            <h3>My Appointments</h3>
            <p>View today's and upcoming patient appointments.</p>
        </a>

        <!-- Patients -->
        <a href="doctor_patients.php" class="dashboard-card">
            <h3>My Patients</h3>
            <p>Access patient profiles and medical history.</p>
        </a>

        <!-- Prescriptions -->
        <a href="doctor_prescriptions.php" class="dashboard-card">
            <h3>Prescriptions</h3>
            <p>Create and manage patient prescriptions.</p>
        </a>

        <!-- Lab Results -->
        <a href="doctor_lab_results.php" class="dashboard-card">
            <h3>Lab Results</h3>
            <p>View lab investigations for your patients</p>
        </a>

        <!-- Feedback -->
        <a href="../feedback.php" class="dashboard-card">
            <h3>Support & Feedback</h3>
            <p>Send feedback to admins and view replies.</p>
        </a>

    </div>

</div>
</main>

<?php
include "../footer.php"; ?>




