<?php
session_start();
header("Content-Type: application/json");
require "include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized access"
    ]);
    exit;
}

$ig_name        = trim($_POST['ig_name'] ?? '');
$ig_description = trim($_POST['ig_description'] ?? '');
$team_id        = $_SESSION['team_id'];

if ($ig_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Organization name required"
    ]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO byteguess_category
    (ig_team_pkid, ig_name, ig_description, ig_status, createddate)
    VALUES (?, ?, ?, 1, NOW())
");

$stmt->bind_param("iss", $team_id, $ig_name, $ig_description);

if (!$stmt->execute()) {
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error
    ]);
    exit;
}

/* ===== CRITICAL SESSION SET ===== */
$_SESSION['ig_id'] = $stmt->insert_id;

/* Reset byteguess flow data */
$_SESSION['messages'] = [];
unset($_SESSION['cg_id']);

echo json_encode([
    "status" => "success",
    "message" => "Organization created successfully"
]);
