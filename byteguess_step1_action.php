<?php
session_start();
header("Content-Type: application/json");
require "include/dataconnect.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['ig_id'])) {
    echo json_encode(["status"=>"error","message"=>"Session expired"]);
    exit;
}

$c = intval($input['c'] ?? 0);
$d = intval($input['d'] ?? 0);

if ($c < 6 || $d <= 0 || $d > $c) {
    echo json_encode(["status"=>"error","message"=>"Invalid values"]);
    exit;
}

/* ===== PROMPT (EXACT AS ORDERED) ===== */

$prompt = "
The game consists of $c information cards.
In each playthrough, the participant will randomly draw and open any $d cards.

Design Objective
The full and correct conclusion should not appear on any single card.
Instead, the insight must emerge only when information from multiple cards is synthesized.

Constraints
Do not reveal the final conclusion directly on any card.
Acknowledge understanding and confirm constraints. Do not generate content yet.
";

/* store in session */
$_SESSION['messages'][] = [
    "role" => "user",
    "content" => $prompt
];

/* ===== INSERT card_group ===== */

$ig_id = $_SESSION['ig_id'];

$stmt = $conn->prepare("
    INSERT INTO card_group
    (cg_max, byteguess_pkid, cg_status)
    VALUES (?, ?, 1)
");

$stmt->bind_param("ii", $d, $ig_id);

if (!$stmt->execute()) {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
    exit;
}

$_SESSION['cg_id'] = $stmt->insert_id;

echo json_encode([
    "status"=>"success",
    "message"=>"Step 1 saved successfully"
]);
