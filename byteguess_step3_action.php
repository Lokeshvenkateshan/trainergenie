<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['messages'])) {
    echo json_encode(["status"=>"error","message"=>"Invalid flow"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$A  = trim($input['A']  ?? '');
$A1 = trim($input['A1'] ?? '');
$B  = trim($input['B']  ?? '');
$B1 = trim($input['B1'] ?? '');

if (!$A || !$A1 || !$B || !$B1) {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

$prompt = "
Using the previously agreed game structure, create a fictitious company scenario.

Training Topic / Participants: {$A}
Industry: {$A1}
Game Objective: {$B}

By reviewing randomly opened cards, the participant should identify an underlying hypothesis related to {$B1}.

Create:
• A fictitious company name
• One paragraph (7-8 sentences)
• Generic, realistic business context
• No training terminology
• Do not state the objective explicitly

Await further input.
";

$_SESSION['messages'][] = [
    "role" => "user",
    "content" => $prompt
];

require "openai_call.php";
$response = callOpenAI($_SESSION['messages']);

$_SESSION['messages'][] = [
    "role" => "assistant",
    "content" => $response
];

$_SESSION['ai_log'][] = [
    "step" => 3,
    "prompt" => $prompt,
    "response" => $response
];

echo json_encode(["status"=>"success"]);
