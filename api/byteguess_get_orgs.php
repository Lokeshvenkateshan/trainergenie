<?php
session_start();
header("Content-Type: application/json");

require "../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    echo json_encode([]);
    exit;
}

$team_id = $_SESSION['team_id'];

$stmt = $conn->prepare("
    SELECT ig_id, ig_name,
    FROM byteguess_category
    WHERE ig_team_pkid = ? AND ig_status = 1
    ORDER BY ig_name
");
$stmt->bind_param("i", $team_id);
$stmt->execute();

$result = $stmt->get_result();
$orgs = [];

while ($row = $result->fetch_assoc()) {
    $orgs[] = $row;
}

echo json_encode($orgs);
