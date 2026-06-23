<?php
// File overview: Handles labtech dashboard functionality.
session_start();
require "../db.php";

// Access control: Only logged-in lab technicians can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Labtech') {
    header("Location: ../login.php");
    exit;
}

$labtechID = $_SESSION['user_id'];

// Fetch lab technician's username for personalized greeting
$stmt = $pdo->prepare(
    "SELECT Uname FROM users WHERE UserID = :id"
);
$stmt->execute([':id' => $labtechID]);
$labtech = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Labtech Dashboard</h2>
    <p class="dashboard-subtitle">
        Welcome, <?= htmlspecialchars($labtech['Uname'] ?? 'Lab Technician') ?>
    </p>

    <div class="dashboard-grid">

        <a href="labtech_lab_tests.php" class="dashboard-card">
            <h3>Upload Results</h3>
            <p>View, filter and submit results for lab tests.</p>
        </a>

        <a href="labtech_patients.php" class="dashboard-card">
            <h3>Patients</h3>
            <p>Access patient lists linked to your lab.</p>
        </a>

        <a href="labtech_chats.php" class="dashboard-card">
            <h3>Lab Chats</h3>
            <p>Communicate with clinicians on test requests.</p>
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




