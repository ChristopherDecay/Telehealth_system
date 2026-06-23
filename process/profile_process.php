<?php
// File overview: Handles profile process functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

$userid = $_SESSION['user_id'];
$role   = $_SESSION['role'];

if (!isset($_POST['submit'])) {
    header("Location: ../profile.php");
    exit;
}

/* ---------- GENERAL INPUTS ---------- */
$fullname = trim($_POST['fullname']);
$dob      = trim($_POST['dob']);
$gender   = trim($_POST['gender']);
$idPP     = trim($_POST['idpp']);
$phoneno  = trim($_POST['phoneno']);
$email    = trim($_POST['email']);

/* ---------- ROLE-SPECIFIC ---------- */
$caregiverID   = $_POST['caregiver_id'] ?? null;
$workhrs       = $_POST['workhrs'] ?? null;
$hospitalID    = $_POST['hospitalnm'] ?? null;
$kmpdc         = $_POST['kmpdc'] ?? null;
$specialization= $_POST['specialization'] ?? null;
$experience    = $_POST['experience'] ?? null;
$nck           = $_POST['nck'] ?? null;
$labID         = $_POST['labnm'] ?? null;
$kmlttb        = $_POST['kmlttb'] ?? null;
$nokname       = $_POST['nokname'] ?? null;
$nokphoneno    = $_POST['nokphoneno'] ?? null;
$nokemail      = $_POST['nokemail'] ?? null;
$shano         = $_POST['shano'] ?? null;
$insurance     = $_POST['insurance'] ?? null;
$policy        = $_POST['policy'] ?? null;
$allergens      = $_POST['allergens'] ?? null;
$majorSurgeries = $_POST['majorsurgeries'] ?? null;
$chronicCond    = $_POST['chronicconditions'] ?? null;
$longTermMeds   = $_POST['longtermmeds'] ?? null;
$isEdit        = isset($_POST['is_edit']) && $_POST['is_edit'] === '1';
$securityAnswers = $_POST['security_answers'] ?? [];
$securityQuestions = getSecurityQuestionsMap();

$hasSecurityInput = false;
foreach ($securityQuestions as $qKey => $qText) {
    $value = trim((string)($securityAnswers[$qKey] ?? ''));
    if ($value !== '') {
        $hasSecurityInput = true;
        break;
    }
}

/* ---------- VALIDATION ---------- */
$errors = [];
if ($fullname === "" || !isAlphaSpace($fullname)) $errors[] = "Invalid full name.";
if ($dob === "" || !isRealisticDobDmy($dob)) $errors[] = "Invalid date of birth.";
if ($gender === "" || !in_array($gender, ['Male','Female'], true)) $errors[] = "Invalid gender.";
if ($idPP === "" || !isAlnumSimple($idPP)) $errors[] = "Invalid ID / Passport.";
if ($phoneno === "" || !isPhoneNumber($phoneno)) $errors[] = "Invalid phone number.";
if ($email === "" || !isEmailBasic($email)) $errors[] = "Invalid email.";

$dobDb = dmyToYmd($dob);
if ($dobDb === null) $errors[] = "Invalid date of birth.";

if ($role === "Patient") {
    if (trim((string)$nokname) === "" || !isAlphaSpace($nokname)) $errors[] = "Invalid Next of Kin name.";
    if (trim((string)$nokphoneno) === "" || !isPhoneNumber($nokphoneno)) $errors[] = "Invalid Next of Kin phone.";
    if (trim((string)$shano) === "" || !isShaNumber($shano)) $errors[] = "Invalid SHA number.";
}

if ($role === "Caregiver") {
    if (!isAtLeastAgeDmy($dob, 18)) $errors[] = "Caregivers must be at least 18 years old.";
    if (trim((string)$workhrs) === "") $errors[] = "Work hours required.";
}

if ($role === "Doctor" || $role === "Nurse") {
    if (trim((string)$hospitalID) === "") $errors[] = "Hospital required.";
    if ($role === "Doctor" && trim((string)$specialization) === "") $errors[] = "Specialization required.";
}

if ($role === "Doctor") {
    if (!isAtLeastAgeDmy($dob, 25)) $errors[] = "Doctors must be at least 25 years old.";
    if (trim((string)$kmpdc) === "") $errors[] = "KMPDC License required.";
    if (trim((string)$experience) !== "" && !isDigitsOnly($experience)) $errors[] = "Experience must be a number.";
}

if ($role === "Nurse") {
    if (!isAtLeastAgeDmy($dob, 21)) $errors[] = "Nurses must be at least 21 years old.";
    if (trim((string)$nck) === "") $errors[] = "NCK License required.";
}

if ($role === "Labtech") {
    if (!isAtLeastAgeDmy($dob, 21)) $errors[] = "Labtechs must be at least 21 years old.";
    if (trim((string)$labID) === "") $errors[] = "Lab required.";
    if (trim((string)$kmlttb) === "") $errors[] = "KMLTTB License required.";
}

if ($hasSecurityInput) {
    foreach ($securityQuestions as $qKey => $qText) {
        $value = trim((string)($securityAnswers[$qKey] ?? ''));
        if ($value === '') {
            $errors[] = "Answer all security questions.";
            break;
        }
    }
}

if (!empty($errors)) {
    echo implode("<br>", $errors);
    exit;
}

try {
    $pdo->beginTransaction();

    if ($role === "Patient") {
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE patients SET
                    FName = ?,
                    DOB = ?,
                    Gender = ?,
                    NatID_PP = ?,
                    PhoneNum = ?,
                    Email = ?,
                    NOKName = ?,
                    NOKPhoneNum = ?,
                    NOKEmail = ?,
                    SHAnum = ?,
                    InsuranceProvider = ?,
                    PolicyNum = ?
                 WHERE PatientID = ?"
            )->execute([
                $fullname,$dobDb,$gender,$idPP,$phoneno,$email,
                $nokname,$nokphoneno,$nokemail,$shano,$insurance,$policy,
                $userid
            ]);

            $histCheck = $pdo->prepare("SELECT 1 FROM history WHERE PatientID = ?");
            $histCheck->execute([$userid]);
            if ($histCheck->fetchColumn()) {
                $pdo->prepare(
                    "UPDATE history SET
                        Allergens = ?,
                        MajorSurgeries = ?,
                        ChronicConditions = ?,
                        LongTermMedications = ?
                     WHERE PatientID = ?"
                )->execute([
                    trim((string)$allergens) !== '' ? $allergens : null,
                    trim((string)$majorSurgeries) !== '' ? $majorSurgeries : null,
                    trim((string)$chronicCond) !== '' ? $chronicCond : null,
                    trim((string)$longTermMeds) !== '' ? $longTermMeds : null,
                    $userid
                ]);
            } else {
                $pdo->prepare(
                    "INSERT INTO history
                     (HistoryID, PatientID, Allergens, MajorSurgeries, ChronicConditions, LongTermMedications)
                     VALUES (?,?,?,?,?,?)"
                )->execute([
                    $userid,
                    $userid,
                    trim((string)$allergens) !== '' ? $allergens : null,
                    trim((string)$majorSurgeries) !== '' ? $majorSurgeries : null,
                    trim((string)$chronicCond) !== '' ? $chronicCond : null,
                    trim((string)$longTermMeds) !== '' ? $longTermMeds : null
                ]);
            }
        } else {
            $pdo->prepare(
                "INSERT INTO patients
                 (PatientID,FName,DOB,Gender,NatID_PP,PhoneNum,Email,
                  NOKName,NOKPhoneNum,NOKEmail,SHAnum,InsuranceProvider,PolicyNum)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $userid,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,
                $nokname,$nokphoneno,$nokemail,$shano,$insurance,$policy
            ]);

            $pdo->prepare(
                "INSERT INTO history
                 (HistoryID, PatientID, Allergens, MajorSurgeries, ChronicConditions, LongTermMedications)
                 VALUES (?,?,?,?,?,?)"
            )->execute([
                $userid,
                $userid,
                trim((string)$allergens) !== '' ? $allergens : null,
                trim((string)$majorSurgeries) !== '' ? $majorSurgeries : null,
                trim((string)$chronicCond) !== '' ? $chronicCond : null,
                trim((string)$longTermMeds) !== '' ? $longTermMeds : null
            ]);
        }

        if (!empty($caregiverID)) {
            $stmt = $pdo->prepare(
                "INSERT INTO caregiver_patients (CaregiverID, PatientID, Status)
                 VALUES (:cid, :pid, 'Pending')
                 ON DUPLICATE KEY UPDATE Status = 'Pending', ResponseDate = NULL"
            );
            $stmt->execute([
                ':cid' => $caregiverID,
                ':pid' => $userid
            ]);
        }
    }

    elseif ($role === "Caregiver") {
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE caregivers SET
                    FName = ?,
                    DOB = ?,
                    Gender = ?,
                    NatID_PP = ?,
                    PhoneNum = ?,
                    Email = ?,
                    WorkHours = ?
                 WHERE CaregiverID = ?"
            )->execute([
                $fullname,$dobDb,$gender,$idPP,$phoneno,$email,$workhrs,$userid
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO caregivers
                 (CaregiverID,FName,DOB,Gender,NatID_PP,PhoneNum,Email,WorkHours)
                 VALUES (?,?,?,?,?,?,?,?)"
            )->execute([
                $userid,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$workhrs
            ]);
        }
    }

    elseif ($role === "Doctor") {
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE doctors SET
                    HospitalID = ?,
                    FName = ?,
                    DOB = ?,
                    Gender = ?,
                    NatID_PP = ?,
                    PhoneNum = ?,
                    Email = ?,
                    LicenseNum = ?,
                    Specialization = ?,
                    ExperienceYears = ?
                 WHERE DoctorID = ?"
            )->execute([
                $hospitalID,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$kmpdc,$specialization,$experience,$userid
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO doctors
                 (DoctorID,HospitalID,FName,DOB,Gender,NatID_PP,PhoneNum,Email,LicenseNum,Specialization,ExperienceYears)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $userid,$hospitalID,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$kmpdc,$specialization,$experience
            ]);
        }
    }

    elseif ($role === "Nurse") {
        $specialization = 'Triage';
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE nurses SET
                    HospitalID = ?,
                    FName = ?,
                    DOB = ?,
                    Gender = ?,
                    NatID_PP = ?,
                    PhoneNum = ?,
                    Email = ?,
                    LicenseNum = ?
                 WHERE NurseID = ?"
            )->execute([
                $hospitalID,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$nck,$userid
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO nurses
                 (NurseID,HospitalID,FName,DOB,Gender,NatID_PP,PhoneNum,Email,LicenseNum,Specialization)
                 VALUES (?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $userid,$hospitalID,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$nck,$specialization
            ]);
        }
    }

    elseif ($role === "Labtech") {
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE labtechs SET
                    LabID = ?,
                    FName = ?,
                    DOB = ?,
                    Gender = ?,
                    NatID_PP = ?,
                    PhoneNum = ?,
                    Email = ?,
                    KMLTTB_License = ?
                 WHERE LabTechID = ?"
            )->execute([
                $labID,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$kmlttb,$userid
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO labtechs
                 (LabTechID,LabID,FName,DOB,Gender,NatID_PP,PhoneNum,Email,KMLTTB_License)
                 VALUES (?,?,?,?,?,?,?,?,?)"
            )->execute([
                $userid,$labID,$fullname,$dobDb,$gender,$idPP,$phoneno,$email,$kmlttb
            ]);
        }
    }

    elseif ($role === "Admin") {
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE admin SET
                    FName = ?,
                    DOB = ?,
                    Gender = ?,
                    NatID_PP = ?,
                    PhoneNum = ?,
                    Email = ?
                 WHERE AdminID = ?"
            )->execute([
                $fullname,$dobDb,$gender,$idPP,$phoneno,$email,$userid
            ]);
        } else {
            $pdo->prepare(
                "INSERT INTO admin
                 (AdminID,FName,DOB,Gender,NatID_PP,PhoneNum,Email)
                 VALUES (?,?,?,?,?,?,?)"
            )->execute([
                $userid,$fullname,$dobDb,$gender,$idPP,$phoneno,$email
            ]);
        }
    }

    if ($hasSecurityInput) {
        ensureUserSecurityQuestionsTable($pdo);
        $secStmt = $pdo->prepare(
            "INSERT INTO user_security_questions (UserID, QuestionKey, AnswerHash)
             VALUES (:uid, :qkey, :hash)
             ON DUPLICATE KEY UPDATE AnswerHash = VALUES(AnswerHash), UpdatedAt = CURRENT_TIMESTAMP"
        );
        foreach ($securityQuestions as $qKey => $qText) {
            $rawAnswer = (string)($securityAnswers[$qKey] ?? '');
            $normalized = normalizeSecurityAnswer($rawAnswer);
            $hash = password_hash($normalized, PASSWORD_DEFAULT);
            $secStmt->execute([
                ':uid' => $userid,
                ':qkey' => $qKey,
                ':hash' => $hash
            ]);
        }
    }

    if (!$isEdit) {
        $pdo->prepare(
            "UPDATE users SET ProfileComplete = 1 WHERE UserID = ?"
        )->execute([$userid]);
    }

    $pdo->commit();

    switch ($role) {
        case 'Admin':
            header("Location: ../admin/admin_dashboard.php");
            break;
        case 'Doctor':
            header("Location: ../doctor/doctor_dashboard.php");
            break;
        case 'Nurse':
            header("Location: ../nurse/nurse_dashboard.php");
            break;
        case 'Labtech':
            header("Location: ../labtech/labtech_dashboard.php");
            break;
        case 'Caregiver':
            header("Location: ../caregiver/caregiver_dashboard.php");
            break;
        case 'Patient':
            header("Location: ../patient/patient_dashboard.php");
            break;
        default:
            header("Location: ../login.php?error=Invalid role");
    }
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p class='form-error-text'>".$e->getMessage()."</p>";
}

