<?php
// File overview: Handles admin view users functionality.
session_start();
require "../db.php";
require "../functions.php";

/*      ADMIN CHECK      */
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_manage_users.php");
    exit;
}

$userID = $_GET['id'];

/*      FETCH USER      */
$stmt = $pdo->prepare(
    "SELECT u.UserID, u.Uname, u.Role, u.Status, u.RegDate, u.AprovDate,
            u.ApprovedBy, a.FName AS ApprovedByName
     FROM users u
     LEFT JOIN admin a ON a.AdminID = u.ApprovedBy
     WHERE u.UserID = :id"
);
$stmt->execute([':id' => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p>User not found.</p>";
    exit;
}

/*      FETCH ROLE PROFILE      */
$profile = [];
switch ($user['Role']) {
    case "Patient":
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE PatientID = :id");
        break;
    case "Caregiver":
        $stmt = $pdo->prepare("SELECT * FROM caregivers WHERE CaregiverID = :id");
        break;
    case "Doctor":
        $stmt = $pdo->prepare(
            "SELECT d.*, h.HospitalName
             FROM doctors d
             JOIN hospitals h ON d.HospitalID = h.HospitalID
             WHERE d.DoctorID = :id"
        );
        break;
    case "Nurse":
        $stmt = $pdo->prepare(
            "SELECT n.*, h.HospitalName
             FROM nurses n
             JOIN hospitals h ON n.HospitalID = h.HospitalID
             WHERE n.NurseID = :id"
        );
        break;
    case "Labtech":
        $stmt = $pdo->prepare(
            "SELECT l.*, lab.LabName
             FROM labtechs l
             JOIN laboratories lab ON l.LabID = lab.LabID
             WHERE l.LabTechID = :id"
        );
        break;
    case "Admin":
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE AdminID = :id");
        break;
    default:
        $stmt = null;
}

if ($stmt) {
    $stmt->execute([':id' => $userID]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
}
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$labRatings = getEntityRatingsMap($pdo, 'Lab');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>User Profile</h2>

    <div class="profile-row"><strong>Username:</strong> <?= htmlspecialchars($user['Uname']) ?></div>
    <div class="profile-row"><strong>Role:</strong> <?= $user['Role'] ?></div>
    <div class="profile-row">
        <strong>Status:</strong>
        <span class="status <?= strtolower($user['Status']) ?>">
            <?= $user['Status'] ?>
        </span>
    </div>
    <div class="profile-row"><strong>Registered:</strong> <?= $user['RegDate'] ?></div>
    <div class="profile-row"><strong>Approved:</strong> <?= $user['AprovDate'] ?? '-' ?></div>
    <div class="profile-row">
        <strong>Approved By:</strong>
        <?php
if (!empty($user['ApprovedByName'])): ?>
            <?= htmlspecialchars($user['ApprovedByName']) ?>
        <?php
else: ?>
            -
        <?php
endif; ?>
    </div>

    <hr>

    <h3><?= $user['Role'] ?> Details</h3>
    <?php if ($user['Role'] === 'Doctor'): ?>
        <div class="profile-row">
            <strong>Rating:</strong>
            <?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$userID] ?? null)) ?>
        </div>
    <?php elseif ($user['Role'] === 'Labtech' && !empty($profile['LabID'])): ?>
        <div class="profile-row">
            <strong>Lab Rating:</strong>
            <?= htmlspecialchars(formatEntityRatingLabel($labRatings[$profile['LabID']] ?? null)) ?>
        </div>
    <?php endif; ?>

    <?php
if ($profile): ?>
        <?php
        $skipKeys = ['PatientID','CaregiverID','DoctorID','NurseID','LabTechID','AdminID','UserID','ApprovedBy'];
        ?>
        <?php
foreach ($profile as $key => $value): ?>
            <?php if (in_array($key, $skipKeys, true)) continue; ?>
            <div class="profile-row">
                <strong><?= htmlspecialchars($key) ?>:</strong>
                <?= htmlspecialchars($value ?? '-') ?>
            </div>
        <?php
endforeach; ?>
    <?php
else: ?>
        <p>No profile details found.</p>
    <?php
endif; ?>

    <br>

    <!-- ACTIONS -->
    <form method="post" action="admin_user_status_update.php" class="filter-form">
        <input type="hidden" name="id" value="<?= $user['UserID'] ?>">

        <?php
if ($user['Status'] !== 'Active'): ?>
            <button type="submit" name="action" value="approve"
                class="btn-approve"
                onclick="return confirm('Approve this user?');">
                Approve
            </button>
        <?php
endif; ?>

        <?php
if ($user['Status'] !== 'Rejected'): ?>
            <button type="submit" name="action" value="reject"
                class="btn-reject"
                onclick="return confirm('Reject this user?');">
                Reject
            </button>
        <?php
endif; ?>

        <?php
if ($user['Status'] === 'Active'): ?>
            <button type="submit" name="action" value="suspend"
                class="btn-suspend"
                onclick="return confirm('Suspend this user?');">
                Suspend
            </button>
        <?php
endif; ?>
    </form>

    <br>
    <a href="admin_manage_users.php" class="btn btn-view">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>




