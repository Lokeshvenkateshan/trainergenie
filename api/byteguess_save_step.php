<?php
session_start();
header("Content-Type: application/json");

require "../include/dataconnect.php";

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($_SESSION['team_id'])) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid JSON input",
        "raw"=>$raw
    ]);
    exit;
}

$ui_id = intval($input['ui_id'] ?? 0);
$step  = intval($input['step'] ?? 0);
$data  = is_array($input['data'] ?? null) ? $input['data'] : [];

$team_id = intval($_SESSION['team_id']);

if ($step < 1 || $step > 6) {
    echo json_encode(["status"=>"error","message"=>"Invalid step"]);
    exit;
}

/* =========================
   STEP 1 – ORG + DRAFT
========================= */
if ($step === 1) {

    $org_id = intval($data['org_id'] ?? 0);
    $org_name = trim($data['org_name'] ?? '');
    $org_desc = trim($data['org_description'] ?? '');

    /* ================= EXISTING ORG ================= */
    if ($org_id) {

        $stmt = $conn->prepare("
            SELECT ig_description
            FROM byteguess_category
            WHERE ig_id = ? AND ig_team_pkid = ?
        ");
        $stmt->bind_param("ii", $org_id, $team_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            echo json_encode(["status"=>"error","message"=>"Invalid organization"]);
            exit;
        }

        $org_desc = $row['ig_description'];
    }

    /* ================= NEW ORG ================= */
    if (!$org_id) {

        $stmt = $conn->prepare("
            INSERT INTO byteguess_category
            (ig_team_pkid, ig_name, ig_description, ig_status, createddate)
            VALUES (?, ?, ?, 1, NOW())
        ");
        $stmt->bind_param("iss", $team_id, $org_name, $org_desc);
        $stmt->execute();
        $org_id = $stmt->insert_id;
    }

    /* ================= USER INPUT DRAFT ================= */
    $stmt = $conn->prepare("
        INSERT INTO byteguess_user_input
        (ui_team_pkid, ui_game_description, ui_cur_step)
        VALUES (?, ?, 1)
    ");
    $stmt->bind_param("is", $team_id, $org_desc);
    $stmt->execute();
    
    echo json_encode([
        "status" => "success",
        "ui_id"  => $stmt->insert_id
    ]);
    exit;
}


/* =========================
   REQUIRE ui_id AFTER STEP 1
========================= */
if ($ui_id <= 0) {
    echo json_encode(["status"=>"error","message"=>"ui_id missing"]);
    exit;
}

/* =========================
   STEP UPDATES
========================= */
$set = [];
$params = [];
$types = "";

switch ($step) {

    case 2:
        if (!isset($data['ui_game_name'], $data['ui_total_cards'], $data['ui_cards_drawn'])) {
            echo json_encode(["status"=>"error","message"=>"Missing game setup fields"]);
            exit;
        }

        $set[] = "ui_game_name = ?";
        $set[] = "ui_total_cards = ?";
        $set[] = "ui_cards_drawn = ?";

        $types .= "sii";
        $params[] = trim($data['ui_game_name']);
        $params[] = intval($data['ui_total_cards']);
        $params[] = intval($data['ui_cards_drawn']);
        break;

    case 3:
        $set[] = "ui_training_topic = ?";
        $set[] = "ui_industry = ?";
        $set[] = "ui_objective = ?";
        $set[] = "ui_hypothesis = ?";

        $types .= "ssss";
        $params[] = trim($data['ui_training_topic'] ?? '');
        $params[] = trim($data['ui_industry'] ?? '');
        $params[] = trim($data['ui_objective'] ?? '');
        $params[] = trim($data['ui_hypothesis'] ?? '');
        break;

    case 4:
        $set[] = "ui_card_structure = ?";
        $types .= "s";
        $params[] = trim($data['ui_card_structure'] ?? '');
        break;

    case 5:
        $set[] = "ui_options = ?";
        $types .= "s";
        $params[] = json_encode($data['ui_options'] ?? []);
        break;
}

/* Always update step */
$set[] = "ui_cur_step = ?";
$types .= "i";
$params[] = $step;

$sql = "
    UPDATE byteguess_user_input
    SET ".implode(", ", $set).",
        updated_at = NOW()
    WHERE ui_id = ? AND ui_team_pkid = ?
";

$types .= "ii";
$params[] = $ui_id;
$params[] = $team_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

echo json_encode(["status"=>"success"]);
