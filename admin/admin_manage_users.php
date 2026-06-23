<?php
// File overview: Handles admin manage users functionality.
session_start();
require "../db.php";

/*      ADMIN CHECK      */
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

/*      FILTERS      */
$filterRole   = $_GET['role'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$sql = "SELECT UserID, Uname, Role, Status, RegDate FROM users WHERE 1=1";
$params = [];

if ($filterRole) {
    $sql .= " AND Role = :role";
    $params[':role'] = $filterRole;
}
if ($filterStatus) {
    $sql .= " AND Status = :status";
    $params[':status'] = $filterStatus;
}

$sql .= " ORDER BY RegDate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Manage Users</h2>

    <!-- FILTER -->
    <form method="get" class="filter-form">
        <select name="role">
            <option value="">All Roles</option>
            <?php
foreach (['Patient','Caregiver','Doctor','Nurse','Labtech','Admin'] as $r): ?>
                <option value="<?= $r ?>" <?= $filterRole === $r ? 'selected' : '' ?>>
                    <?= $r ?>
                </option>
            <?php
endforeach; ?>
        </select>

        <select name="status">
            <option value="">All Status</option>
            <?php
foreach (['Pending','Active','Suspended','Rejected'] as $s): ?>
                <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>>
                    <?= $s ?>
                </option>
            <?php
endforeach; ?>
        </select>

        <button class="btn btn-view">Filter</button>
        <a href="admin_manage_users.php" class="btn btn-view">Reset</a>
    </form>

    <!-- USERS TABLE -->
    <table class="user-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
if ($users): ?>
            <?php
foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['Uname']) ?></td>
                    <td><?= $u['Role'] ?></td>
                    <td>
                        <span class="status <?= strtolower($u['Status']) ?>">
                            <?= $u['Status'] ?>
                        </span>
                    </td>
                    <td><?= $u['RegDate'] ?></td>
                    <td>
                        <a href="admin_view_users.php?id=<?= $u['UserID'] ?>" class="btn btn-view">View</a>

                        <form method="post" action="admin_user_status_update.php" class="form-inline">
                            <input type="hidden" name="id" value="<?= $u['UserID'] ?>">

                            <?php
if ($u['Status'] !== 'Active'): ?>
                                <button name="action" value="approve"
                                    class="btn-approve"
                                    onclick="return confirm('Approve user?');">
                                    Approve
                                </button>
                            <?php
endif; ?>

                            <?php
if ($u['Status'] !== 'Rejected'): ?>
                                <button name="action" value="reject"
                                    class="btn-reject"
                                    onclick="return confirm('Reject user?');">
                                    Reject
                                </button>
                            <?php
endif; ?>

                            <?php
if ($u['Status'] === 'Active'): ?>
                                <button name="action" value="suspend"
                                    class="btn-suspend"
                                    onclick="return confirm('Suspend user?');">
                                    Suspend
                                </button>
                            <?php
endif; ?>
                        </form>
                    </td>
                </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="5">No users found.</td></tr>
        <?php
endif; ?>
        </tbody>
    </table>

</div>
</main>

<?php
include "../footer.php"; ?>





