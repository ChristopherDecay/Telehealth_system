<?php
// File overview: Handles logout functionality.
session_start();
require "db.php";
require "functions.php";

if (isset($_SESSION['user_id'])) {
    try {
        ensureLoginLogsTable($pdo);
        $stmt = $pdo->prepare(
            "UPDATE login_logs
             SET LogoutAt = NOW(),
                 SessionMinutes = TIMESTAMPDIFF(MINUTE, LoginAt, NOW())
             WHERE SessionID = :sid
               AND LogoutAt IS NULL
             ORDER BY LoginAt DESC
             LIMIT 1"
        );
        $stmt->execute([
            ':sid' => session_id()
        ]);
    } catch (Exception $e) {
        // Ignore logging issues on logout
    }
}

session_destroy();
header("Location: login.php");
exit;
