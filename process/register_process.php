<?php
// File overview: Handles register process functionality.
session_start();
require "../db.php";
require "../functions.php";

$errors = [];

$uname = trim($_POST['uname'] ?? "");
$pwd   = trim($_POST['pwd'] ?? "");
$pwd2  = trim($_POST['pwd2'] ?? "");
$role  = trim($_POST['role'] ?? "");
$captcha = trim($_POST['captcha'] ?? "");
$expectedCaptcha = $_SESSION['register_captcha_answer'] ?? null;
unset($_SESSION['register_captcha_answer']);
$securityAnswers = $_POST['security_answers'] ?? [];
$securityQuestions = getSecurityQuestionsMap();

// Basic validation
if ($role == "")  $errors[] = "Role required";
if ($uname == "") $errors[] = "Username required";

if ($pwd == "") {
    $errors[] = "Password required";
} else {
    $pwdCheck = isStrongPassword($pwd);
    if ($pwdCheck !== true) {
        $errors[] = $pwdCheck;
    }
}

if ($pwd2 == "") {
    $errors[] = "Confirm your password";
} elseif ($pwd !== $pwd2) {
    $errors[] = "Passwords do not match";
}

if ($captcha === "") {
    $errors[] = "CAPTCHA answer is required";
} elseif ($expectedCaptcha === null || !hash_equals((string)$expectedCaptcha, $captcha)) {
    $errors[] = "Invalid CAPTCHA answer";
}

foreach ($securityQuestions as $qKey => $qText) {
    $rawAnswer = trim((string)($securityAnswers[$qKey] ?? ""));
    if ($rawAnswer === "") {
        $errors[] = "Answer all security questions";
        break;
    }
}

if (!empty($errors)) {
    header("Location: ../register.php?error=" . urlencode(implode(" ", $errors)));
    exit;
}

$validRoles = ['Patient','Caregiver','Doctor','Nurse','Labtech','Admin'];
if (!in_array($role, $validRoles, true)) {
    die("Invalid role");
}
if (!isAlnumSimple($uname)) {
    die("Username must be letters and numbers only");
}

// role-based status assignment: Patients and Caregivers are active immediately, others require admin approval
$activeRoles = ['Patient', 'Caregiver'];
$status = in_array($role, $activeRoles) ? 'Active' : 'Pending';

// Auto-activate only the very first Admin account
if ($role === 'Admin') {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Admin'");
    $adminCount = (int)$stmt->fetchColumn();
    if ($adminCount === 0) {
        $status = 'Active';
    }
}

// create user ID with collision check
try {
    $userid = generateID($pdo, $role); // Safe, collision-free
} catch (Exception $e) {
    die("Error generating user ID: " . $e->getMessage());
}

$hash = password_hash($pwd, PASSWORD_DEFAULT);
ensureUserSecurityQuestionsTable($pdo);

$sql = "INSERT INTO users 
        (UserID, Uname, Passwd, Role, RegDate, Status, ProfileComplete)
        VALUES (:id, :un, :pw, :role, NOW(), :status, 0)";

$stmt = $pdo->prepare($sql);

try {
    $pdo->beginTransaction();
    $stmt->execute([
        ':id'     => $userid,
        ':un'     => $uname,
        ':pw'     => $hash,
        ':role'   => $role,
        ':status' => $status
    ]);

    $sqStmt = $pdo->prepare(
        "INSERT INTO user_security_questions (UserID, QuestionKey, AnswerHash)
         VALUES (:uid, :qkey, :ahash)"
    );
    foreach ($securityQuestions as $qKey => $qText) {
        $normalized = normalizeSecurityAnswer($securityAnswers[$qKey] ?? "");
        $sqStmt->execute([
            ':uid' => $userid,
            ':qkey' => $qKey,
            ':ahash' => password_hash($normalized, PASSWORD_DEFAULT)
        ]);
    }
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database error: " . $e->getMessage());
}

// redirection to profile page after registration, where they can complete their profile
header("Location: ../profile.php");
exit;
