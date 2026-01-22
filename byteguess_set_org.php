<?php
session_start();
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$ig_id = intval($input['ig_id'] ?? 0);

if ($ig_id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid org"]);
    exit;
}

$_SESSION['ig_id'] = $ig_id;
$_SESSION['messages'] = [];
unset($_SESSION['cg_id'], $_SESSION['c'], $_SESSION['d']);

echo json_encode(["status"=>"success"]);
 