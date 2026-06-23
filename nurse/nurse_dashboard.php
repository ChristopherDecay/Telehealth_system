<?php
// File overview: Handles nurse dashboard functionality.
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Nurse') {
    header("Location: ../login.php");
    exit;
}

$nurseID = $_SESSION['user_id'];
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Nurse Dashboard</h2>
    <p class="dashboard-subtitle">Manage patient care and appointments</p>

    <div class="dashboard-grid">

        <a href="nurse_appointments.php" class="dashboard-card">
            <h3>My Appointments</h3>
            <p>View appointments assigned to you</p>
        </a>

        <a href="nurse_assign_doctor.php" class="dashboard-card">
            <h3>Assign Doctors</h3>
            <p>Match patients to doctors based on their reasons.</p>
        </a>

        <a href="../feedback.php" class="dashboard-card">
            <h3>Support & Feedback</h3>
            <p>Send feedback to admins and view replies.</p>
        </a>

    </div>

</div>
</main>

<?php
include "../footer.php"; ?>





