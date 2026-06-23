<?php
// File overview: Handles caregiver request patient functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only logged-in caregivers can access this page
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Caregiver') {
    header("Location: ../login.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = trim($_POST['patient_identifier'] ?? '');
    $caregiverID = $_SESSION['user_id'];

    if (!isNonEmpty($input)) {
        $error = "Please enter Patient Username.";
    } else {

        // Validate patient existence by ID or username
        $stmt = $pdo->prepare(
            "SELECT p.PatientID
             FROM patients p
             JOIN users u ON p.PatientID = u.UserID
             WHERE p.PatientID = :val OR u.Uname = :val"
        );
        $stmt->execute([':val' => $input]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient) {
            $error = "Patient not found.";
        } elseif ($patient['PatientID'] === $caregiverID) {
            $error = "You cannot request yourself.";
        } else {

            // Check if a request already exists for this patient
            $stmt = $pdo->prepare(
                "SELECT Status
                 FROM caregiver_patients
                 WHERE CaregiverID = :cid AND PatientID = :pid"
            );
            $stmt->execute([
                ':cid' => $caregiverID,
                ':pid' => $patient['PatientID']
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $error = "A request already exists (Status: {$existing['Status']}).";
            } else {

                // Insert new caregiver-patient request with 'Pending' status
                $stmt = $pdo->prepare(
                    "INSERT INTO caregiver_patients (CaregiverID, PatientID)
                     VALUES (:cid, :pid)"
                );
                $stmt->execute([
                    ':cid' => $caregiverID,
                    ':pid' => $patient['PatientID']
                ]);

                $message = "Request sent successfully. Waiting for patient approval.";
            }
        }
    }
}

// Fetch caregiver's patient request history with patient details
$historyStmt = $pdo->prepare(
    "SELECT cp.PatientID, cp.Status, cp.RequestDate, cp.ResponseDate,
            u.Uname,
            COALESCE(p.FName, '') AS PatientName
     FROM caregiver_patients cp
     JOIN users u ON u.UserID = cp.PatientID
     LEFT JOIN patients p ON p.PatientID = cp.PatientID
     WHERE cp.CaregiverID = :cid
     ORDER BY cp.RequestDate DESC, cp.PatientID DESC"
);
$historyStmt->execute([':cid' => $_SESSION['user_id']]);
$requestHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

function formatDateTimeSafe($value) {
    if (!$value) return '-';
    $ts = strtotime((string)$value);
    if ($ts === false) return htmlspecialchars((string)$value);
    return date('d M Y, H:i', $ts);
}
$backUrl = 'caregiver_dashboard.php';
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>Request Patient</h2>
    <p>Enter the Patient Username to request care access.</p>

    <?php
if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php
endif; ?>

    <?php
if ($message): ?>
        <div class="success"><?= htmlspecialchars($message) ?></div>
    <?php
endif; ?>

    <form method="post" onsubmit="return validateCaregiverRequestForm(this);">

        <label>Patient Username</label>
        <input type="text" name="patient_identifier" placeholder="e.g. john_doe">

        <input type="submit" value="Send Request">
    </form>

    <h3>Request History</h3>
    <?php if (empty($requestHistory)): ?>
        <p>No requests yet.</p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Responded</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requestHistory as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['PatientName'] !== '' ? $row['PatientName'] : '-') ?></td>
                        <td><?= htmlspecialchars($row['Uname']) ?></td>
                        <td><?= htmlspecialchars($row['Status']) ?></td>
                        <td><?= formatDateTimeSafe($row['RequestDate'] ?? null) ?></td>
                        <td><?= formatDateTimeSafe($row['ResponseDate'] ?? null) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-view" onclick="if (window.history.length > 1) { window.history.back(); return false; }">&larr; Back</a>

</div>
</main>

<?php
include "../footer.php"; ?>


