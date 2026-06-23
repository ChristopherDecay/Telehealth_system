<?php
// File overview: Handles patient process appointments functionality.
session_start();

/*
 * Legacy compatibility endpoint.
 * Normalizes old field names and routes to the canonical processor.
 */
if (isset($_POST['date']) && !isset($_POST['appointment_date'])) {
    $_POST['appointment_date'] = $_POST['date'];
}
if (isset($_POST['time']) && !isset($_POST['appointment_time'])) {
    $_POST['appointment_time'] = $_POST['time'];
}

require __DIR__ . "/patient_book_appointment_process.php";
