<?php
// File overview: Handles nurse appointments functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Nurse') {
    header("Location: ../login.php");
    exit;
}

$nurseID = $_SESSION['user_id'];
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$backUrl = 'nurse_dashboard.php';

$stmt = $pdo->prepare(
    "SELECT a.AppID, a.AppointmentDate, a.DurationMinutes, a.Status, a.DoctorID,
            p.FName AS PatientName
     FROM appointments a
     JOIN patients p ON a.PatientID = p.PatientID
     WHERE a.NurseID = :nid
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':nid' => $nurseID]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>My Appointments</h2>
    <p class="dashboard-subtitle">View appointments assigned to you.</p>
    <?php
if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php
elseif ($success): ?>
        <div class="success">Appointment rejected and reassigned successfully.</div>
    <?php
endif; ?>

    <table class="user-table">
        <thead>
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
if (!$appointments): ?>
            <tr><td colspan="4">No appointments found.</td></tr>
        <?php
else: ?>
            <?php
foreach ($appointments as $row): ?>
                <?php
$dateDisplay = date('d-m-Y H:i', strtotime($row['AppointmentDate'])); ?>
                <tr>
                    <td><?= htmlspecialchars($row['PatientName']) ?></td>
                    <td><?= htmlspecialchars($dateDisplay) ?></td>
                    <td><?= htmlspecialchars(getAppointmentStatusLabel($row['Status'], $row['DoctorID'] ?? null, null)) ?></td>
                    <td>

                        <a href="nurse_view_patient.php?appointment=<?= $row['AppID'] ?>" class="btn btn-view">
                            View Patient
                        </a>
                        <a href="../appointments/appointment_chat.php?aid=<?= $row['AppID'] ?>" class="btn btn-view">
                            Open Chat
                        </a>
                        <?php
if (!in_array((string)$row['Status'], ['Completed','Cancelled'], true)): ?>
                            <form method="post" action="nurse_reject_appointment.php" class="inline-action-group">
                                <input type="hidden" name="appid" value="<?= $row['AppID'] ?>">
                                <input type="hidden" name="return_to_list" value="1">
                                <input type="text" name="reason" data-capitalize="sentences" placeholder="Reassignment reason (optional)" size="20">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reassign this appointment to another nurse?')">
                                    Reassign Nurse
                                </button>
                            </form>
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

    <?php
if (!$appointments): ?>
        <div class="empty-state">
            <h4>No appointments assigned</h4>
            <p>New patient appointments will appear here once assigned.</p>
            <div class="empty-actions">
                <a href="nurse_dashboard.php" class="btn btn-view">Go to Dashboard</a>
            </div>
        </div>
    <?php
endif; ?>

    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>






