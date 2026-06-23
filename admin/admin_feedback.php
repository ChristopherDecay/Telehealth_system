<?php
// File overview: Handles admin feedback functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only allow admins
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'], $_POST['reply'])) {
    $feedbackId = $_POST['feedback_id'];
    $reply = trim($_POST['reply']);

    if (isNonEmpty($reply)) {
        $stmt = $pdo->prepare(
            "UPDATE feedback 
             SET Reply = :reply, ReplyDate = NOW() 
             WHERE FeedbackID = :feedback_id"
        );
        $stmt->execute([':reply' => $reply, ':feedback_id' => $feedbackId]);

        header("Location: admin_feedback.php");
        exit;
    }
}

// Fetch feedback with user info
$sql = "SELECT f.FeedbackID, f.UserID, f.Feedback, f.FBDate, f.Reply, f.ReplyDate,
               u.Uname, u.Role
        FROM feedback f
        JOIN users u ON f.UserID = u.UserID
        ORDER BY f.FBDate DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>User Feedback Management</h2>

    <!-- FILTERS -->
    <div class="filter-toolbar">
        <select id="roleFilter" class="filter-select">
            <option value="">All Roles</option>
            <option value="Patient">Patient</option>
            <option value="Caregiver">Caregiver</option>
            <option value="Doctor">Doctor</option>
            <option value="Nurse">Nurse</option>
            <option value="Labtech">Labtech</option>
            <option value="Admin">Admin</option>
        </select>

        <select id="statusFilter" class="filter-select">
            <option value="">All Feedback</option>
            <option value="unanswered">Unanswered Only</option>
            <option value="answered">Answered Only</option>
        </select>

        <input type="text" id="searchInput" placeholder="Search feedback..." class="filter-search-input">
    </div>

    <!-- FEEDBACK TABLE -->
    <div class="table-scroll">
    <table class="user-table" id="feedbackTable">
        <thead>
            <tr>
                <th>Feedback ID</th>
                <th>User</th>
                <th>Role</th>
                <th>Feedback</th>
                <th>Submitted On</th>
                <th>Reply</th>
                <th>Reply Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
if ($feedbacks): ?>
            <?php
foreach ($feedbacks as $f): ?>
                <tr data-role="<?= $f['Role'] ?>" data-reply="<?= $f['Reply'] ? 'answered' : 'unanswered' ?>">
                    <td><?= $f['FeedbackID'] ?></td>
                    <td><?= htmlspecialchars($f['Uname']) ?></td>
                    <td><?= $f['Role'] ?></td>
                    <td class="cell-wrap"><?= nl2br(htmlspecialchars($f['Feedback'])) ?></td>
                    <?php
$fbDate = $f['FBDate'] ? date('d-m-Y H:i', strtotime($f['FBDate'])) : '-' ?>
                    <?php
$replyDate = $f['ReplyDate'] ? date('d-m-Y H:i', strtotime($f['ReplyDate'])) : '-' ?>
                    <td><?= htmlspecialchars($fbDate) ?></td>
                    <td>
                        <?php
if ($f['Reply']): ?>
                            <div class="cell-wrap"><?= nl2br(htmlspecialchars($f['Reply'])) ?></div>
                        <?php
else: ?>
                            <form method="post" onsubmit="return validateAdminReplyForm(this);">
                                <input type="hidden" name="feedback_id" value="<?= $f['FeedbackID'] ?>">
                                <textarea name="reply" rows="1" class="message-input feedback-reply-input" data-capitalize="sentences" placeholder="Write reply..."></textarea>
                        <?php
endif; ?>
                    </td>
                    <td><?= htmlspecialchars($replyDate) ?></td>
                    <td>
                        <?php
if (!$f['Reply']): ?>
                                <button type="submit" class="btn-approve">Send Reply</button>
                            </form>
                        <?php
endif; ?>
                    </td>
                </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="8">No feedback found.</td></tr>
        <?php
endif; ?>
        </tbody>
    </table>
    </div>

</div>
</main>

<!--     LIVE SEARCH + FILTER SCRIPT     -->
<script>
const searchInput = document.getElementById('searchInput');
const roleFilter = document.getElementById('roleFilter');
const statusFilter = document.getElementById('statusFilter');
const rows = document.querySelectorAll('#feedbackTable tbody tr');

function filterRows() {
    const search = searchInput.value.toLowerCase();
    const role = roleFilter.value;
    const status = statusFilter.value;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowRole = row.getAttribute('data-role');
        const rowStatus = row.getAttribute('data-reply');

        let show = true;

        if (role && rowRole !== role) show = false;
        if (status && rowStatus !== status) show = false;
        if (search && !text.includes(search)) show = false;

        row.style.display = show ? '' : 'none';
    });
}

// Event listeners
searchInput.addEventListener('keyup', filterRows);
roleFilter.addEventListener('change', filterRows);
statusFilter.addEventListener('change', filterRows);
</script>

<?php
include "../footer.php"; ?>



