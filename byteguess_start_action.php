<?php
session_start();
header("Content-Type: application/json");
require "include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$team_id = $_SESSION['team_id'];

/* =========================
   CASE 1: EXISTING ORG
========================= */
if (isset($_POST['existing_ig_id']) && $_POST['existing_ig_id'] !== '') {

    $ig_id = intval($_POST['existing_ig_id']);

    $check = $conn->prepare("
        SELECT ig_id
        FROM byteguess_category
        WHERE ig_id = ? AND ig_team_pkid = ?
    ");
    $check->bind_param("ii", $ig_id, $team_id);
    $check->execute();

    if ($check->get_result()->num_rows === 0) {
        echo json_encode(["status"=>"error","message"=>"Invalid organization"]);
        exit;
    }

    $_SESSION['ig_id'] = $ig_id;

    // reset flow
    $_SESSION['messages'] = [];
    unset($_SESSION['cg_id']);

    echo json_encode(["status"=>"success"]);
    exit;
}

/* =========================
   CASE 2: CREATE NEW ORG
========================= */
$ig_name = trim($_POST['ig_name'] ?? '');
$ig_desc = trim($_POST['ig_description'] ?? '');

if ($ig_name === '') {
    echo json_encode(["status"=>"error","message"=>"Organization name required"]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO byteguess_category
    (ig_team_pkid, ig_name, ig_description, ig_status, createddate)
    VALUES (?, ?, ?, 1, NOW())
");
$stmt->bind_param("iss", $team_id, $ig_name, $ig_desc);

if (!$stmt->execute()) {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
    exit;
}

$_SESSION['ig_id'] = $stmt->insert_id;
$_SESSION['messages'] = [];
unset($_SESSION['cg_id']);

echo json_encode(["status"=>"success"]);
