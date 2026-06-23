<?php
// File overview: Handles notifications count functionality.
session_start();
require "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM notifications
     WHERE UserID = :uid AND Role = :role AND IsRead = 0"
);
$stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':role' => $_SESSION['role']
]);

echo json_encode(['count' => (int)$stmt->fetchColumn()]);
