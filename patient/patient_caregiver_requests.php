<?php
// File overview: Handles patient caregiver requests functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in patients can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

$stmt = $pdo->prepare(
    "SELECT cp.ID, cp.CaregiverID, cp.Status, c.FName, c.PhoneNum, c.Email, u.Uname
     FROM caregiver_patients cp
     JOIN caregivers c ON c.CaregiverID = cp.CaregiverID
     JOIN users u ON u.UserID = cp.CaregiverID
     WHERE cp.PatientID = :pid
     ORDER BY cp.ID DESC"
);
$stmt->execute([':pid' => $patientID]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
$backUrl = 'patient_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Caregiver Requests</h2>
    <p>Review and approve caregiver access requests.</p>

    <?php
if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php
endif; ?>

    <?php
if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php
endif; ?>

    <table class="user-table">
        <thead>
        <tr>
            <th>Caregiver</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
if (!$requests): ?>
            <tr><td colspan="6">No caregiver requests found.</td></tr>
        <?php
else: ?>
            <?php
foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['FName']) ?></td>
                    <td><?= htmlspecialchars($r['Uname']) ?></td>
                    <td><?= htmlspecialchars($r['PhoneNum']) ?></td>
                    <td><?= htmlspecialchars($r['Email']) ?></td>
                    <td class="status <?= strtolower($r['Status']) ?>"><?= $r['Status'] ?></td>
                    <td>
                        <?php
if ($r['Status'] === 'Pending'): ?>
                            <form method="post" action="patient_process_caregiver_request.php" class="form-inline">
                                <input type="hidden" name="request_id" value="<?= $r['ID'] ?>">
                                <button type="submit" name="action" value="accept" class="btn btn-approve">Accept</button>
                            </form>
                            <form method="post" action="patient_process_caregiver_request.php" class="form-inline">
                                <input type="hidden" name="request_id" value="<?= $r['ID'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                            </form>
                        <?php
elseif ($r['Status'] === 'Accepted'): ?>
                            <form method="post" action="patient_process_caregiver_request.php" class="form-inline">
                                <input type="hidden" name="request_id" value="<?= $r['ID'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                            </form>
                        <?php
else: ?>
                            <span class="status <?= strtolower($r['Status']) ?>"><?= $r['Status'] ?></span>
                        <?php
endif; ?>
                    </td>
                </tr>
            <?php
endforeach; ?>
        <?php
endif; ?>
        </tbody>
    </table>

    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>







