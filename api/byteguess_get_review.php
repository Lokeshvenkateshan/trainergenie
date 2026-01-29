<?php
session_start();
header("Content-Type: application/json");
require "../include/dataconnect.php";

$ui_id = intval($_GET['ui_id'] ?? 0);
$team_id = $_SESSION['team_id'] ?? 0;

if (!$team_id || !$ui_id) {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM byteguess_user_input WHERE ui_id = ? AND ui_team_pkid = ?");
$stmt->bind_param("ii", $ui_id, $team_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result);