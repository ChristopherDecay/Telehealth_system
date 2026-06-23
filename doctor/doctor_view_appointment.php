<?php
// File overview: Handles doctor view appointment functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Doctor") {
    header("Location: ../login.php");
    exit;
}

$doctorID = $_SESSION['user_id'];
$appid = $_GET['id'] ?? '';

/* Fetch appointment */
$stmt = $pdo->prepare(
    "SELECT a.*, u.Uname AS PatientName
     FROM appointments a
     JOIN users u ON a.PatientID = u.UserID
     WHERE a.AppID = :id AND a.DoctorID = :did"
);
$stmt->execute([':id' => $appid, ':did' => $doctorID]);
$appt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appt) {
    echo "Appointment not found.";
    exit;
}

// If the patient cancelled this appointment, block access to their details.
$statusText = trim((string)$appt['Status']);
$statusLower = strtolower($statusText);
$cancelledByPatient = (strpos($statusLower, 'cancelled') !== false) || (strpos($statusLower, 'canceled') !== false);

/* Fetch patient vitals for this appointment */
$stmt = $pdo->prepare(
    "SELECT Temperature, BloodPressure, HeartRate, RespiratoryRate,
            OxygenSaturation, RecordedAt, UploadedByID, UploadedByRole,
            COALESCE(
                CASE UploadedByRole
                    WHEN 'Patient' THEN p.FName
                    WHEN 'Doctor' THEN d.FName
                    WHEN 'Nurse' THEN n.FName
                    WHEN 'Caregiver' THEN c.FName
                    WHEN 'Labtech' THEN l.FName
                    WHEN 'Admin' THEN a.FName
                    ELSE NULL
                END,
                u.Uname,
                UploadedByID
            ) AS UploadedByName
     FROM patient_vitals
     LEFT JOIN users u ON u.UserID = patient_vitals.UploadedByID
     LEFT JOIN patients p ON p.PatientID = patient_vitals.UploadedByID
     LEFT JOIN doctors d ON d.DoctorID = patient_vitals.UploadedByID
     LEFT JOIN nurses n ON n.NurseID = patient_vitals.UploadedByID
     LEFT JOIN caregivers c ON c.CaregiverID = patient_vitals.UploadedByID
     LEFT JOIN labtechs l ON l.LabTechID = patient_vitals.UploadedByID
     LEFT JOIN admin a ON a.AdminID = patient_vitals.UploadedByID
     WHERE AppID = :id
     ORDER BY RecordedAt DESC"
);
$stmt->execute([':id' => $appid]);
$vitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Fetch specialist specializations that have at least one doctor */
$specStmt = $pdo->query(
    "SELECT DISTINCT Specialization
     FROM doctors
     WHERE Specialization NOT IN ('General Practitioner','Family Medicine')
       AND Specialization IS NOT NULL
       AND TRIM(Specialization) <> ''
     ORDER BY Specialization ASC"
);
$specializations = $specStmt->fetchAll(PDO::FETCH_COLUMN);
$labs = $pdo->query("SELECT LabID, LabName FROM laboratories ORDER BY LabName")->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');
$error = $_GET['error'] ?? '';
$success = trim((string)($_GET['success'] ?? ''));
$backUrl = 'doctor_appointments.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>Appointment Details</h2>

<?php
if ($cancelledByPatient): ?>
    <div class="error">
        This appointment was cancelled by the patient. Details are not available.
    </div>
    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view"
       onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>
</div>
</main>
<?php
include "../footer.php"; ?>
<?php
exit;
endif; ?>

<?php
if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php
elseif ($success !== ''): ?>
    <div class="success">
        <?php
if (isset($_GET['rejected'])): ?>
            Appointment rejected and sent for reassignment.
        <?php
elseif ($success === 'labreq'): ?>
            Lab test request sent to patient for approval.
        <?php
else: ?>
            Specialist referral created.
        <?php
endif; ?>
    </div>
<?php
endif; ?>

<?php
$dateDisplay = date('d-m-Y', strtotime($appt['AppointmentDate'])); ?>
<div class="profile-row"><strong>Patient</strong><?= htmlspecialchars($appt['PatientName']) ?></div>
<div class="profile-row"><strong>Date</strong><?= htmlspecialchars($dateDisplay) ?></div>
<div class="profile-row"><strong>Time</strong><?= date('H:i', strtotime($appt['AppointmentDate'])) ?></div>
<div class="profile-row"><strong>Status</strong><?= htmlspecialchars(getAppointmentStatusLabel($appt['Status'], $appt['DoctorID'] ?? null, $appt['DoctorRejectedAt'] ?? null)) ?></div>
<br>

<hr>

<h3>Recorded Vitals</h3>
<?php
if ($vitals): ?>
    <table class="user-table">
        <thead>
            <tr>
                <th>Recorded At</th>
                <th>Temperature</th>
                <th>Blood Pressure</th>
                <th>Heart Rate</th>
                <th>Respiratory Rate</th>
                <th>Oxygen Saturation</th>
                <th>Uploader Role</th>
                <th>Uploaded By</th>
            </tr>
        </thead>
        <tbody>
            <?php
foreach ($vitals as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['RecordedAt']) ?></td>
                    <td><?= htmlspecialchars($v['Temperature'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['BloodPressure'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['HeartRate'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['RespiratoryRate'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['OxygenSaturation'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['UploadedByRole'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['UploadedByName'] ?? '-') ?></td>
                </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
<?php
else: ?>
    <p>No vitals recorded for this appointment.</p>
<?php
endif; ?>

<hr>

<h3 id="request-actions">Specialist Recommendation</h3>
<?php
if ($specializations): ?>
    <form method="post" action="doctor_recommend_specialist.php">
        <input type="hidden" name="appid" value="<?= $appid ?>">
        <label>Required Specialization</label>
        <select name="specialization">
            <option value="">-- Select Specialization --</option>
            <?php
foreach ($specializations as $spec): ?>
                <option value="<?= htmlspecialchars($spec) ?>">
                    <?= htmlspecialchars($spec) ?>
                </option>
            <?php
endforeach; ?>
        </select>

        <label>Date (DD-MM-YYYY)</label>
        <input type="text" name="appointment_date" placeholder="DD-MM-YYYY">

        <label>Time (HH:MM)</label>
        <input type="text" name="appointment_time" placeholder="HH:MM">

        <label>Duration (minutes)</label>
        <select name="duration_minutes">
            <option value="">-- Select Duration --</option>
            <?php
for ($m = 20; $m <= 120; $m += 10): ?>
                <option value="<?= $m ?>"><?= $m ?></option>
            <?php
endfor; ?>
        </select>

        <input type="submit" value="Create Specialist Referral" class="btn btn-approve">
    </form>
<?php
else: ?>
    <p>No specialist specializations available.</p>
<?php
endif; ?>

<hr>

<h3>Clinical Actions</h3>
<p class="dashboard-subtitle">Request lab tests for this patient. Patient approval is required before labtech sees the test.</p>

<form method="post" action="doctor_request_lab_test.php">
    <input type="hidden" name="appid" value="<?= htmlspecialchars($appid) ?>">

    <label>Laboratory</label>
    <select name="lab_id">
        <option value="">-- Select Laboratory --</option>
        <?php
foreach ($labs as $lab): ?>
            <option value="<?= htmlspecialchars($lab['LabID']) ?>">
                <?= htmlspecialchars($lab['LabName']) ?> - Rating: <?= htmlspecialchars(formatEntityRatingLabel($labRatings[$lab['LabID']] ?? null)) ?>
            </option>
        <?php
endforeach; ?>
    </select>

    <label>Test Name</label>
    <input type="text" name="test_name" data-capitalize="words" placeholder="e.g. Full Blood Count">

    <label>Request Note (optional)</label>
    <textarea name="request_note" rows="3" data-capitalize="sentences" placeholder="Why this test is needed"></textarea>

    <input type="submit" value="Send Lab Test Request" class="btn btn-approve">
</form>

<hr>
<a href="doctor_patient_history.php?pid=<?= $appt['PatientID'] ?>" 
   class="btn btn-view">
   View Full Medical History
</a>


<hr>

<form method="post" action="doctor_complete_appointment.php">
    <input type="hidden" name="appid" value="<?= $appid ?>">
    <input type="submit" class="btn-approve" value="Mark as Completed">
</form>

<?php
if (!in_array((string)$appt['Status'], ['Completed','Cancelled'], true)): ?>
    <br>
    <form method="post" action="doctor_reject_appointment.php" class="inline-action-group">
        <input type="hidden" name="appid" value="<?= $appid ?>">
        <input type="hidden" name="return_to_view" value="1">
        <input type="text" name="reason" data-capitalize="sentences" placeholder="Rejection reason (optional)" size="26">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this appointment? It will be reassigned.')">
            Reject and Reassign
        </button>
    </form>
<?php
endif; ?>

<br>
<a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>
