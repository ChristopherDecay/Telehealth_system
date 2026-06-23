<?php
// File overview: Handles send message functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Doctor','Patient','Nurse'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentID = $_POST['appointmentID'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if ($appointmentID && $message !== '') {
        $role = $_SESSION['role'];
        $userID = $_SESSION['user_id'];

        $ctx = getAppointmentChatContext($pdo, $appointmentID, $userID, $role);
        if (!$ctx['allowed']) {
            exit('Unauthorized');
        }

        $stmt = $pdo->prepare("
            INSERT INTO messages (AppID, SenderRole, SenderID, Message)
            VALUES (:aid, :role, :sid, :msg)
        ");
        $stmt->execute([
            ':aid' => $appointmentID,
            ':role' => $_SESSION['role'],
            ':sid' => $_SESSION['user_id'],
            ':msg' => $message
        ]);
        $appt = $ctx['appointment'];
        $notify = [
            ['id' => $appt['PatientID'], 'role' => 'Patient'],
            ['id' => $appt['DoctorID'], 'role' => 'Doctor'],
            ['id' => $appt['NurseID'], 'role' => 'Nurse']
        ];
        foreach ($notify as $n) {
            if (!$n['id'] || $n['id'] === $userID) continue;
            addNotification(
                $pdo,
                $n['id'],
                $n['role'],
                'New Chat Message',
                'You received a new message about your appointment.',
                "/Telehealth_system/appointments/appointment_chat.php?aid=$appointmentID"
            );
        }
        echo "Sent";
    }
}
