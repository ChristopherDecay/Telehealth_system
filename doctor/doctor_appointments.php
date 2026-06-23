<?php
// File overview: Handles doctor appointments functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in doctors can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];

// Fetch doctor's appointments with patient details and caregiver count
$stmt = $pdo->prepare(
    "SELECT a.AppID, a.AppointmentDate, a.DurationMinutes, a.Status,
            a.PatientID, COALESCE(p.FName, u.Uname) AS PatientName,
            (
                SELECT COUNT(*) FROM caregiver_patients cp
                WHERE cp.PatientID = a.PatientID AND cp.Status = 'Accepted'
            ) AS CaregiverCount
     FROM appointments a
     JOIN users u ON a.PatientID = u.UserID
     LEFT JOIN patients p ON a.PatientID = p.PatientID
     WHERE a.DoctorID = :doc
       AND a.Status <> 'AwaitingPatientApproval'
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':doc' => $doctorID]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$error = $_GET['error'] ?? '';
$success = isset($_GET['success']);
$backUrl = 'doctor_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">
    <h2>My Appointments</h2>
    <p class="dashboard-subtitle">View your upcoming appointments and start consultations</p>

    <?php
if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php
elseif ($success): ?>
        <div class="success">Appointment rejected and reassigned.</div>
    <?php
endif; ?>

    <?php
if ($appointments): ?>
    <table class="user-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Appointment Date</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
foreach ($appointments as $row): ?>
            <?php
                $dateDisplay = date('d-m-Y H:i', strtotime($row['AppointmentDate']));
                $durationValue = (int)($row['DurationMinutes'] ?? 20);
            ?>
            <tr>
                <td><?= htmlspecialchars($row['PatientName']) ?></td>
                <td><?= htmlspecialchars($dateDisplay) ?></td>
                <td><?= htmlspecialchars($durationValue) ?> min</td>
                <td><?= htmlspecialchars(getAppointmentStatusLabel($row['Status'], $doctorID, null)) ?></td>
                <td>
                    <?php
$statusLower = strtolower(trim((string)$row['Status']));
$isCancelled = (strpos($statusLower, 'cancelled') !== false) || (strpos($statusLower, 'canceled') !== false);
if (!$isCancelled): ?>
                        <a href="../appointments/appointment_chat.php?aid=<?= $row['AppID'] ?>" 
                           class="btn btn-view">Open Chat</a>
                        <?php
if ((int)$row['CaregiverCount'] > 0): ?>
                            <a href="../caregiver/caregiver_appointment_chat.php?aid=<?= $row['AppID'] ?>" 
                               class="btn btn-view">Caregiver Chat</a>
                        <?php
endif; ?>
                        <?php
if (!in_array($row['Status'], ['Completed','Cancelled'], true)): ?>
                            <form method="post" action="doctor_reject_appointment.php" class="inline-action-group">
                                <input type="hidden" name="appid" value="<?= $row['AppID'] ?>">
                                <input type="text" name="reason" data-capitalize="sentences" placeholder="Reassignment reason (optional)" size="22">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject and reassign this appointment?')">Reject &amp; Reassign</button>
                            </form>
                        <?php
endif; ?>
                    <?php
else: ?>
                        <span class="status cancelled">Cancelled</span>
                    <?php
endif; ?>
                </td>
            </tr>
        <?php
endforeach; ?>
        </tbody>
    </table>
    <?php
else: ?>
        <div class="empty-state">
            <h4>No appointments assigned</h4>
            <p>When a patient is assigned to you, their appointment will appear here.</p>
            <div class="empty-actions">
                <a href="doctor_dashboard.php" class="btn btn-view">Go to Dashboard</a>
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






