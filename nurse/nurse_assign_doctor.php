<?php
// File overview: Handles nurse assign doctor functionality.
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentID = $_POST['appid'] ?? '';
    $doctorID = trim($_POST['doctor_id'] ?? '');
    $date = trim($_POST['appointment_date'] ?? '');
    $time = trim($_POST['appointment_time'] ?? '');
    $duration = trim($_POST['duration_minutes'] ?? '');

    if ($appointmentID === '' || $doctorID === '' || $date === '' || $time === '' || $duration === '') {
        header("Location: nurse_assign_doctor.php?error=Please select a doctor, date, time, and duration.");
        exit;
    }

    if (!isDateDmy($date) || !isTimeHm($time)) {
        header("Location: nurse_assign_doctor.php?error=Invalid date or time format.");
        exit;
    }
    if (!isValidDurationMinutes($duration)) {
        header("Location: nurse_assign_doctor.php?error=Invalid duration. Use 20-120 minutes in 10-minute steps.");
        exit;
    }

    $dateDb = dmyToYmd($date);
    if ($dateDb === null) {
        header("Location: nurse_assign_doctor.php?error=Invalid date or time format.");
        exit;
    }
    if ($dateDb < date('Y-m-d')) {
        header("Location: nurse_assign_doctor.php?error=Appointment date cannot be in the past.");
        exit;
    }
    $appointmentDateTime = $dateDb . ' ' . $time . ':00';
    $durationMinutes = (int)$duration;
    $startDt = new DateTime($appointmentDateTime);
    $endDt = (clone $startDt)->modify('+' . $durationMinutes . ' minutes');
    $appointmentEnd = $endDt->format('Y-m-d H:i:s');
    if (!isAtLeastLeadTimeFromNow($appointmentDateTime, 60)) {
        header("Location: nurse_assign_doctor.php?error=Appointment must be at least 1 hour from now.");
        exit;
    }
    if (!isWithinHospitalHours($appointmentDateTime, $durationMinutes)) {
        header("Location: nurse_assign_doctor.php?error=Appointments are only allowed during hospital hours (08:00-17:00).");
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT AppointmentDate, PatientID, Status, ReasonCategory
         FROM appointments
         WHERE AppID = :aid AND NurseID = :nid"
    );
    $stmt->execute([
        ':aid' => $appointmentID,
        ':nid' => $nurseID
    ]);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appt) {
        header("Location: nurse_assign_doctor.php?error=Unauthorized appointment.");
        exit;
    }
    if (in_array((string)$appt['Status'], ['Completed','Cancelled'], true)) {
        header("Location: nurse_assign_doctor.php?error=Closed appointments cannot be reassigned.");
        exit;
    }

    $requiredCategory = trim((string)($appt['ReasonCategory'] ?? ''));
    $isSpecialistReferral = ($requiredCategory !== '' && strcasecmp($requiredCategory, 'General') !== 0);

    if ($isSpecialistReferral) {
        $specMatch = $pdo->prepare(
            "SELECT COUNT(*)
             FROM doctors
             WHERE DoctorID = :doc
               AND Specialization = :spec"
        );
        $specMatch->execute([
            ':doc' => $doctorID,
            ':spec' => $requiredCategory
        ]);
        if ((int)$specMatch->fetchColumn() <= 0) {
            header("Location: nurse_assign_doctor.php?error=Selected doctor does not match required specialization.");
            exit;
        }
    } else {
        $gpMatch = $pdo->prepare(
            "SELECT COUNT(*)
             FROM doctors
             WHERE DoctorID = :doc
               AND Specialization IN ('General Practitioner','Family Medicine')"
        );
        $gpMatch->execute([':doc' => $doctorID]);
        if ((int)$gpMatch->fetchColumn() <= 0) {
            header("Location: nurse_assign_doctor.php?error=Selected doctor must be a GP or Family Medicine doctor.");
            exit;
        }
    }

    $check = $pdo->prepare(
        "SELECT COUNT(*)
         FROM appointments
         WHERE DoctorID = :doc
           AND Status IN ('Pending','Confirmed','AwaitingPatientApproval')
           AND AppID <> :aid
           AND NOT (
               DATE_ADD(AppointmentDate, INTERVAL DurationMinutes MINUTE) <= :new_start
               OR AppointmentDate >= :new_end
           )"
    );
    $check->execute([
        ':doc' => $doctorID,
        ':aid' => $appointmentID,
        ':new_start' => $appointmentDateTime,
        ':new_end' => $appointmentEnd
    ]);

    if ($check->fetchColumn() > 0) {
        header("Location: nurse_assign_doctor.php?error=Doctor is already booked for this time.");
        exit;
    }

    /* Prevent patient overlap */
    $checkPatient = $pdo->prepare(
        "SELECT COUNT(*)
         FROM appointments
         WHERE PatientID = :pid
           AND Status IN ('Pending','Confirmed','AwaitingPatientApproval')
           AND AppID <> :aid
           AND NOT (
               DATE_ADD(AppointmentDate, INTERVAL DurationMinutes MINUTE) <= :new_start
               OR AppointmentDate >= :new_end
           )"
    );
    $checkPatient->execute([
        ':pid' => $appt['PatientID'],
        ':aid' => $appointmentID,
        ':new_start' => $appointmentDateTime,
        ':new_end' => $appointmentEnd
    ]);
    if ($checkPatient->fetchColumn() > 0) {
        header("Location: nurse_assign_doctor.php?error=Patient is already booked for this time.");
        exit;
    }

    /* Prevent nurse overlap */
    $checkNurse = $pdo->prepare(
        "SELECT COUNT(*)
         FROM appointments
         WHERE NurseID = :nid
           AND Status IN ('Pending','Confirmed','AwaitingPatientApproval')
           AND AppID <> :aid
           AND NOT (
               DATE_ADD(AppointmentDate, INTERVAL DurationMinutes MINUTE) <= :new_start
               OR AppointmentDate >= :new_end
           )"
    );
    $checkNurse->execute([
        ':nid' => $nurseID,
        ':aid' => $appointmentID,
        ':new_start' => $appointmentDateTime,
        ':new_end' => $appointmentEnd
    ]);
    if ($checkNurse->fetchColumn() > 0) {
        header("Location: nurse_assign_doctor.php?error=Nurse is already booked for this time.");
        exit;
    }

    $nextStatus = $isSpecialistReferral ? 'AwaitingPatientApproval' : 'Pending';

    $update = $pdo->prepare(
        "UPDATE appointments
         SET DoctorID = :doc,
             AppointmentDate = :appt,
             DurationMinutes = :dur,
             Status = :status
         WHERE AppID = :aid AND NurseID = :nid"
    );
    $update->execute([
        ':doc' => $doctorID,
        ':aid' => $appointmentID,
        ':nid' => $nurseID,
        ':appt' => $appointmentDateTime,
        ':dur' => $durationMinutes,
        ':status' => $nextStatus
    ]);

    if ($isSpecialistReferral) {
        addNotification(
            $pdo,
            $appt['PatientID'],
            'Patient',
            'Specialist Approval Needed',
            "Your specialist referral is ready for $date $time ($durationMinutes min). Please approve it.",
            "/Telehealth_system/patient/patient_my_appointments.php"
        );
    } else {
        addNotification(
            $pdo,
            $doctorID,
            'Doctor',
            'New Appointment Assigned',
            "You have a new appointment scheduled for $date $time ($durationMinutes min).",
            "/Telehealth_system/doctor/doctor_appointments.php"
        );

        addNotification(
            $pdo,
            $appt['PatientID'],
            'Patient',
            'Doctor Assigned',
            "Your appointment is scheduled for $date $time ($durationMinutes min).",
            "/Telehealth_system/patient/patient_my_appointments.php"
        );
    }

    header("Location: nurse_assign_doctor.php?success=1");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT a.AppID, a.AppointmentDate, a.DurationMinutes, a.ReasonCategory, a.ReasonText,
            a.DoctorRejectedAt, a.DoctorRejectionReason, a.Status,
            p.FName AS PatientName,
            d.FName AS DoctorName, d.DoctorID
     FROM appointments a
     JOIN patients p ON a.PatientID = p.PatientID
     LEFT JOIN doctors d ON a.DoctorID = d.DoctorID
     WHERE a.NurseID = :nid
     ORDER BY a.AppointmentDate DESC"
);
$stmt->execute([':nid' => $nurseID]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');

?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Assign Doctors</h2>
    <p class="dashboard-subtitle">Assign doctors based on appointment category or specialist referral.</p>

    <?php
if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php
elseif ($success): ?>
        <div class="success">
            <?= $success === 'rejected' ? 'Appointment rejected and reassigned successfully.' : 'Doctor assigned successfully.' ?>
        </div>
    <?php
endif; ?>

    <?php
if (!$appointments): ?>
        <p>No appointments assigned to you yet.</p>
    <?php
else: ?>
        <div class="assign-table-wrapper">
        <table class="user-table assign-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Reason</th>
                    <th>Rejection</th>
                    <th>Doctor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
foreach ($appointments as $a): ?>
                <?php
                    $dateDisplay = date('d-m-Y H:i', strtotime($a['AppointmentDate']));
                    $durationValue = (int)($a['DurationMinutes'] ?? 20);
                    $dateValue = date('d-m-Y', strtotime($a['AppointmentDate']));
                    $timeValue = date('H:i', strtotime($a['AppointmentDate']));
                    $requiredCategory = trim((string)($a['ReasonCategory'] ?? ''));
                    $isSpecialistReferral = ($requiredCategory !== '' && strcasecmp($requiredCategory, 'General') !== 0);
                    if ($isSpecialistReferral) {
                        $doctorListStmt = $pdo->prepare(
                            "SELECT d.DoctorID, d.FName, d.Specialization,
                                    COUNT(ap.AppID) AS ActiveAppointments
                             FROM doctors d
                             LEFT JOIN appointments ap
                                    ON ap.DoctorID = d.DoctorID
                                   AND ap.Status IN ('Pending','Confirmed','AwaitingPatientApproval')
                             WHERE d.Specialization = :spec
                             GROUP BY d.DoctorID, d.FName, d.Specialization
                             ORDER BY ActiveAppointments ASC, d.FName ASC"
                        );
                        $doctorListStmt->execute([':spec' => $requiredCategory]);
                    } else {
                        $doctorListStmt = $pdo->prepare(
                            "SELECT d.DoctorID, d.FName, d.Specialization,
                                    COUNT(ap.AppID) AS ActiveAppointments
                             FROM doctors d
                             LEFT JOIN appointments ap
                                    ON ap.DoctorID = d.DoctorID
                                   AND ap.Status IN ('Pending','Confirmed','AwaitingPatientApproval')
                             WHERE d.Specialization IN ('General Practitioner','Family Medicine')
                             GROUP BY d.DoctorID, d.FName, d.Specialization
                             ORDER BY ActiveAppointments ASC, d.FName ASC"
                        );
                        $doctorListStmt->execute();
                    }
                    $doctorList = $doctorListStmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <tr>
                    <td><?= htmlspecialchars($a['PatientName']) ?></td>
                    <td><?= htmlspecialchars($dateDisplay) ?></td>
                    <td><?= htmlspecialchars($durationValue) ?> min</td>
                    <td><?= htmlspecialchars(getAppointmentStatusLabel($a['Status'], $a['DoctorID'] ?? null, $a['DoctorRejectedAt'] ?? null)) ?></td>
                    <td><?= htmlspecialchars($a['ReasonCategory'] ?? '-') ?></td>
                    <td><?= nl2br(htmlspecialchars($a['ReasonText'] ?? '-')) ?></td>
                    <td>
                        <?php
if (!empty($a['DoctorRejectedAt'])): ?>
                            <div>Rejected</div>
                            <?php
if (!empty($a['DoctorRejectionReason'])): ?>
                                <div><?= htmlspecialchars($a['DoctorRejectionReason']) ?></div>
                            <?php
endif; ?>
                        <?php
else: ?>
                            <span>-</span>
                        <?php
endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($a['DoctorID'])): ?>
                            <?= htmlspecialchars($a['DoctorName'] ?? 'Unassigned') ?>
                            <br>
                            <small>Rating: <?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$a['DoctorID']] ?? null)) ?></small>
                        <?php else: ?>
                            <?= htmlspecialchars($a['DoctorName'] ?? 'Unassigned') ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
if (in_array((string)$a['Status'], ['Completed','Cancelled'], true)): ?>
                            <span>Closed</span>
                        <?php
elseif ($doctorList): ?>
                            <form method="post">
                                <input type="hidden" name="appid" value="<?= $a['AppID'] ?>">
                                <table class="user-table table-margin-reset">
                                    <thead>
                                        <tr>
                                            <th>Date (DD-MM-YYYY)</th>
                                            <th>Time (HH:MM)</th>
                                            <th>Duration (min)</th>
                                            <th>Doctor</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="appointment_date" value="<?= htmlspecialchars($dateValue) ?>" size="10" placeholder="DD-MM-YYYY">
                                            </td>
                                            <td>
                                                <input type="text" name="appointment_time" value="<?= htmlspecialchars($timeValue) ?>" size="5" placeholder="HH:MM">
                                            </td>
                                            <td>
                                                <select name="duration_minutes">
                                                    <?php
for ($m = 20; $m <= 120; $m += 10): ?>
                                                        <option value="<?= $m ?>" <?= $m === $durationValue ? 'selected' : '' ?>>
                                                            <?= $m ?>
                                                        </option>
                                                    <?php
endfor; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="doctor_id" class="assign-doctor-select">
                                                    <option value="">Select Doctor</option>
                                                    <?php
foreach ($doctorList as $d): ?>
                                                        <option value="<?= $d['DoctorID'] ?>" <?= $a['DoctorID'] === $d['DoctorID'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($d['FName']) ?> (<?= htmlspecialchars($d['Specialization']) ?>) - <?= (int)$d['ActiveAppointments'] ?> active - Rating: <?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$d['DoctorID']] ?? null)) ?>
                                                        </option>
                                                    <?php
endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-approve">
                                                    <?= $a['DoctorID'] ? 'Update' : 'Assign' ?>
                                                </button>
                                                <?php
if ($isSpecialistReferral): ?>
                                                    <div class="hint">Requires: <?= htmlspecialchars($requiredCategory) ?></div>
                                                <?php
endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        <?php
else: ?>
                            <span>No matching doctors</span>
                        <?php
endif; ?>
                        <?php
if (!in_array((string)$a['Status'], ['Completed','Cancelled'], true)): ?>
                            <form method="post" action="nurse_reject_appointment.php" class="inline-action-group">
                                <input type="hidden" name="appid" value="<?= $a['AppID'] ?>">
                                <input type="text" name="reason" data-capitalize="sentences" placeholder="Reassignment reason (optional)" size="22">
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
            </tbody>
        </table>
        </div>
    <?php
endif; ?>

</div>
</main>

<?php
include "../footer.php"; ?>




