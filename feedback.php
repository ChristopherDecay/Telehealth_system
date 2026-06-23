<?php
// File overview: Handles feedback submission and feedback history display for non-admin users.
session_start();
require "db.php";
require "functions.php";

// Allow only authenticated non-admin users.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] === "Admin") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Read one-time status messages from redirect query params.
$success = isset($_GET['success']);
$error = $_GET['error'] ?? "";

// Load the current user's feedback history with any admin replies.
$stmt = $pdo->prepare(
    "SELECT Feedback, FBDate, Reply, ReplyDate
     FROM feedback
     WHERE UserID = :uid
     ORDER BY FBDate DESC"
);
$stmt->execute([':uid' => $userID]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Send Feedback to Admin</h2>
    <p class="dashboard-subtitle">
        Share questions, suggestions, or concerns. Admin replies will appear below.
    </p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success">Feedback submitted successfully.</div>
    <?php endif; ?>

    <form method="post" action="process/feedback_process.php" class="form-stack feedback-form"
          onsubmit="return validateFeedbackForm(this);">
        <label for="feedback">Your Feedback:</label>
        <textarea name="feedback" id="feedback" rows="2" class="message-input feedback-textarea" data-capitalize="sentences"
                  placeholder="Type your feedback..."></textarea>
        <input type="submit" value="Submit Feedback">
    </form>

    <h3>Your Feedback History</h3>
    <table class="user-table">
        <thead>
            <tr>
                <th>Feedback</th>
                <th>Submitted On</th>
                <th>Admin Reply</th>
                <th>Reply Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($feedbacks): ?>
                <?php foreach ($feedbacks as $f): ?>
                    <?php
                        $fbDate = $f['FBDate'] ? date('d-m-Y H:i', strtotime($f['FBDate'])) : '-';
                        $replyDate = $f['ReplyDate'] ? date('d-m-Y H:i', strtotime($f['ReplyDate'])) : '-';
                    ?>
                    <tr>
                        <td><?= nl2br(htmlspecialchars($f['Feedback'])) ?></td>
                        <td><?= htmlspecialchars($fbDate) ?></td>
                        <td><?= nl2br(htmlspecialchars($f['Reply'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars($replyDate) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No feedback submitted yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
</main>

<?php include "footer.php"; ?>
