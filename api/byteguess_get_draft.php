<?php
session_start();
header("Content-Type: application/json");

require "../include/dataconnect.php";

$team_id = $_SESSION['team_id'] ?? 0;

if (!$team_id) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM byteguess_user_input
    WHERE ui_team_pkid = ?
      AND ui_cur_step < 6
    ORDER BY ui_id DESC
    LIMIT 1
");
$stmt->bind_param("i", $team_id);
$stmt->execute();

$draft = $stmt->get_result()->fetch_assoc();

if (!$draft) {
    echo json_encode(["status"=>"empty"]);
    exit;
}

echo json_encode([
    "status" => "success",
    "draft"  => $draft
]);
