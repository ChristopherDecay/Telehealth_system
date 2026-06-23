<?php
// File overview: Handles db functionality.
// Database connection settings.
$host  = "localhost";
$db    = "telehealthDB";
$uname = "";
$pwd   = "";

try {
    // Create a PDO connection to MySQL using UTF-8 encoding.
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $uname,
        $pwd
    );

    // Throw exceptions for database errors instead of silent failures.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Show a basic connection error message and stop execution.
    echo "<h3>Database Connection Failed</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<a href='register.php'>Go back to Register</a>";
    exit;
}
?>
