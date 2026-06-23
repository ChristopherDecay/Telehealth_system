<?php
// File overview: Handles admin manage hospitals functionality.
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$hospitals = $pdo->query(
    "SELECT * FROM hospitals ORDER BY HospitalName"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>Manage Hospitals</h2>

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

<!-- ADD HOSPITAL -->
<form method="post" action="admin_hospital_process.php" class="form-box" onsubmit="return validateHospitalForm(this);">
    <h3>Add Hospital</h3>

    <label>Hospital ID</label>
    <input type="text" value="Auto-generated" readonly>

    <label>Hospital Name</label>
    <input type="text" name="hospital_name" data-capitalize="words">

    <label>Location</label>
    <input type="text" name="location" data-capitalize="words">

    <label>KMPDC License</label>
    <input type="text" name="license">

    <button type="submit" name="action" value="add" class="btn-approve">Add Hospital</button>
</form>

<hr>

<!-- HOSPITAL LIST -->
<table class="user-table">
<thead>
<tr id="hospital-<?= htmlspecialchars($h['HospitalID']) ?>">
    <th>ID</th>
    <th>Name</th>
    <th>Location</th>
    <th>License</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php
foreach ($hospitals as $h): ?>
<tr>
    <td><?= $h['HospitalID'] ?></td>
    <td><?= htmlspecialchars($h['HospitalName']) ?></td>
    <td><?= htmlspecialchars($h['Location']) ?></td>
    <td><?= htmlspecialchars($h['KMPDCLicense']) ?></td>
    <td>
        <form method="post" action="admin_hospital_process.php"
              onsubmit="return confirm('Delete this hospital?');">
            <input type="hidden" name="hospital_id" value="<?= $h['HospitalID'] ?>">
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



