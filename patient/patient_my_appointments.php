<?php
// File overview: Handles patient my appointments functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in patients can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];

// Fetch patient's appointments with doctor details and caregiver count
$stmt = $pdo->prepare(
    "SELECT a.AppID, a.AppointmentDate, a.DurationMinutes, a.Status,
            a.DoctorRejectedAt, a.DoctorRejectionReason,
            d.DoctorID, d.FName AS DoctorName,
            (
                SELECT COUNT(*) FROM caregiver_patients cp
                WHERE cp.PatientID = a.PatientID AND cp.Status = 'Accepted'
            ) AS CaregiverCount
     FROM appointments a
     LEFT JOIN doctors d ON a.DoctorID = d.DoctorID
     WHERE a.PatientID = :pid
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':pid' => $patientID]);
$myAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$error = $_GET['error'] ?? '';
$success = trim((string)($_GET['success'] ?? ''));
$successMessage = '';
if ($success !== '') {
    if ($success === '1' || $success === 'reassigned') {
        $successMessage = 'Appointment rejected and sent for reassignment.';
    } elseif ($success === 'booked') {
        $successMessage = 'Appointment booked successfully.';
    } elseif ($success === 'approved') {
        $successMessage = 'Appointment approved and sent to doctor.';
    } else {
        $successMessage = $success;
    }
}
$backUrl = 'patient_dashboard.php';
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
elseif ($successMessage !== ''): ?>
        <div class="success"><?= htmlspecialchars($successMessage) ?></div>
    <?php
endif; ?>

    <?php
if ($myAppointments): ?>
    <table class="user-table">
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Appointment Date</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Rejection</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
foreach ($myAppointments as $row): ?>
            <?php
                $dateDisplay = date('d-m-Y H:i', strtotime($row['AppointmentDate']));
                $durationValue = (int)($row['DurationMinutes'] ?? 20);
            ?>
            <tr>
                <td>
                    <?= htmlspecialchars($row['DoctorName'] ?? 'Pending assignment') ?>
                    <?php if (!empty($row['DoctorID'])): ?>
                        <br><small><?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$row['DoctorID']] ?? null)) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($dateDisplay) ?></td>
                <td><?= htmlspecialchars($durationValue) ?> min</td>
                <td><?= htmlspecialchars(getAppointmentStatusLabel($row['Status'], $row['DoctorID'] ?? null, $row['DoctorRejectedAt'] ?? null)) ?></td>
                <td>
                    <?php
if (!empty($row['DoctorRejectedAt'])): ?>
                        <div>Rejected</div>
                        <?php
if (!empty($row['DoctorRejectionReason'])): ?>
                            <div><?= htmlspecialchars($row['DoctorRejectionReason']) ?></div>
                        <?php
endif; ?>
                    <?php
else: ?>
                        <span>-</span>
                    <?php
endif; ?>
                </td>
                <td>
                    <?php
if ((string)$row['Status'] === 'AwaitingPatientApproval' && !empty($row['DoctorID'])): ?>
                        <form method="post" action="patient_approve_appointment.php" class="inline-action-group">
                            <input type="hidden" name="appid" value="<?= $row['AppID'] ?>">
                            <button type="submit" class="btn btn-approve" onclick="return confirm('Approve this specialist appointment?')">
                                Approve
                            </button>
                        </form>
                    <?php
else: ?>
                        <a href="../appointments/appointment_chat.php?aid=<?= $row['AppID'] ?>" 
                           class="btn btn-view">Open Chat</a>
                        <?php
if ((int)$row['CaregiverCount'] > 0): ?>
                            <a href="../caregiver/caregiver_appointment_chat.php?aid=<?= $row['AppID'] ?>" 
                               class="btn btn-view">Caregiver Chat</a>
                        <?php
endif; ?>
                        <?php
if (!in_array((string)$row['Status'], ['Completed','Cancelled'], true) && !empty($row['DoctorID'])): ?>
                            <form method="post" action="patient_reject_appointment.php" class="inline-action-group">
                                <input type="hidden" name="appid" value="<?= $row['AppID'] ?>">
                                <input type="text" name="reason" data-capitalize="sentences" placeholder="Cancellation reason (optional)" size="20">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this appointment?')">
                                    Cancel
                                </button>
                            </form>
                        <?php
endif; ?>
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
            <h4>No appointments yet</h4>
            <p>Book an appointment to start your care journey.</p>
            <div class="empty-actions">
                <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>
                <a href="patient_book_appointments.php" class="btn btn-view">Book Appointment</a>
                <a href="patient_dashboard.php" class="btn btn-approve">Go to Dashboard</a>
            </div>
        </div>
    <?php
endif; ?>

</div>
</main>

<?php
include "../footer.php"; ?>



