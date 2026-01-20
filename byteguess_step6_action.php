<?php
session_start();
header("Content-Type: application/json");

require "include/dataconnect.php";
require "config.php";

if (!isset($_SESSION['messages']) || !isset($_SESSION['cg_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid session"
    ]);
    exit;
}

/* ===== PROMPT ===== */
$prompt = "
Using the correct hypothesis and card signals, generate a participant-facing answer key.

Provide an answer key paragraph that can be shared with participants.

Guidelines
• Focus on:
  – Why the correct answer is strongest
  – What participants may have missed in partial options
  – How different card signals connect
• Tone should be:
  – Constructive
  – Learning-oriented
  – Not judgmental

The explanation should reinforce synthesis and reasoning, not just correctness.
";

/* ===== AI CONTEXT ===== */
$_SESSION['messages'][] = [
    "role" => "user",
    "content" => $prompt
];

/* ===== AI CALL ===== */
require "openai_call.php";
$aiResponse = trim(callOpenAI($_SESSION['messages']));

$_SESSION['messages'][] = [
    "role" => "assistant",
    "content" => $aiResponse
];

/* ===== STORE RESULT IN DB ===== */
$stmt = $conn->prepare("
    UPDATE card_group
    SET cg_result = ?
    WHERE cg_id = ?
");

$stmt->bind_param(
    "si",
    $aiResponse,
    $_SESSION['cg_id']
);

$stmt->execute();

/* ===== RESPONSE ===== */
echo json_encode([
    "status" => "success",
    "answer_key" => $aiResponse
]);
