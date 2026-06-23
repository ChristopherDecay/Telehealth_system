<?php
// File overview: Handles patient process caregiver request functionality.
session_start();
require "../db.php";

// Access control: Only logged-in patients can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_POST['request_id'], $_POST['action'])) {
    header("Location: patient_caregiver_requests.php?error=Invalid%20request");
    exit;
}

$requestID = $_POST['request_id'];
$action = $_POST['action'];
$patientID = $_SESSION['user_id'];

// Validate that the request exists and belongs to this patient, and is still pending or accepted
$stmt = $pdo->prepare(
    "SELECT CaregiverID
     FROM caregiver_patients
     WHERE ID = :id AND PatientID = :pid AND Status IN ('Pending','Accepted')"
);
$stmt->execute([
    ':id' => $requestID,
    ':pid' => $patientID
]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    header("Location: patient_caregiver_requests.php?error=Request%20not%20found");
    exit;
}

if ($action === 'accept') {

    /* Accept caregiver */
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "UPDATE caregiver_patients
         SET Status = 'Accepted', ResponseDate = NOW()
         WHERE ID = :id"
    );
    $stmt->execute([':id' => $requestID]);

    $pdo->commit();
    header("Location: patient_caregiver_requests.php?success=Request%20accepted");
    exit;

} elseif ($action === 'reject') {

    $stmt = $pdo->prepare(
        "UPDATE caregiver_patients
         SET Status = 'Rejected', ResponseDate = NOW()
         WHERE ID = :id"
    );
    $stmt->execute([':id' => $requestID]);
    header("Location: patient_caregiver_requests.php?success=Request%20rejected");
    exit;
}

header("Location: patient_caregiver_requests.php");
exit;
