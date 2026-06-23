<?php
// File overview: Allows caregivers to upload and review documents for accepted patients.
session_start();
require "../db.php";
require "../functions.php";

// Access control: only logged-in caregivers can access this page.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Caregiver') {
    header("Location: ../login.php");
    exit;
}

$caregiverID = $_SESSION['user_id'];

// Upload state shown above the caregiver form.
$uploadError = '';
$uploadSuccess = false;

// Process a caregiver document upload for an accepted patient.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientID = $_POST['patient_id'] ?? '';
    
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $uploadError = "File upload failed. Please choose a valid file.";
    } elseif (isNonEmpty($patientID)) {
        $file = $_FILES['document'];
        $allowed = ['pdf','jpg','jpeg','png','doc','docx'];

        // Confirm this caregiver is allowed to upload for the selected patient.
        $accessStmt = $pdo->prepare(
            "SELECT 1
             FROM caregiver_patients
             WHERE CaregiverID = :cid
               AND PatientID = :pid
               AND Status = 'Accepted'
             LIMIT 1"
        );
        $accessStmt->execute([
            ':cid' => $caregiverID,
            ':pid' => $patientID
        ]);

        if (!$accessStmt->fetchColumn()) {
            $uploadError = "Selected patient is not assigned to you.";
        } elseif (!isAllowedExtension($file['name'], $allowed)) {
            $uploadError = "File type not allowed. Only PDF, DOC, JPG, PNG allowed.";
        } elseif ($file['size'] > 10*1024*1024) {
            $uploadError = "File size exceeds 10MB limit.";
        } else {
            $uploadDir = dirname(__DIR__) . "/uploads/caregiver_docs/";
            $publicPath = "../uploads/caregiver_docs/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $ext = getFileExtension($file['name']);
            $filename = $patientID . "_" . time() . "." . $ext;
            $destination = $uploadDir . $filename;
            $filepath = $publicPath . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO documents (PatientID, CaregiverID, UploaderRole, FileName, FilePath)
                     VALUES (:pid, :cid, 'Caregiver', :fname, :fpath)"
                );
                $stmt->execute([
                    ':pid' => $patientID,
                    ':cid' => $caregiverID,
                    ':fname' => $file['name'],
                    ':fpath' => $filepath
                ]);
                $uploadSuccess = true;
            } else {
                $uploadError = "Error uploading file. Try again.";
            }
        }
    } else {
        $uploadError = "Please select a patient.";
    }
}

// Accepted patients available in the upload dropdown.
$stmt = $pdo->prepare(
    "SELECT p.PatientID, p.FName
     FROM caregiver_patients cp
     JOIN patients p ON cp.PatientID = p.PatientID
     WHERE cp.CaregiverID = :cid
       AND cp.Status = 'Accepted'"
);
$stmt->execute([':cid' => $caregiverID]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Previously uploaded caregiver documents, shown in the table below.
$stmt = $pdo->prepare(
    "SELECT d.*, p.FName
     FROM documents d
     JOIN patients p ON d.PatientID = p.PatientID
     WHERE d.CaregiverID = :cid
     ORDER BY d.UploadedAt DESC"
);
$stmt->execute([':cid' => $caregiverID]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>Upload Patient Documents</h2>

<?php
if($uploadError): ?>
    <div class="error"><?= htmlspecialchars($uploadError) ?></div>
<?php
elseif ($uploadSuccess): ?>
    <div class="success">Document uploaded successfully</div>
<?php
endif; ?>

<form method="post" enctype="multipart/form-data" onsubmit="return validateCaregiverUploadDocumentForm(this);">
    <label>Patient</label>
    <select name="patient_id">
        <option value="">-- Select Patient --</option>
        <?php
foreach ($patients as $p): ?>
            <option value="<?= $p['PatientID'] ?>"><?= htmlspecialchars($p['FName']) ?></option>
        <?php
endforeach; ?>
    </select>

    <label>Document (PDF, DOC, JPG, PNG - max 10MB)</label>
    <input type="file" name="document">

    <input type="submit" value="Upload Document">
</form>

<hr>

<h3>My Uploaded Documents</h3>

<?php
if (!$documents): ?>
    <p>No documents uploaded yet.</p>
<?php
else: ?>
    <table class="admin-table">
        <tr>
            <th>Patient</th>
            <th>Document</th>
            <th>Uploaded</th>
            <th>Action</th>
        </tr>
        <?php foreach ($documents as $doc): ?>
            <tr>
                <td><?= htmlspecialchars($doc['FName']) ?></td>
                <td><?= htmlspecialchars($doc['FileName']) ?></td>
                <td><?= htmlspecialchars($doc['UploadedAt']) ?></td>
                <td>
                    <a class="btn btn-view"
                       href="<?= htmlspecialchars($doc['FilePath']) ?>"
                       target="_blank">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php
endif; ?>

</div>
</main>

<?php
include "../footer.php"; ?>
