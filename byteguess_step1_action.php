<?php
session_start();
header("Content-Type: application/json");
require "include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$existingIg = intval($input['ig_id'] ?? 0);
$name = trim($input['ig_name'] ?? '');
$desc = trim($input['ig_description'] ?? '');

if ($existingIg > 0) {
    // Use existing organization
    $_SESSION['ig_id'] = $existingIg;

} else {
    if ($name === '') {
        echo json_encode(["status"=>"error","message"=>"Organization name required"]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO byteguess_category
        (ig_team_pkid, ig_name, ig_description, ig_status, createddate)
        VALUES (?, ?, ?, 1, NOW())
    ");
    $stmt->bind_param("iss", $_SESSION['team_id'], $name, $desc);
    $stmt->execute();

    $_SESSION['ig_id'] = $stmt->insert_id;
}

/* RESET GAME FLOW */
unset($_SESSION['cg_id'], $_SESSION['c'], $_SESSION['d']);
$_SESSION['messages'] = [];
$_SESSION['ai_log'] = [];

echo json_encode(["status"=>"success"]);
