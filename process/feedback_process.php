<?php
// File overview: Handles feedback process functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] === "Admin") {
    header("Location: ../login.php");
    exit;
}

$userID = $_SESSION['user_id'];
$feedback = trim($_POST['feedback'] ?? "");

if (!isNonEmpty($feedback)) {
    header("Location: ../feedback.php?error=Feedback cannot be empty.");
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO feedback (UserID, Feedback, FBDate)
     VALUES (:uid, :fb, NOW())"
);
$stmt->execute([
    ':uid' => $userID,
    ':fb' => $feedback
]);

header("Location: ../feedback.php?success=1");
exit;
