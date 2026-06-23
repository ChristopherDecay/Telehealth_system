<?php
// File overview: Handles admin dashboard functionality.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../db.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

include "../header.php";
?>

<div class="dashboard-container">
    <h2>Admin Dashboard</h2>
    <p class="dashboard-subtitle">Manage users, monitor system activity, and respond to feedback</p>

    <div class="dashboard-grid">

        <a href="admin_view_users.php" class="dashboard-card">
            <h3>&#128101; Manage Users</h3>
            <p>View, approve, or disapprove user accounts</p>
        </a>

        <a href="admin_ratings.php" class="dashboard-card">
            <h3>&#11088; View Ratings</h3>
            <p>Monitor doctor and service ratings</p>
        </a>

        <a href="admin_feedback.php" class="dashboard-card">
            <h3>&#128172; Feedback</h3>
            <p>Read and respond to user feedback</p>
        </a>

        <a href="admin_manage_hospitals.php" class="dashboard-card">
            <h3>&#127973; Hospitals</h3>
            <p>Add or remove hospitals</p>
        </a>

        <a href="admin_manage_labs.php" class="dashboard-card">
            <h3>&#129514; Laboratories</h3>
            <p>Add or remove labs</p>
        </a>
        
        <a href="admin_reports.php" class="dashboard-card">
            <h3>&#128202; System Overview</h3>
            <p>View platform activity and summaries</p>
        </a>

    </div>
</div>

<?php
include "../footer.php"; ?>



