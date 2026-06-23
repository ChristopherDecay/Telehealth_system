<?php
// File overview: Handles admin hospital process functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $name = trim($_POST['hospital_name'] ?? '');
    $loc = trim($_POST['location'] ?? '');
    $lic = trim($_POST['license'] ?? '');

    if ($name === '' || $loc === '' || $lic === '') {
        header("Location: admin_manage_hospitals.php?error=All fields are required");
        exit;
    }

    $maxNum = $pdo->query(
        "SELECT MAX(CAST(SUBSTRING(HospitalID, 4) AS UNSIGNED))
         FROM hospitals
         WHERE HospitalID LIKE 'HSP%'"
    )->fetchColumn();
    $nextNum = ((int)$maxNum) + 1;
    $id = sprintf('HSP%03d', $nextNum);

    $stmt = $pdo->prepare(
        "INSERT INTO hospitals (HospitalID, HospitalName, Location, KMPDCLicense)
         VALUES (:id, :name, :loc, :lic)"
    );
    $stmt->execute([
        ':id'   => $id,
        ':name' => $name,
        ':loc'  => $loc,
        ':lic'  => $lic
    ]);

    header("Location: admin_manage_hospitals.php?success=Hospital%20added%20successfully");
    exit;

} elseif ($action === 'delete') {
    $id = trim($_POST['hospital_id'] ?? '');
    if ($id === '') {
        header("Location: admin_manage_hospitals.php?error=Invalid hospital");
        exit;
    }

    // Prevent deleting hospitals that have doctors
    $check = $pdo->prepare("SELECT COUNT(*) FROM doctors WHERE HospitalID = :id");
    $check->execute([':id' => $id]);

    if ($check->fetchColumn() > 0) {
        header("Location: admin_manage_hospitals.php?error=Hospital in use");
        exit;
    }

    $pdo->prepare(
        "DELETE FROM hospitals WHERE HospitalID = :id"
    )->execute([':id' => $id]);
}

header("Location: admin_manage_hospitals.php");
exit;



