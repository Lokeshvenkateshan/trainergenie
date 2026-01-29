<?php
session_start();
header("Content-Type: application/json");
require "../include/dataconnect.php";

$team_id = $_SESSION['team_id'] ?? 0;
$input = json_decode(file_get_contents("php://input"), true);

$ui_id = intval($input['ui_id'] ?? 0);
$step  = intval($input['step'] ?? 0);
$data  = $input['data'] ?? [];

if (!$team_id) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

/* --- STEP 1: INSERT --- */
if ($step === 1) {
    if (empty($data['ui_game_name']) || empty($data['ui_total_cards'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO byteguess_user_input 
        (ui_team_pkid, ui_game_name, ui_game_description, ui_total_cards, ui_cards_drawn, ui_card_structure, ui_cur_step) 
        VALUES (?, ?, ?, ?, ?, ?, 1)");
    
    $stmt->bind_param("issiis", 
        $team_id, 
        $data['ui_game_name'], 
        $data['ui_game_description'], 
        $data['ui_total_cards'], 
        $data['ui_cards_drawn'], 
        $data['ui_card_structure']
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "ui_id" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB Error"]);
    }
    exit;
}

/* --- STEPS 2 & 3: UPDATE --- */
if ($ui_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    exit;
}

$map = [
    2 => ["ui_training_topic", "ui_industry", "ui_objective", "ui_hypothesis"],
    3 => ["ui_options", "ui_clue"]
];

if (!isset($map[$step])) {
    echo json_encode(["status" => "error", "message" => "Invalid step"]);
    exit;
}

$set_clauses = [];
$params = [];
$types = "";

foreach ($map[$step] as $col) {
    $set_clauses[] = "$col = ?";
    $val = $data[$col] ?? '';
    $params[] = is_array($val) ? json_encode($val) : $val;
    $types .= "s";
}

// Update current step
$set_clauses[] = "ui_cur_step = ?";
$params[] = $step;
$types .= "i";

// WHERE clauses
$params[] = $ui_id;
$params[] = $team_id;
$types .= "ii";

$sql = "UPDATE byteguess_user_input SET " . implode(", ", $set_clauses) . " WHERE ui_id = ? AND ui_team_pkid = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

echo json_encode(["status" => "success"]);