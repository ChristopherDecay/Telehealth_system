<?php
// File overview: Handles caregiver appointments functionality.
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
    "SELECT a.AppID, a.AppointmentDate, a.Status,
            p.PatientID, p.FName AS PatientName,
            d.DoctorID, d.FName AS DoctorName
     FROM appointments a
     JOIN patients p ON a.PatientID = p.PatientID
     JOIN caregiver_patients cp ON cp.PatientID = p.PatientID
     JOIN doctors d ON a.DoctorID = d.DoctorID
     WHERE cp.CaregiverID = :cid
       AND cp.Status = 'Accepted'
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':cid' => $caregiverID]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

<h2>Patient Appointments</h2>
<p class="dashboard-subtitle">View upcoming and past appointments of your patients</p>

<?php
if (!$appointments): ?>
    <p>No appointments found for your patients.</p>
<?php
else: ?>
    <table class="user-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date & Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
foreach ($appointments as $a): ?>
                <?php
$dateTimeDisplay = date('d-m-Y H:i', strtotime($a['AppointmentDate'])); ?>
                <tr>
                    <td><?= htmlspecialchars($a['PatientName']) ?></td>
                    <td>
                        <?= htmlspecialchars($a['DoctorName']) ?>
                        <br><small><?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$a['DoctorID']] ?? null)) ?></small>
                    </td>
                    <td><?= htmlspecialchars($dateTimeDisplay) ?></td>
                    <td>
                        <span class="status <?= strtolower($a['Status']) ?>">
                            <?= htmlspecialchars(getAppointmentStatusLabel($a['Status'], $a['DoctorID'] ?? null, null)) ?>
                        </span>
                    </td>
                </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
<?php
endif; ?>

<a href="caregiver_dashboard.php" class="btn btn-view">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>
