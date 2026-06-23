<?php
// File overview: Handles admin lab process functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $name = trim($_POST['lab_name'] ?? '');
    $loc = trim($_POST['lab_location'] ?? '');

    if ($name === '' || $loc === '') {
        header("Location: admin_manage_labs.php?error=All fields are required");
        exit;
    }

    $maxNum = $pdo->query(
        "SELECT MAX(CAST(SUBSTRING(LabID, 4) AS UNSIGNED))
         FROM laboratories
         WHERE LabID LIKE 'LAB%'"
    )->fetchColumn();
    $nextNum = ((int)$maxNum) + 1;
    $id = sprintf('LAB%03d', $nextNum);

    $pdo->prepare(
        "INSERT INTO laboratories (LabID, LabName, LabLocation)
         VALUES (:id, :name, :loc)"
    )->execute([
        ':id'   => $id,
        ':name' => $name,
        ':loc'  => $loc
    ]);

    header("Location: admin_manage_labs.php?success=Laboratory%20added%20successfully");
    exit;

} elseif ($action === 'delete') {
    $id = trim($_POST['lab_id'] ?? '');
    if ($id === '') {
        header("Location: admin_manage_labs.php?error=Invalid lab");
        exit;
    }

    // Prevent deleting labs with lab techs
    $check = $pdo->prepare("SELECT COUNT(*) FROM labtechs WHERE LabID = :id");
    $check->execute([':id' => $id]);

    if ($check->fetchColumn() > 0) {
        header("Location: admin_manage_labs.php?error=Lab in use");
        exit;
    }

    $pdo->prepare(
        "DELETE FROM laboratories WHERE LabID = :id"
    )->execute([':id' => $id]);
}

header("Location: admin_manage_labs.php");
exit;



