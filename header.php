<?php
// File overview: Handles header functionality.
// Ensure session is available for role-based navigation and user state checks.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Map each role to its dashboard URL; default is the central router.
$dashboardLink = "/Telehealth_system/dashboard.php";
// Track profile completion for access gating and navigation hints.
$profileComplete = null;

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'Admin':
            $dashboardLink = "/Telehealth_system/admin/admin_dashboard.php";
            break;

        case 'Doctor':
            $dashboardLink = "/Telehealth_system/doctor/doctor_dashboard.php";
            break;

        case 'Nurse':
            $dashboardLink = "/Telehealth_system/nurse/nurse_dashboard.php";
            break;

        case 'Patient':
            $dashboardLink = "/Telehealth_system/patient/patient_dashboard.php";
            break;

        case 'Caregiver':
            $dashboardLink = "/Telehealth_system/caregiver/caregiver_dashboard.php";
            break;

        case 'Labtech':
            $dashboardLink = "/Telehealth_system/labtech/labtech_dashboard.php";
            break;
    }
}

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    require_once __DIR__ . "/db.php";

    // Enforce profile completion before accessing any other parts of the system.
    $stmt = $pdo->prepare("SELECT ProfileComplete FROM users WHERE UserID = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $profileComplete = (int)$stmt->fetchColumn() === 1;

    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $isProfilePage = strpos($script, '/profile.php') !== false;
    $isLogoutPage = strpos($script, '/logout.php') !== false;
    if (!$profileComplete && !$isProfilePage && !$isLogoutPage) {
        header("Location: /Telehealth_system/profile.php?reason=complete_profile");
        exit;
    }
}

// Fetch unread notification count for the current signed-in user.
$notifCount = 0;
if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM notifications
         WHERE UserID = :uid AND Role = :role AND IsRead = 0"
    );
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':role' => $_SESSION['role']
    ]);
    $notifCount = (int)$stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>TeleHealth System</title>
    <link rel="stylesheet" href="/Telehealth_system/css/style.css">
    <script src="/Telehealth_system/validation.js"></script>
</head>
<body>

<!-- Global top navigation shown on every page that includes header.php. -->
<nav>
    <strong>MediCo</strong>

    <div>
        <a href="/Telehealth_system/home.php">Home</a>

        <?php
if (!isset($_SESSION['user_id'])): ?>
            <a href="/Telehealth_system/login.php">Login</a>
            <a href="/Telehealth_system/register.php">Register</a>
        <?php
else: ?>
            <a href="<?= $dashboardLink ?>">Dashboard</a>
            <a href="/Telehealth_system/profile.php<?= $profileComplete ? '?edit=1' : '' ?>">
                <?= $profileComplete ? 'Edit Profile' : 'Complete Profile' ?>
            </a>
            <a href="/Telehealth_system/notifications.php">Notifications
                <span class="notif-badge" id="notifBadge" style="<?= $notifCount > 0 ? '' : 'display:none;' ?>">
                    <?= $notifCount ?>
                </span>
            </a>
            <a href="/Telehealth_system/logout.php">Logout</a>
        <?php
endif; ?>
    </div>
</nav>

<?php
if (isset($_SESSION['user_id'], $_SESSION['role'])): ?>
<?php
    // Show a back-to-dashboard shortcut on most authenticated inner pages.
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $isDashboard = strpos($script, 'dashboard.php') !== false;
    $isHome = strpos($script, '/home.php') !== false;
    $isAuth = strpos($script, '/login.php') !== false || strpos($script, '/register.php') !== false;
?>
    <?php
if (!$isDashboard && !$isHome && !$isAuth): ?>
        <div class="navigation-back-row">
            <a href="<?= $dashboardLink ?>" class="btn btn-view">&larr; Back to Dashboard</a>
        </div>
    <?php
endif; ?>
<?php
endif; ?>

<?php
if (isset($_SESSION['user_id'], $_SESSION['role'])): ?>
<script>
    // Poll notification count periodically so the badge stays fresh without reload.
    (function () {
        const badge = document.getElementById("notifBadge");
        if (!badge) return;

        async function refreshNotifCount() {
            try {
                const res = await fetch("/Telehealth_system/notifications_count.php", { cache: "no-store" });
                if (!res.ok) return;
                const data = await res.json();
                const count = parseInt(data.count || 0, 10);
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = "inline-block";
                } else {
                    badge.textContent = "0";
                    badge.style.display = "none";
                }
            } catch (e) {
                // Ignore transient network or parsing errors.
            }
        }

        refreshNotifCount();
        setInterval(refreshNotifCount, 15000);
    })();
</script>
<?php
endif; ?>
