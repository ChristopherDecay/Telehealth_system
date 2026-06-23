<?php
// File overview: Lets patients upload and review their own medical documents.
session_start();
require "../db.php";

// Access control: only logged-in patients can access their documents.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== "Patient") {
    header("Location: ../login.php");
    exit;
}

$patientID = $_SESSION['user_id'];

// Patient documents shown in the table below.
$stmt = $pdo->prepare(
    "SELECT FileName, FilePath, UploadedAt
     FROM documents
     WHERE PatientID = :pid AND UploaderRole = 'Patient'
     ORDER BY UploadedAt DESC"
);
$stmt->execute([':pid' => $patientID]);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Upload Medical Documents</h2>

    <?php
if (isset($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php
endif; ?>

    <?php
if (isset($_GET['success'])): ?>
        <div class="success">Document uploaded successfully</div>
    <?php
endif; ?>

    <!-- Upload Form -->
    <form method="post" action="patient_upload_documents_process.php"
          enctype="multipart/form-data" onsubmit="return validateUploadDocumentForm(this);">

        <label>Document (PDF, JPG, PNG - max 5MB)</label>
        <input type="file" name="document">

        <input type="submit" value="Upload Document">
    </form>

    <hr>

    <h3>My Documents</h3>

    <?php
if (!$docs): ?>
        <p>No documents uploaded.</p>
    <?php
else: ?>
        <table class="admin-table">
            <tr>
                <th>Document</th>
                <th>Uploaded</th>
                <th>Action</th>
            </tr>

            <?php
foreach ($docs as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['FileName']) ?></td>
                <td><?= $d['UploadedAt'] ?></td>
                <td>
                    <a class="btn btn-view"
                       href="<?= htmlspecialchars($d['FilePath']) ?>"
                       target="_blank">View</a>
                </td>
            </tr>
            <?php
endforeach; ?>
        </table>
    <?php
endif; ?>

</div>
</main>

<?php
include "../footer.php"; ?>

