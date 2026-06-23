<?php
// File overview: Lets lab technicians upload result files for assigned lab tests.
session_start();
require "../db.php";
require "../functions.php";

// Access control: only logged-in lab technicians can access this page.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Labtech') {
    header("Location: ../login.php");
    exit;
}
ensureLabTestApprovalColumns($pdo);

// Request label: identify the lab test being updated.
if (!isset($_GET['testid'])) {
    header("Location: labtech_lab_tests.php");
    exit;
}

$testID = $_GET['testid'];

// Resolve the laboratory assigned to the current lab technician.
$stmtLab = $pdo->prepare(
    "SELECT LabID FROM labtechs WHERE LabTechID = :id"
);
$stmtLab->execute([':id' => $_SESSION['user_id']]);
$lab = $stmtLab->fetch(PDO::FETCH_ASSOC);

if (!$lab) {
    echo "<p>Lab not found for this lab technician.</p>";
    exit;
}

$labID = $lab['LabID'];

// Fetch lab test and patient details for the result upload screen.
$stmt = $pdo->prepare(
    "SELECT lt.LabTestID, lt.PatientID, lt.TestName, lt.Status, lt.Result, lt.TestDate,
            p.FName, p.DOB, p.Gender
     FROM lab_tests lt
     JOIN patients p ON lt.PatientID = p.PatientID
     WHERE lt.LabTestID = :testid
       AND lt.LabID = :labid
       AND COALESCE(lt.PatientApprovalStatus, 'Accepted') = 'Accepted'"
);
$stmt->execute([':testid' => $testID, ':labid' => $labID]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$test) {
    echo "<p>Lab test not found or not assigned to you.</p>";
    exit;
}

$dobDisplay = ymdToDmy($test['DOB']) ?? $test['DOB'];

// Validate, store, and attach the uploaded result file.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resultfile'])) {

    $file = $_FILES['resultfile'];
    $uploadDir = dirname(__DIR__) . "/uploads/lab_results/";
    $publicPath = "../uploads/lab_results/";
    $allowed = ['pdf','jpg','jpeg','png'];

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "File upload failed. Please choose a valid file.";
    } elseif (!isAllowedExtension($file['name'], $allowed)) {
        $error = "File type not allowed. Only PDF/JPG/PNG allowed.";
    } elseif ($file['size'] > 10 * 1024 * 1024) {
        $error = "File size exceeds 10MB limit.";
    } else {
        $ext = getFileExtension($file['name']);
        $filename = $testID . "_" . time() . "." . $ext;
        $targetFile = $uploadDir . $filename;
        $resultPath = $publicPath . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            $error = "Failed to upload file. Please try again.";
        }
    }

    if (!isset($error)) {
        ensureLabTestResultDateColumn($pdo);
        $stmt = $pdo->prepare(
            "UPDATE lab_tests 
             SET Result = :result, Status = 'Completed', ResultDate = NOW()
             WHERE LabTestID = :testid"
        );
        $stmt->execute([':result' => $resultPath, ':testid' => $testID]);

        header("Location: labtech_lab_tests.php");
        exit;
    }
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Update Lab Test Result</h2>

    <div class="profile-row"><strong>Test ID</strong><?= $test['LabTestID'] ?></div>
    <div class="profile-row"><strong>Patient Name</strong><?= htmlspecialchars($test['FName']) ?></div>
    <div class="profile-row"><strong>DOB</strong><?= htmlspecialchars($dobDisplay) ?></div>
    <div class="profile-row"><strong>Gender</strong><?= $test['Gender'] ?></div>
    <div class="profile-row"><strong>Test Name</strong><?= htmlspecialchars($test['TestName']) ?></div>
    <div class="profile-row"><strong>Status</strong><?= $test['Status'] ?></div>

    <hr>

    <?php
if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php
endif; ?>

    <form method="post" enctype="multipart/form-data" onsubmit="return validateUploadDocumentForm(this);">
        <label>Result File (PDF, JPG, PNG - max 10MB)</label>
        <input type="file" name="resultfile">
        <input type="submit" value="Upload Result">
    </form>

    <br>
    <a href="labtech_lab_tests.php" class="btn btn-view">Back to Lab Tests</a>

</div>
</main>

<?php
include "../footer.php"; ?>




