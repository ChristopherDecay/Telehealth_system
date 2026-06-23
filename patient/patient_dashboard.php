<?php
// File overview: Handles patient dashboard functionality.
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Patient Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['uname']) ?></p>

    <div class="dashboard-grid">

        <a href="patient_book_appointments.php" class="dashboard-card">
            <h3>&#128467; Book Appointment</h3>
            <p>Schedule consultations with doctors</p>
        </a>
        <a href="patient_my_appointments.php" class="dashboard-card">
            <h3>My Appointments</h3>
            <p>View appointment history and status</p>
        </a>
        <a href="patient_caregiver_requests.php" class="dashboard-card">
            <h3>Caregiver Requests</h3>
            <p>Approve or reject caregiver access</p>
        </a>

        <a href="patient_upload_documents.php" class="dashboard-card">
            <h3>&#128196; Medical Documents</h3>
            <p>Upload and view your medical files</p>
        </a>

        <a href="patient_lab_tests.php" class="dashboard-card">
            <h3>&#129514; Lab Tests</h3>
            <p>Check lab test results and status</p>
        </a>

        <a href="patient_prescriptions.php" class="dashboard-card">
            <h3>Prescriptions</h3>
            <p>View diagnosis and medication plans from doctors</p>
        </a>

        <a href="patient_ratings.php" class="dashboard-card">
            <h3>&#11088; Rate Services</h3>
            <p>Rate doctors & labs</p>
        </a>

        <a href="../feedback.php" class="dashboard-card">
            <h3>&#128172; Feedback</h3>
            <p>Send feedback and view responses</p>
        </a>

    </div>

</div>
</main>

<?php
include "../footer.php"; ?>







