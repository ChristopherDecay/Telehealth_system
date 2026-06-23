<?php
// File overview: Handles admin user status update functionality.
session_start();
require "../db.php";

/*      ADMIN CHECK      */
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

/*      INPUT CHECK      */
$id     = $_POST['id'] ?? '';
$action = $_POST['action'] ?? '';

if ($id === '' || $action === '') {
    header("Location: admin_manage_users.php");
    exit;
}

/*      ACTION MAP      */
$statusMap = [
    'approve' => ['status' => 'Active',    'aprov' => 'NOW()', 'approvedBy' => ':admin_id'],
    'reject'  => ['status' => 'Rejected',  'aprov' => 'NULL',  'approvedBy' => 'NULL'],
    'suspend' => ['status' => 'Suspended', 'aprov' => 'NULL',  'approvedBy' => 'NULL']
];

if (!isset($statusMap[$action])) {
    header("Location: admin_manage_users.php");
    exit;
}

/*      UPDATE USER      */
$sql = "
    UPDATE users 
    SET Status = :status,
        AprovDate = {$statusMap[$action]['aprov']},
        ApprovedBy = {$statusMap[$action]['approvedBy']}
    WHERE UserID = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':status' => $statusMap[$action]['status'],
    ':id'     => $id,
    ':admin_id' => $_SESSION['user_id']
]);

/*      REDIRECT      */
header("Location: admin_view_users.php?id=" . urlencode($id));
exit;



