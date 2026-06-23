<?php
// File overview: Handles login process functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_POST['uname'], $_POST['pwd'], $_POST['role'])) {
    header("Location: ../login.php?error=Invalid request");
    exit;
}

$uname = trim($_POST['uname']);
$pwd   = trim($_POST['pwd']);
$role  = trim($_POST['role']);

if ($uname === "" || $pwd === "" || $role === "") {
    header("Location: ../login.php?error=All fields are required");
    exit;
}

if (!isAlnumSimple($uname)) {
    header("Location: ../login.php?error=Invalid username format");
    exit;
}

// Fetch user by username
$sql = "SELECT UserID, Passwd, Role, Status, ProfileComplete FROM users WHERE Uname = :u";
$stmt = $pdo->prepare($sql);
$stmt->execute([':u' => $uname]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../login.php?error=Invalid credentials");
    exit;
}

// Verify password and role
if (!password_verify($pwd, $user['Passwd'])) {
    header("Location: ../login.php?error=Invalid credentials");
    exit;
}

if ($user['Role'] !== $role) {
    header("Location: ../login.php?error=Role mismatch");
    exit;
}

// Patients and Caregivers should be Active immediately, but other roles require admin approval

if (!in_array($role, ['Patient', 'Caregiver']) && $user['Status'] !== 'Active') {
    header("Location: ../login.php?error=Account pending approval");
    exit;
}

// Set session variables
$_SESSION['user_id'] = $user['UserID'];
$_SESSION['role']    = $user['Role'];
$_SESSION['uname']   = $uname;

// Log the login event
try {
    ensureLoginLogsTable($pdo);
    $stmt = $pdo->prepare(
        "INSERT INTO login_logs
         (UserID, Role, SessionID, LoginAt, IPAddress)
         VALUES (:uid, :role, :sid, NOW(), :ip)"
    );
    $stmt->execute([
        ':uid' => $user['UserID'],
        ':role' => $user['Role'],
        ':sid' => session_id(),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);
} catch (Exception $e) {
    // Do not block login on log write failure
}

// Check if profile is complete
if ((int)$user['ProfileComplete'] !== 1) {
    header("Location: ../profile.php");
    exit;
}

//  Redirect to respective dashboard based on role
switch ($user['Role']) {
    case 'Admin':
        header("Location: ../admin/admin_dashboard.php");
        break;
    case 'Doctor':
        header("Location: ../doctor/doctor_dashboard.php");
        break;
    case 'Nurse':
        header("Location: ../nurse/nurse_dashboard.php");
        break;
    case 'Labtech':
        header("Location: ../labtech/labtech_dashboard.php");
        break;
    case 'Caregiver':
        header("Location: ../caregiver/caregiver_dashboard.php");
        break;
    case 'Patient':
        header("Location: ../patient/patient_dashboard.php");
        break;
    default:
        session_destroy();
        header("Location: ../login.php?error=Invalid role");
}
exit;
