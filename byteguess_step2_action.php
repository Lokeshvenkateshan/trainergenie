<?php
session_start();
header("Content-Type: application/json");

require "include/dataconnect.php";
require "config.php";

if (!isset($_SESSION['ig_id'])) {
    echo json_encode(["status"=>"error","message"=>"Invalid org session"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$c = intval($input['c'] ?? 0);
$d = intval($input['d'] ?? 0);

if ($c < 6 || $d <= 0 || $d > $c) {
    echo json_encode(["status"=>"error","message"=>"Invalid C / D values"]);
    exit;
}

/* ===== STORE SESSION ===== */
$_SESSION['c'] = $c;
$_SESSION['d'] = $d;

/* ===== CREATE CARD GROUP (GAME) ===== */
$stmt = $conn->prepare("
    INSERT INTO card_group (cg_max, byteguess_pkid, cg_status)
    VALUES (?, ?, 1)
");
$stmt->bind_param("ii", $d, $_SESSION['ig_id']);
$stmt->execute();

$_SESSION['cg_id'] = $stmt->insert_id;

/* ===== AI STEP-1 PROMPT ===== */
$prompt = "
You are an instructional content designer assisting in the creation of a randomized learning card game used in professional training.

Game Structure
The game consists of {$c} information cards.
In each playthrough, the participant will randomly draw and open any {$d} cards.

Each card contains partial data, clues, signals, or perspectives related to a single underlying hypothesis or conclusion.

Design Objective
The full conclusion must not appear on any single card.
Insights must emerge only through synthesis of multiple cards.

Constraints
Do not generate content yet.
Acknowledge understanding only.
";

$_SESSION['messages'][] = [
    "role" => "user",
    "content" => $prompt
];

/* ===== CALL AI ===== */
require "openai_call.php";
$aiResponse = callOpenAI($_SESSION['messages']);

$_SESSION['messages'][] = [
    "role" => "assistant",
    "content" => $aiResponse
];

$_SESSION['ai_log'][] = [
    "step" => 2,
    "prompt" => $prompt,
    "response" => $aiResponse
];

echo json_encode(["status"=>"success"]);
