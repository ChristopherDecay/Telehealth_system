<?php
// File overview: Handles patient book appointments functionality.
session_start();
require "../db.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../login.php");
    exit;
}

?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

    <h2>&#128467; Book Appointment</h2>

    <?php
if (isset($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php
endif; ?>

    <form method="post" action="patient_book_appointment_process.php"
          onsubmit="return validateBookAppointmentForm(this) && confirm('Confirm booking this appointment?');">
        <p class="dashboard-subtitle">Booking hours: 08:00 to 17:00, and at least 1 hour in advance.</p>

        <label>Date (DD-MM-YYYY)</label>
        <input type="text" name="appointment_date" placeholder="DD-MM-YYYY">

        <label>Time (HH:MM)</label>
        <input type="text" name="appointment_time" placeholder="HH:MM">

        <label>Duration (minutes)</label>
        <select name="duration_minutes">
            <option value="">-- Select Duration --</option>
            <?php
for ($m = 20; $m <= 120; $m += 10): ?>
                <option value="<?= $m ?>"><?= $m ?></option>
            <?php
endfor; ?>
        </select>

        <label>Reason for Appointment</label>
        <textarea name="reason" rows="4" data-capitalize="sentences" placeholder="Briefly describe your concern..."></textarea>

        <input type="submit" value="Book Appointment" class="btn btn-approve">
    </form>

</div>
</main>

<?php
include "../footer.php"; ?>



