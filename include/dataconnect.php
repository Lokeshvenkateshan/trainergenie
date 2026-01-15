<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trainer_genie";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed");
}
?>
