<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trainer_genie";
$port = 3306;   // semicolon added, integer value

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
