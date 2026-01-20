<?php
session_start();
header("Content-Type: application/json");

require "include/dataconnect.php";
require "config.php";

if (!isset($_SESSION['cg_id']) || !isset($_SESSION['messages'])) {
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$f1 = intval($input['f1'] ?? 0);
$f2 = intval($input['f2'] ?? 0);
$f3 = intval($input['f3'] ?? 0);

if ($f1 <= 0 || $f2 <= 0 || $f3 <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid F values"]);
    exit;
}

/* ================= PROMPT ================= */

$prompt = "
Using the previously generated cards, create four hypothesis options.

Each option must:
• Be a short storyline or summary
• Contain 2–3 statements
• Have a short title (maximum 4 words)

Answer Quality Mix
• {$f1} fully correct
• {$f2} partially correct
• {$f3} strong distractor

Rules
• Do not label which option is correct
• Do not add explanations

Format STRICTLY as:
**Option X: Title**
Paragraph
";

$_SESSION['messages'][] = [
    "role" => "user",
    "content" => $prompt
];

/* ================= AI CALL ================= */

require "openai_call.php";
$aiResponse = callOpenAI($_SESSION['messages']);

$_SESSION['messages'][] = [
    "role" => "assistant",
    "content" => $aiResponse
];

/* ================= PARSE RESPONSE ================= */

$pattern = '/\*\*Option\s+(\d+):\s*(.*?)\*\*\s*(.*?)(?=\n\*\*Option|\z)/s';
preg_match_all($pattern, $aiResponse, $matches, PREG_SET_ORDER);

$answers = [];
$order = 1;

foreach ($matches as $match) {
    $title = trim($match[2]);
    $answer = trim(preg_replace('/\s+/', ' ', $match[3]));

    $answers[] = [
        "answer" => $answer,
        "title"  => $title,
        "order"  => $order
    ];

    $order++;
}

/* ================= VALIDATION ================= */

if (count($answers) !== 4) {
    echo json_encode([
        "status" => "error",
        "message" => "AI response parsing failed",
        "raw" => $aiResponse
    ]);
    exit;
}

/* ================= STORE IN DB ================= */

$jsonAnswers = json_encode($answers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$stmt = $conn->prepare("
    UPDATE card_group
    SET cg_answer = ?
    WHERE cg_id = ?
");
$stmt->bind_param("si", $jsonAnswers, $_SESSION['cg_id']);
$stmt->execute();

/* ================= RESPONSE ================= */

echo json_encode([
    "status" => "success",
    "stored_json" => $answers
]);
