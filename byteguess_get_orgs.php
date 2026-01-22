<?php
session_start();
header("Content-Type: application/json");
require "include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT ig_id, ig_name
    FROM byteguess_category
    WHERE ig_team_pkid = ?
      AND ig_status = 1
");
$stmt->bind_param("i", $_SESSION['team_id']);
$stmt->execute();

$res = $stmt->get_result();
$out = [];
while ($r = $res->fetch_assoc()) $out[] = $r;

echo json_encode($out);
