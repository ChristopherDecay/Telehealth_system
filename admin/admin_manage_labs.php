<?php
// File overview: Handles admin manage labs functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$labs = $pdo->query(
    "SELECT * FROM laboratories ORDER BY LabName"
)->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>Manage Laboratories</h2>

<?php
if (isset($_GET['success'])): ?>
    <div class="toast toast--success" id="toastSuccess">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
    <script>
        (function () {
            const toast = document.getElementById("toastSuccess");
            if (!toast) return;
            requestAnimationFrame(() => toast.classList.add("toast--show"));
            setTimeout(() => toast.classList.remove("toast--show"), 3000);
            setTimeout(() => toast.remove(), 3600);
        })();
    </script>
<?php
endif; ?>

<!-- ADD LAB -->
<form method="post" action="admin_lab_process.php" class="form-box" onsubmit="return validateLabForm(this);">
    <h3>Add Laboratory</h3>

    <label>Lab ID</label>
    <input type="text" value="Auto-generated" readonly>

    <label>Lab Name</label>
    <input type="text" name="lab_name" data-capitalize="words">

    <label>Location</label>
    <input type="text" name="lab_location" data-capitalize="words">

    <button type="submit" name="action" value="add" class="btn-approve">
        Add Lab
    </button>
</form>

<hr>

<table class="user-table">
<thead>
<tr id="lab-<?= htmlspecialchars($l['LabID']) ?>">
    <th>ID</th>
    <th>Name</th>
    <th>Location</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php
foreach ($labs as $l): ?>
<tr>
    <td><?= $l['LabID'] ?></td>
    <td>
        <?= htmlspecialchars($l['LabName']) ?><br>
        <small><?= htmlspecialchars(formatEntityRatingLabel($labRatings[$l['LabID']] ?? null)) ?></small>
    </td>
    <td><?= htmlspecialchars($l['LabLocation']) ?></td>
    <td>
        <form method="post" action="admin_lab_process.php"
              onsubmit="return confirm('Delete this lab?');">
            <input type="hidden" name="lab_id" value="<?= $l['LabID'] ?>">
            <button type="submit" name="action" value="delete" class="btn-reject">
                Delete
            </button>
        </form>
    </td>
</tr>
<?php
endforeach; ?>

</tbody>
</table>

</div>
</main>

<?php
include "../footer.php"; ?>



