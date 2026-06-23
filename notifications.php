<?php
// File overview: Displays user notifications and marks them as read.
session_start();
require "db.php";

// Restrict access to authenticated users only.
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch notifications for the current user and role (newest first).
$stmt = $pdo->prepare(
    "SELECT NotificationID, Title, Message, Link, IsRead, CreatedAt
     FROM notifications
     WHERE UserID = :uid AND Role = :role
     ORDER BY CreatedAt DESC"
);
$stmt->execute([':uid' => $userID, ':role' => $role]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark all fetched notifications as read once the page is opened.
$pdo->prepare(
    "UPDATE notifications SET IsRead = 1 WHERE UserID = :uid AND Role = :role"
)->execute([':uid' => $userID, ':role' => $role]);
?>

<?php include "header.php"; ?>

<main class="main-content">
<div class="dashboard-container">
    <h2>Notifications</h2>
    <p class="dashboard-subtitle">Recent activity in your account</p>

    <?php if (!$notifications): ?>
        <p>No notifications yet.</p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Time</th>
                    <th>Link</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($notifications as $n): ?>
                <?php $timeDisplay = date('d-m-Y H:i', strtotime($n['CreatedAt'])); ?>
                <?php
                    // Normalize links so relative paths resolve within the app.
                    $rawLink = $n['Link'] ?? '';
                    $link = '';
                    if ($rawLink !== '') {
                        $lowerLink = strtolower($rawLink);
                        if (strpos($lowerLink, 'http://') === 0 || strpos($lowerLink, 'https://') === 0 || strpos($rawLink, '/') === 0) {
                            $link = $rawLink;
                        } else {
                            $link = '/Telehealth_system/' . ltrim($rawLink, '/');
                        }
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($n['Title']) ?></td>
                    <td><?= htmlspecialchars($n['Message']) ?></td>
                    <td><?= htmlspecialchars($timeDisplay) ?></td>
                    <td>
                        <?php if ($link !== ''): ?>
                            <a class="btn btn-view" href="<?= htmlspecialchars($link) ?>">Open</a>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</main>

<?php include "footer.php"; ?>
