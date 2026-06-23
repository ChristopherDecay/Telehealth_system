<?php
// File overview: Validates, stores, and records patient medical document uploads.
session_start();
require "../db.php";
require "../functions.php";

// Access control: only logged-in patients can upload their own documents.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];

if (!isset($_FILES['document']) || $_FILES['document']['error'] !== 0) {
    header("Location: patient_upload_documents.php?error=File upload failed");
    exit;
}

$file = $_FILES['document'];

if (!isAllowedExtension($file['name'], ['pdf','jpg','jpeg','png'])) {
    header("Location: patient_upload_documents.php?error=Invalid file type");
    exit;
}

// Enforce upload size limit.
if ($file['size'] > 5 * 1024 * 1024) {
    header("Location: patient_upload_documents.php?error=File too large (max 5MB)");
    exit;
}

// Ensure the medical document folder exists.
$uploadDir = "../uploads/medical_docs/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Store with a generated filename to avoid unsafe original filenames.
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = $patientID . "_" . time() . "." . strtolower($ext);
$destination = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    header("Location: patient_upload_documents.php?error=Unable to save file");
    exit;
}

// Save the uploaded document reference to the database.
$stmt = $pdo->prepare(
    "INSERT INTO documents
     (PatientID, CaregiverID, UploaderRole, FileName, FilePath, UploadedAt)
     VALUES (:pid, NULL, 'Patient', :fn, :fp, NOW())"
);

$stmt->execute([
    ':pid' => $patientID,
    ':fn'  => $file['name'],
    ':fp'  => $destination
]);

header("Location: patient_upload_documents.php?success=1");
exit;

