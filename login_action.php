<?php
session_start();
header("Content-Type: application/json");
include("include/dataconnect.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$email    = trim($_POST["team_login"] ?? "");
$password = trim($_POST["team_password"] ?? "");

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit;
}

// Encode password using base64
$encoded_password = base64_encode($password);

$stmt = $conn->prepare("
    SELECT team_id, team_name 
    FROM team 
    WHERE team_login = ? 
      AND team_password = ?
      AND team_status = 1
");

$stmt->bind_param("ss", $email, $encoded_password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $row = $result->fetch_assoc();

    // Store session
    $_SESSION["team_id"]   = $row["team_id"];
    $_SESSION["team_name"] = $row["team_name"];

    echo json_encode([
        "status" => "success",
        "message" => "Login successful"
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email or password"
    ]);
}
