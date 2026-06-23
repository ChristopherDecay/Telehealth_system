<?php
// File overview: Handles caregiver chats functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only allow caregivers
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Caregiver') {
    header("Location: ../login.php");
    exit;
}

$caregiverID = $_SESSION['user_id'];

// Fetch appointments for patients under this caregiver (only accepted relationships), including doctor info and ratings.
$stmt = $pdo->prepare(
    "SELECT a.AppID, a.AppointmentDate,
            p.PatientID, p.FName AS PatientName,
            d.DoctorID, d.FName AS DoctorName
     FROM appointments a
     JOIN caregiver_patients cp ON cp.PatientID = a.PatientID
     JOIN patients p ON p.PatientID = a.PatientID
     LEFT JOIN doctors d ON d.DoctorID = a.DoctorID
     WHERE cp.CaregiverID = :cid
       AND cp.Status = 'Accepted'
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':cid' => $caregiverID]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$backUrl = 'caregiver_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

<h2>Caregiver Appointment Chats</h2>
<p class="dashboard-subtitle">Open a chat for a specific appointment.</p>

<table class="user-table">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Appointment Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($appointments): ?>
        <?php
foreach ($appointments as $row): ?>
            <?php
$dateDisplay = date('d-m-Y H:i', strtotime($row['AppointmentDate'])); ?>
            <tr>
                <td><?= htmlspecialchars($row['PatientName']) ?></td>
                <td>
                    <?= htmlspecialchars($row['DoctorName'] ?? 'Pending assignment') ?>
                    <?php if (!empty($row['DoctorID'])): ?>
                        <br><small><?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$row['DoctorID']] ?? null)) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($dateDisplay) ?></td>
                <td>
                    <a href="caregiver_appointment_chat.php?aid=<?= $row['AppID'] ?>" class="btn btn-view">
                        Open Chat
                    </a>
                </td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="4">No appointments found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<?php
if (!$appointments): ?>
    <div class="empty-state">
        <h4>No appointments to chat yet</h4>
        <p>Ask your patient to add you as a caregiver or wait for an assignment.</p>
        <div class="empty-actions">
            <a href="caregiver_dashboard.php" class="btn btn-view">Go to Dashboard</a>
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
