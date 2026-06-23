<?php
// File overview: Handles dashboard functionality.
session_start();
require "db.php";

// Ensure the user is logged in with a valid role before routing.
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Verify the user still exists and check profile completion status.
$stmt = $pdo->prepare("SELECT ProfileComplete FROM users WHERE UserID = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Block dashboard access until profile setup is complete.
if ((int)$user['ProfileComplete'] !== 1) {
    header("Location: profile.php");
    exit;
}

// Role-based dashboard routing.
switch ($role) {
    case 'Admin':
        header("Location: admin/admin_dashboard.php");
        break;
    case 'Doctor':
        header("Location: doctor/doctor_dashboard.php");
        break;
    case 'Nurse':
        header("Location: nurse/nurse_dashboard.php");
        break;
    case 'Patient':
        header("Location: patient/patient_dashboard.php");
        break;
    case 'Caregiver':
        header("Location: caregiver/caregiver_dashboard.php");
        break;
    case 'Labtech':
        header("Location: labtech/labtech_dashboard.php");
        break;
    default:
        // Defensive fallback for invalid or tampered role values.
        session_destroy();
        header("Location: login.php?error=Invalid role");
        break;
}

exit;
