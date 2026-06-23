<?php
// File overview: Handles caregiver dashboard functionality.
session_start();
require "../db.php";

// Access control: Only allow caregivers
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Caregiver') {
    header("Location: ../login.php");
    exit;
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Caregiver Dashboard</h2>
    <p class="dashboard-subtitle">
        Welcome, <?= htmlspecialchars($_SESSION['uname']) ?>.  
        Manage and support patients who have approved your care.
    </p>

    <div class="dashboard-grid">

        <!-- REQUEST PATIENT -->
        <a href="caregiver_request_patient.php" class="dashboard-card">
            <h3>Request Patient</h3>
            <p>Send a care request to a patient for approval.</p>
        </a>

        <!-- ACCEPTED PATIENTS -->
        <a href="caregiver_patients.php" class="dashboard-card">
            <h3>My Patients</h3>
            <p>View and manage patients who accepted your request.</p>
        </a>

        <!-- APPOINTMENTS -->
        <a href="caregiver_appointments.php" class="dashboard-card">
            <h3>Appointments</h3>
            <p>View upcoming and past appointments for your patients.</p>
        </a>

        <!-- DOCUMENTS -->
        <a href="caregiver_upload_documents.php" class="dashboard-card">
            <h3>Medical Documents</h3>
            <p>Upload and manage patient medical files.</p>
        </a>


        <!-- LAB STATUS -->
        <a href="caregiver_lab_status.php" class="dashboard-card">
            <h3>Lab Test Status</h3>
            <p>Track lab test progress for assigned patients.</p>
        </a>

        <!-- COMMUNICATION -->
        <a href="caregiver_chats.php" class="dashboard-card">
            <h3>Doctor Communication</h3>
            <p>Discuss patient care with doctors.</p>
        </a>

        <!-- FEEDBACK -->
        <a href="../feedback.php" class="dashboard-card">
            <h3>Support & Feedback</h3>
            <p>Send questions or feedback to administrators.</p>
        </a>

    </div>
</div>
</main>

<?php
include "../footer.php"; ?>




