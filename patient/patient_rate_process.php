<?php
// File overview: Validates and saves patient ratings for doctors and laboratories.
session_start();
require "../db.php";
require "../functions.php";

// Access control: only logged-in patients can submit ratings.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];
$type      = $_POST['type'] ?? "";
$entityID  = $_POST['entity_id'] ?? "";
$rating    = trim($_POST['rating'] ?? "");

$returnPage = $type === 'lab' ? 'patient_rate_lab.php' : 'patient_rate_doctor.php';

// Validate required rating fields before checking service history.
if ($entityID == "" || $rating == "") {
    header("Location: " . $returnPage . "?error=Please%20select%20an%20item%20and%20enter%20a%20rating.");
    exit;
}

if (!is_numeric($rating)) {
    header("Location: " . $returnPage . "?error=Rating%20must%20be%20a%20number.");
    exit;
}

$ratingVal = (float)$rating;
if ($ratingVal < 1 || $ratingVal > 5) {
    header("Location: " . $returnPage . "?error=Rating%20must%20be%20between%201%20and%205.");
    exit;
}

$entityTypeMap = [
    'doctor' => 'Doctor',
    'lab' => 'Lab'
];

// Normalize request type to the database entity labels.
if (!isset($entityTypeMap[$type])) {
    header("Location: patient_ratings.php?error=Invalid%20rating%20type.");
    exit;
}

// Ensure the patient has used this doctor or lab before accepting the rating.
if ($type === 'doctor') {
    $check = $pdo->prepare(
        "SELECT 1
         FROM appointments
         WHERE PatientID = :pid
           AND DoctorID = :did
           AND Status = 'Completed'
         LIMIT 1"
    );
    $check->execute([
        ':pid' => $patientID,
        ':did' => $entityID
    ]);
    if (!$check->fetchColumn()) {
        header("Location: patient_rate_doctor.php?error=You%20can%20only%20rate%20doctors%20you%20have%20used.");
        exit;
    }
} elseif ($type === 'lab') {
    $check = $pdo->prepare(
        "SELECT 1
         FROM lab_tests
         WHERE PatientID = :pid
           AND LabID = :lid
           AND (Status = 'Completed' OR Result IS NOT NULL)
         LIMIT 1"
    );
    $check->execute([
        ':pid' => $patientID,
        ':lid' => $entityID
    ]);
    if (!$check->fetchColumn()) {
        header("Location: patient_rate_lab.php?error=You%20can%20only%20rate%20labs%20you%20have%20used.");
        exit;
    }
}

$stmt = $pdo->prepare(
    "INSERT INTO ratings
     (UserID, EntityType, EntityID, RatingValue, RatingDate)
     VALUES (:uid, :etype, :eid, :r, NOW())"
);

$stmt->execute([
    ':uid' => $patientID,
    ':etype' => $entityTypeMap[$type],
    ':eid' => $entityID,
    ':r'   => $rating
]);

header("Location: patient_ratings.php");
exit;



