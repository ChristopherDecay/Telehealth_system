<?php
// File overview: Handles forgot-password lookup and reset using security questions.
session_start();
require "../db.php";
require "../functions.php";

$action = trim($_POST['action'] ?? '');
$validRoles = ['Patient', 'Caregiver', 'Doctor', 'Nurse', 'Labtech', 'Admin'];
ensureUserSecurityQuestionsTable($pdo);

if ($action === 'lookup') {
    $uname = trim($_POST['uname'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($uname === '' || $role === '') {
        header("Location: ../forgot_password.php?error=" . urlencode("Role and username are required"));
        exit;
    }
    if (!in_array($role, $validRoles, true) || !isAlnumSimple($uname)) {
        header("Location: ../forgot_password.php?error=" . urlencode("Invalid input"));
        exit;
    }

    $stmt = $pdo->prepare("SELECT UserID FROM users WHERE Uname = :un AND Role = :role LIMIT 1");
    $stmt->execute([
        ':un' => $uname,
        ':role' => $role
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: ../forgot_password.php?error=" . urlencode("No matching account found"));
        exit;
    }

    $qStmt = $pdo->prepare(
        "SELECT QuestionKey, AnswerHash
         FROM user_security_questions
         WHERE UserID = :uid"
    );
    $qStmt->execute([':uid' => $user['UserID']]);
    $rows = $qStmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) < 2) {
        header("Location: ../forgot_password.php?error=" . urlencode("Security questions are not set for this account"));
        exit;
    }

    $availableKeys = [];
    foreach ($rows as $r) {
        $availableKeys[] = $r['QuestionKey'];
    }
    $availableKeys = array_values(array_unique($availableKeys));
    if (count($availableKeys) < 2) {
        header("Location: ../forgot_password.php?error=" . urlencode("Not enough security questions available"));
        exit;
    }

    $pickedIndexes = array_rand($availableKeys, 2);
    if (!is_array($pickedIndexes)) {
        $pickedIndexes = [$pickedIndexes];
    }
    $q1Key = $availableKeys[$pickedIndexes[0]];
    $q2Key = $availableKeys[$pickedIndexes[1]];

    $questionMap = getSecurityQuestionsMap();
    if (!isset($questionMap[$q1Key], $questionMap[$q2Key])) {
        header("Location: ../forgot_password.php?error=" . urlencode("Security question configuration error"));
        exit;
    }

    $_SESSION['forgot_password_flow'] = [
        'user_id' => $user['UserID'],
        'role' => $role,
        'q1_key' => $q1Key,
        'q2_key' => $q2Key,
        'question1_text' => $questionMap[$q1Key],
        'question2_text' => $questionMap[$q2Key],
        'issued_at' => time()
    ];

    header("Location: ../forgot_password.php?info=" . urlencode("Answer both questions to reset your password"));
    exit;
}

if ($action === 'reset') {
    $flow = $_SESSION['forgot_password_flow'] ?? null;
    if (!is_array($flow) || !isset($flow['user_id'], $flow['q1_key'], $flow['q2_key'], $flow['issued_at'])) {
        header("Location: ../forgot_password.php?error=" . urlencode("Start the reset process again"));
        exit;
    }
    if ((time() - (int)$flow['issued_at']) > 900) {
        unset($_SESSION['forgot_password_flow']);
        header("Location: ../forgot_password.php?error=" . urlencode("Reset session expired. Try again"));
        exit;
    }

    $answer1 = trim($_POST['answer1'] ?? '');
    $answer2 = trim($_POST['answer2'] ?? '');
    $pwd = trim($_POST['pwd'] ?? '');
    $pwd2 = trim($_POST['pwd2'] ?? '');

    if ($answer1 === '' || $answer2 === '' || $pwd === '' || $pwd2 === '') {
        header("Location: ../forgot_password.php?error=" . urlencode("All fields are required"));
        exit;
    }
    if ($pwd !== $pwd2) {
        header("Location: ../forgot_password.php?error=" . urlencode("Passwords do not match"));
        exit;
    }
    $pwdCheck = isStrongPassword($pwd);
    if ($pwdCheck !== true) {
        header("Location: ../forgot_password.php?error=" . urlencode($pwdCheck));
        exit;
    }

    $qStmt = $pdo->prepare(
        "SELECT QuestionKey, AnswerHash
         FROM user_security_questions
         WHERE UserID = :uid
           AND (QuestionKey = :q1 OR QuestionKey = :q2)"
    );
    $qStmt->execute([
        ':uid' => $flow['user_id'],
        ':q1' => $flow['q1_key'],
        ':q2' => $flow['q2_key']
    ]);
    $rows = $qStmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) !== 2) {
        unset($_SESSION['forgot_password_flow']);
        header("Location: ../forgot_password.php?error=" . urlencode("Could not validate security questions"));
        exit;
    }

    $hashes = [];
    foreach ($rows as $r) {
        $hashes[$r['QuestionKey']] = $r['AnswerHash'];
    }

    $ans1Ok = isset($hashes[$flow['q1_key']])
        && password_verify(normalizeSecurityAnswer($answer1), $hashes[$flow['q1_key']]);
    $ans2Ok = isset($hashes[$flow['q2_key']])
        && password_verify(normalizeSecurityAnswer($answer2), $hashes[$flow['q2_key']]);
    if (!$ans1Ok || !$ans2Ok) {
        header("Location: ../forgot_password.php?error=" . urlencode("Security answers are incorrect"));
        exit;
    }

    $upd = $pdo->prepare("UPDATE users SET Passwd = :pw WHERE UserID = :uid");
    $upd->execute([
        ':pw' => password_hash($pwd, PASSWORD_DEFAULT),
        ':uid' => $flow['user_id']
    ]);

    unset($_SESSION['forgot_password_flow']);
    header("Location: ../login.php?error=" . urlencode("Password reset successful. Please login"));
    exit;
}

header("Location: ../forgot_password.php?error=" . urlencode("Invalid request"));
exit;
