<?php
header("Content-Type: application/json");
include("include/dataconnect.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}
 
$team_name  = trim($_POST["team_name"] ?? "");
$team_login = trim($_POST["team_login"] ?? "");
$password   = trim($_POST["team_password"] ?? "");
$confirm    = trim($_POST["confirm_password"] ?? "");

if ($password !== $confirm) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

$encoded_password = base64_encode($password);
$team_creatby = 1;

// Check email exists
$check = $conn->prepare("SELECT team_id FROM team WHERE team_login = ?");
$check->bind_param("s", $team_login);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit;
}

// Insert record
$stmt = $conn->prepare("
    INSERT INTO team 
    (team_name, team_login, team_password, team_creatby)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("sssi", $team_name, $team_login, $encoded_password, $team_creatby);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Signup successful"]);
} else {
    echo json_encode(["status" => "error", "message" => "Signup failed"]);
}
