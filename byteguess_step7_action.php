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
Your Task
Create a clear and unambiguous “How to Play & Complete the Game” guideline that can be shown directly to participants.

Mandatory Content to Cover
1. What the player is trying to achieve (in simple terms)
2. How the cards work (total cards, how many can be opened, randomness)
3. Step-by-step instructions on how to play the game from start to finish
4. What players should expect (incomplete information, no single right card)
5. How to complete the game (making a final choice / submission)

Clarity Rules (Critical)
• Assume the player has never played this game before
• Use plain language, short sentences, and numbered steps
• Explicitly state that:
  – No single card contains the answer
  – Not all cards will be visible
  – Reasoned judgment is expected, not guessing
• Avoid facilitator language, answers, or hints

Tone & Length
• Professional, encouraging, and neutral
• Suitable to fit on one screen or one printed page

Output Requirement
Generate a standalone “How to Play” guideline that can be shared verbatim with participants.
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

/* ===== RETURN ===== */
echo json_encode([
    "status" => "success",
    "guidelines" => $aiResponse
]);
