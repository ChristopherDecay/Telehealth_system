<?php
// File overview: Handles profile functionality.
// Require authenticated session and DB access for profile completion/editing.
session_start();
require "db.php";
require "functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['user_id'];
$role   = $_SESSION['role'];

// If profile is already complete, allow access only in explicit edit mode.
$stmt = $pdo->prepare("SELECT ProfileComplete FROM users WHERE UserID = :id");
$stmt->execute([':id'=>$userid]);
$profileComplete = (int)$stmt->fetchColumn() === 1;
$isEdit = isset($_GET['edit']) && $profileComplete;
if ($profileComplete && !$isEdit) {
    switch ($role) {
        case 'Admin':
            header("Location: admin/admin_dashboard.php");
            break;
        case 'Doctor':
            header("Location: doctor/doctor_dashboard.php");
            break;
        case 'Nurse':
            header("Location: nurse/nurse_dashboard.php");
            break;
        case 'Labtech':
            header("Location: labtech/labtech_dashboard.php");
            break;
        case 'Caregiver':
            header("Location: caregiver/caregiver_dashboard.php");
            break;
        case 'Patient':
            header("Location: patient/patient_dashboard.php");
            break;
        default:
            header("Location: login.php?error=Invalid role");
    }
    exit;
}

include "header.php";

// Explain access gating when redirected here.
$notice = '';
if (!$profileComplete && isset($_GET['reason']) && $_GET['reason'] === 'complete_profile') {
    $notice = 'Please complete your profile to access the rest of the system.';
}

// Load dropdown option data used by role-specific form sections.
$caregivers = $pdo->query(
    "SELECT CaregiverID, FName FROM caregivers ORDER BY FName"
)->fetchAll(PDO::FETCH_ASSOC);

$hospitals = $pdo->query(
    "SELECT HospitalID, HospitalName FROM hospitals ORDER BY HospitalName"
)->fetchAll(PDO::FETCH_ASSOC);

$labs = $pdo->query(
    "SELECT LabID, LabName FROM laboratories ORDER BY LabName"
)->fetchAll(PDO::FETCH_ASSOC);
$labRatings = getEntityRatingsMap($pdo, 'Lab');

$doctorSpecializations = $pdo->query(
    "SELECT DoctorSpecName FROM doctor_specializations ORDER BY DoctorSpecName"
)->fetchAll(PDO::FETCH_COLUMN);
$securityQuestions = getSecurityQuestionsMap();

// Default prefill values so the form renders safely in create and edit modes.
$prefill = [
    'fullname' => '',
    'dob' => '',
    'gender' => '',
    'idpp' => '',
    'phoneno' => '',
    'email' => '',
    'caregiver_id' => '',
    'workhrs' => '',
    'hospitalnm' => '',
    'kmpdc' => '',
    'specialization' => '',
    'experience' => '',
    'nck' => '',
    'labnm' => '',
    'kmlttb' => '',
    'nokname' => '',
    'nokphoneno' => '',
    'nokemail' => '',
    'shano' => '',
    'insurance' => '',
    'policy' => '',
    'allergens' => '',
    'majorsurgeries' => '',
    'chronicconditions' => '',
    'longtermmeds' => ''
];

// In edit mode, fetch existing profile details from role-specific tables.
if ($isEdit) {
    if ($role === 'Patient') {
        $stmt = $pdo->prepare(
            "SELECT p.*, h.Allergens, h.MajorSurgeries, h.ChronicConditions, h.LongTermMedications
             FROM patients p
             LEFT JOIN history h ON h.PatientID = p.PatientID
             WHERE p.PatientID = :id"
        );
        $stmt->execute([':id' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $prefill['fullname'] = $row['FName'] ?? '';
        $prefill['dob'] = isset($row['DOB']) ? ymdToDmy($row['DOB']) : '';
        $prefill['gender'] = $row['Gender'] ?? '';
        $prefill['idpp'] = $row['NatID_PP'] ?? '';
        $prefill['phoneno'] = $row['PhoneNum'] ?? '';
        $prefill['email'] = $row['Email'] ?? '';
        $prefill['nokname'] = $row['NOKName'] ?? '';
        $prefill['nokphoneno'] = $row['NOKPhoneNum'] ?? '';
        $prefill['nokemail'] = $row['NOKEmail'] ?? '';
        $prefill['shano'] = $row['SHAnum'] ?? '';
        $prefill['insurance'] = $row['InsuranceProvider'] ?? '';
        $prefill['policy'] = $row['PolicyNum'] ?? '';
        $prefill['allergens'] = $row['Allergens'] ?? '';
        $prefill['majorsurgeries'] = $row['MajorSurgeries'] ?? '';
        $prefill['chronicconditions'] = $row['ChronicConditions'] ?? '';
        $prefill['longtermmeds'] = $row['LongTermMedications'] ?? '';

        // Prefill latest accepted caregiver relationship, if available.
        $careStmt = $pdo->prepare(
            "SELECT CaregiverID
             FROM caregiver_patients
             WHERE PatientID = :pid AND Status = 'Accepted'
             ORDER BY ResponseDate DESC
             LIMIT 1"
        );
        $careStmt->execute([':pid' => $userid]);
        $prefill['caregiver_id'] = $careStmt->fetchColumn() ?: '';
    } elseif ($role === 'Caregiver') {
        $stmt = $pdo->prepare("SELECT * FROM caregivers WHERE CaregiverID = :id");
        $stmt->execute([':id' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $prefill['fullname'] = $row['FName'] ?? '';
        $prefill['dob'] = isset($row['DOB']) ? ymdToDmy($row['DOB']) : '';
        $prefill['gender'] = $row['Gender'] ?? '';
        $prefill['idpp'] = $row['NatID_PP'] ?? '';
        $prefill['phoneno'] = $row['PhoneNum'] ?? '';
        $prefill['email'] = $row['Email'] ?? '';
        $prefill['workhrs'] = $row['WorkHours'] ?? '';
    } elseif ($role === 'Doctor') {
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE DoctorID = :id");
        $stmt->execute([':id' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $prefill['fullname'] = $row['FName'] ?? '';
        $prefill['dob'] = isset($row['DOB']) ? ymdToDmy($row['DOB']) : '';
        $prefill['gender'] = $row['Gender'] ?? '';
        $prefill['idpp'] = $row['NatID_PP'] ?? '';
        $prefill['phoneno'] = $row['PhoneNum'] ?? '';
        $prefill['email'] = $row['Email'] ?? '';
        $prefill['hospitalnm'] = $row['HospitalID'] ?? '';
        $prefill['kmpdc'] = $row['LicenseNum'] ?? '';
        $prefill['specialization'] = $row['Specialization'] ?? '';
        $prefill['experience'] = $row['ExperienceYears'] ?? '';
    } elseif ($role === 'Nurse') {
        $stmt = $pdo->prepare("SELECT * FROM nurses WHERE NurseID = :id");
        $stmt->execute([':id' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $prefill['fullname'] = $row['FName'] ?? '';
        $prefill['dob'] = isset($row['DOB']) ? ymdToDmy($row['DOB']) : '';
        $prefill['gender'] = $row['Gender'] ?? '';
        $prefill['idpp'] = $row['NatID_PP'] ?? '';
        $prefill['phoneno'] = $row['PhoneNum'] ?? '';
        $prefill['email'] = $row['Email'] ?? '';
        $prefill['hospitalnm'] = $row['HospitalID'] ?? '';
        $prefill['nck'] = $row['LicenseNum'] ?? '';
    } elseif ($role === 'Labtech') {
        $stmt = $pdo->prepare("SELECT * FROM labtechs WHERE LabTechID = :id");
        $stmt->execute([':id' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $prefill['fullname'] = $row['FName'] ?? '';
        $prefill['dob'] = isset($row['DOB']) ? ymdToDmy($row['DOB']) : '';
        $prefill['gender'] = $row['Gender'] ?? '';
        $prefill['idpp'] = $row['NatID_PP'] ?? '';
        $prefill['phoneno'] = $row['PhoneNum'] ?? '';
        $prefill['email'] = $row['Email'] ?? '';
        $prefill['labnm'] = $row['LabID'] ?? '';
        $prefill['kmlttb'] = $row['KMLTTB_License'] ?? '';
    } elseif ($role === 'Admin') {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE AdminID = :id");
        $stmt->execute([':id' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $prefill['fullname'] = $row['FName'] ?? '';
        $prefill['dob'] = isset($row['DOB']) ? ymdToDmy($row['DOB']) : '';
        $prefill['gender'] = $row['Gender'] ?? '';
        $prefill['idpp'] = $row['NatID_PP'] ?? '';
        $prefill['phoneno'] = $row['PhoneNum'] ?? '';
        $prefill['email'] = $row['Email'] ?? '';
    }
}

?>

<div class="page-wrapper">
<main class="main-content">
<div class="profile-container">

<h2><?= $isEdit ? 'Edit Your Profile' : 'Complete Your Profile' ?> (<?= htmlspecialchars($role) ?>)</h2>
<?php if ($notice !== ''): ?>
    <div class="notice"><?= htmlspecialchars($notice) ?></div>
<?php endif; ?>

<!-- Single profile form; sections appear based on current user role. -->
<form action="process/profile_process.php" method="post" onsubmit="return validateProfileForm(this);">
<input type="hidden" name="role" value="<?= $role ?>">
<input type="hidden" name="is_edit" value="<?= $isEdit ? '1' : '0' ?>">

<h3>General Information</h3>

<label>Full Name</label>
<input type="text" name="fullname" data-capitalize="words" value="<?= htmlspecialchars($prefill['fullname']) ?>">

<label>Date of Birth (DD-MM-YYYY)</label>
<input type="text" name="dob" value="<?= htmlspecialchars($prefill['dob']) ?>">

<label>Gender</label>
<input type="radio" name="gender" value="Male" <?= $prefill['gender'] === 'Male' ? 'checked' : '' ?>> Male
<input type="radio" name="gender" value="Female" <?= $prefill['gender'] === 'Female' ? 'checked' : '' ?>> Female

<label>ID / Passport</label>
<input type="text" name="idpp" value="<?= htmlspecialchars($prefill['idpp']) ?>">

<label>Phone Number</label>
<input type="text" name="phoneno" value="<?= htmlspecialchars($prefill['phoneno']) ?>">

<label>Email</label>
<input type="text" name="email" value="<?= htmlspecialchars($prefill['email']) ?>">

<h3>Security Questions</h3>
<p class="hint-text">Leave all answers blank to keep your current security questions.</p>
<?php foreach ($securityQuestions as $qKey => $qText): ?>
    <label><?= htmlspecialchars($qText) ?></label>
    <input type="text" name="security_answers[<?= htmlspecialchars($qKey) ?>]" autocomplete="off">
<?php endforeach; ?>

<!-- Patient-only profile fields. -->
<?php
if ($role === "Patient"): ?>
<h3>Patient Details</h3>

<label>Caregiver (optional)</label>
<select name="caregiver_id">
    <option value="">-- Select Caregiver --</option>
    <?php
foreach ($caregivers as $c): ?>
        <option value="<?= $c['CaregiverID'] ?>" <?= $prefill['caregiver_id'] === $c['CaregiverID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['FName']) ?>
        </option>
    <?php
endforeach; ?>
</select>

<label>Next of Kin Name</label>
<input type="text" name="nokname" data-capitalize="words" value="<?= htmlspecialchars($prefill['nokname']) ?>">

<label>Next of Kin Phone</label>
<input type="text" name="nokphoneno" value="<?= htmlspecialchars($prefill['nokphoneno']) ?>">

<label>Next of Kin Email</label>
<input type="text" name="nokemail" value="<?= htmlspecialchars($prefill['nokemail']) ?>">

<label>SHA Number</label>
<input type="text" name="shano" value="<?= htmlspecialchars($prefill['shano']) ?>">

<label>Insurance Provider</label>
<input type="text" name="insurance" data-capitalize="words" value="<?= htmlspecialchars($prefill['insurance']) ?>">

<label>Policy Number</label>
<input type="text" name="policy" value="<?= htmlspecialchars($prefill['policy']) ?>">

<h3>Medical History (Optional)</h3>

<label>Allergens</label>
<textarea name="allergens" data-capitalize="sentences" rows="3"><?= htmlspecialchars($prefill['allergens']) ?></textarea>

<label>Major Surgeries</label>
<textarea name="majorsurgeries" data-capitalize="sentences" rows="3"><?= htmlspecialchars($prefill['majorsurgeries']) ?></textarea>

<label>Chronic Conditions</label>
<textarea name="chronicconditions" data-capitalize="sentences" rows="3"><?= htmlspecialchars($prefill['chronicconditions']) ?></textarea>

<label>Long Term Medications</label>
<textarea name="longtermmeds" data-capitalize="sentences" rows="3"><?= htmlspecialchars($prefill['longtermmeds']) ?></textarea>
<?php
endif; ?>

<!-- Caregiver-only profile fields. -->
<?php
if ($role === "Caregiver"): ?>
<h3>Caregiver Details</h3>
<label>Work Hours</label>
<input type="text" name="workhrs" data-capitalize="sentences" value="<?= htmlspecialchars($prefill['workhrs']) ?>">
<?php
endif; ?>

<!-- Doctor-only profile fields. -->
<?php
if ($role === "Doctor"): ?>
<h3>Doctor Details</h3>

<label>Hospital</label>
<select name="hospitalnm">
    <option value="">-- Select Hospital --</option>
    <?php
foreach ($hospitals as $h): ?>
        <option value="<?= $h['HospitalID'] ?>" <?= $prefill['hospitalnm'] === $h['HospitalID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($h['HospitalName']) ?>
        </option>
    <?php
endforeach; ?>
</select>

<label>KMPDC License</label>
<input type="text" name="kmpdc" value="<?= htmlspecialchars($prefill['kmpdc']) ?>">

<label>Specialization</label>
<select name="specialization">
    <option value="">-- Select Specialization --</option>
    <?php
foreach ($doctorSpecializations as $spec): ?>
        <option value="<?= htmlspecialchars($spec) ?>" <?= $prefill['specialization'] === $spec ? 'selected' : '' ?>>
            <?= htmlspecialchars($spec) ?>
        </option>
    <?php
endforeach; ?>
</select>

<label>Years of Experience</label>
<input type="text" name="experience" value="<?= htmlspecialchars((string)$prefill['experience']) ?>">
<?php
endif; ?>

<!-- Nurse-only profile fields. -->
<?php
if ($role === "Nurse"): ?>
<h3>Nurse Details</h3>

<label>Hospital</label>
<select name="hospitalnm">
    <option value="">-- Select Hospital --</option>
    <?php
foreach ($hospitals as $h): ?>
        <option value="<?= $h['HospitalID'] ?>" <?= $prefill['hospitalnm'] === $h['HospitalID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($h['HospitalName']) ?>
        </option>
    <?php
endforeach; ?>
</select>

<label>NCK License</label>
<input type="text" name="nck" value="<?= htmlspecialchars($prefill['nck']) ?>">
<?php
endif; ?>

<!-- Lab technician-only profile fields. -->
<?php
if ($role === "Labtech"): ?>
<h3>Labtech Details</h3>

<label>Lab</label>
<select name="labnm">
    <option value="">-- Select Lab --</option>
    <?php
foreach ($labs as $lab): ?>
        <option value="<?= $lab['LabID'] ?>" <?= $prefill['labnm'] === $lab['LabID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($lab['LabName']) ?> - Rating: <?= htmlspecialchars(formatEntityRatingLabel($labRatings[$lab['LabID']] ?? null)) ?>
        </option>
    <?php
endforeach; ?>
</select>

<label>KMLTTB License</label>
<input type="text" name="kmlttb" value="<?= htmlspecialchars($prefill['kmlttb']) ?>">
<?php
endif; ?>

<br><br>
<input type="submit" name="submit" value="<?= $isEdit ? 'Save Changes' : 'Submit Profile' ?>">

</form>
</div>
</main>
<?php
include "footer.php"; ?>
</div>
