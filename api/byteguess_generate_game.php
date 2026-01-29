<?php
/**
 * BYTEGUESS – STEP 6 FINAL GENERATION
 * Full AI Orchestration: Scenario -> Cards -> Options -> Answer Key -> Clues
 */

ini_set('display_errors', 0);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();
header("Content-Type: application/json");

require "../include/dataconnect.php";
require "../config.php";
require "../openai_call.php";

/* SESSION + INPUT VALIDATION */
$team_id = $_SESSION['team_id'] ?? 0;
$input = json_decode(file_get_contents("php://input"), true);
$ui_id = intval($input['ui_id'] ?? 0);

if (!$team_id || !$ui_id) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

/* LOAD USER INPUT (DRAFT) */
$stmt = $conn->prepare("SELECT * FROM byteguess_user_input WHERE ui_id = ? AND ui_team_pkid = ?");
$stmt->bind_param("ii", $ui_id, $team_id);
$stmt->execute();
$ui = $stmt->get_result()->fetch_assoc();

if (!$ui) {
    echo json_encode(["status" => "error", "message" => "Draft not found"]);
    exit;
}

/* CREATE CARD GROUP (GAME) */
$stmt = $conn->prepare("
    INSERT INTO card_group 
    (cg_name, cg_description, cg_max, byteguess_pkid, cg_status) 
    VALUES (?, ?, ?, ?, 1)
");
$stmt->bind_param(
    "ssii",
    $ui['ui_game_name'],
    $ui['ui_game_description'],
    $ui['ui_cards_drawn'],
    $team_id
);
$stmt->execute();
$cg_id = $stmt->insert_id;

$messages = [];

/* --- PROMPT 2: INITIAL SETUP --- */
$prompt2 = "
You are an instructional content designer assisting in the creation of a randomized learning card game.

Game Structure
The game consists of {$ui['ui_total_cards']} information cards.
Each playthrough opens {$ui['ui_cards_drawn']} cards.

Rules
• Each card shows partial clues
• The conclusion must NOT appear on a single card
• Insight must emerge only through synthesis

Do not generate content yet.
Acknowledge understanding only.
";

$messages[] = ["role" => "user", "content" => $prompt2];
$response2 = callOpenAI($messages);
$messages[] = ["role" => "assistant", "content" => $response2];

/* --- PROMPT 3: SCENARIO --- */
$prompt3 = "
Create a fictitious company scenario.

Training Topic: {$ui['ui_training_topic']}
Industry: {$ui['ui_industry']}
Objective: {$ui['ui_objective']}

Participants should identify a hidden hypothesis related to:
{$ui['ui_hypothesis']}

Requirements:
• Fictitious company
• 7–8 sentences
• Realistic business tone
• No training language
";

$messages[] = ["role" => "user", "content" => $prompt3];
$response3 = callOpenAI($messages);
$messages[] = ["role" => "assistant", "content" => $response3];

/* --- PROMPT 4: CARDS --- */
$prompt4 = "
Using the agreed structure and company context, create exactly {$ui['ui_total_cards']} cards.

Each card:
• May include: {$ui['ui_card_structure']}
• 4–5 statements
• Indirect language
• No conclusions

Format STRICTLY:
**Card 1: Title**
Paragraph
";

$messages[] = ["role" => "user", "content" => $prompt4];
$cardsRaw = callOpenAI($messages);
$messages[] = ["role" => "assistant", "content" => $cardsRaw];

/* PARSE + INSERT CARDS */
$blocks = preg_split('/\*\*Card\s+\d+:/i', $cardsRaw);
array_shift($blocks);

$stmt = $conn->prepare("
    INSERT INTO card_unit 
    (cu_card_group_pkid, cu_sequence, cu_name, cu_image, cu_description, cu_status) 
    VALUES (?, ?, ?, 'cu_image.jpg', ?, 1)
");

$seq = 1;
foreach ($blocks as $block) {
    $lines = preg_split("/\R/", trim($block));
    $title = trim(str_replace("**", "", array_shift($lines)));
    $text  = trim(implode("\n", $lines));

    if ($title && $text) {
        $stmt->bind_param("iiss", $cg_id, $seq, $title, $text);
        $stmt->execute();
        $seq++;
    }
}

/* --- PROMPT 5: OPTIONS --- */
$opts = json_decode($ui['ui_options'], true);

$prompt5 = "
Using the generated cards, create four hypothesis options.

Mix:
• {$opts['full']} fully correct
• {$opts['partial']} partially correct
• {$opts['wrong']} incorrect

Rules:
• Short title (≤4 words)
• 2–3 statements each
• No labels

Format STRICTLY:
**Option X: Title**
Paragraph
";

$messages[] = ["role" => "user", "content" => $prompt5];
$optionsRaw = callOpenAI($messages);
$messages[] = ["role" => "assistant", "content" => $optionsRaw];

preg_match_all(
    '/\*\*Option\s+(\d+):\s*(.*?)\*\*\s*(.*?)(?=\n\*\*Option|\z)/s',
    $optionsRaw,
    $matches,
    PREG_SET_ORDER
);

$answers = [];
$order = 1;
foreach ($matches as $m) {
    $answers[] = [
        "order" => $order++,
        "title" => trim($m[2]),
        "answer" => trim($m[3])
    ];
}

$jsonAnswers = json_encode($answers, JSON_UNESCAPED_UNICODE);
$stmt = $conn->prepare("UPDATE card_group SET cg_answer = ? WHERE cg_id = ?");
$stmt->bind_param("si", $jsonAnswers, $cg_id);
$stmt->execute();

/* --- PROMPT 6: ANSWER KEY --- */
$prompt6 = "
Using the correct hypothesis and card signals, generate a participant-facing answer key.
Provide an answer key paragraph that can be shared with participants.
Guidelines
• Focus on:
• Why the correct answer is strongest
• What participants may have missed in partial options
• How different card signals connect
• Tone should be:
• Constructive
• Learning-oriented
• Not judgmental
The explanation should reinforce synthesis and reasoning, not just correctness.
Give only answer key content, don't give any heading before and after the content.
";

$messages[] = ["role" => "user", "content" => $prompt6];
$answerKey = callOpenAI($messages);
$messages[] = ["role" => "assistant", "content" => $answerKey];

$stmt = $conn->prepare("UPDATE card_group SET cg_result = ? WHERE cg_id = ?");
$stmt->bind_param("si", $answerKey, $cg_id);
$stmt->execute();

/* --- NEW STEP: CLUE GENERATION (Conditional) --- */
$num_clues = intval($ui['ui_clue'] ?? 0);

if ($num_clues > 0) {
    $promptClue = "
    Generate $num_clues clue statements about the hypothesis. 
    Do not give direct reference to the answer. 
    The clue should be such the participant can get some inference about the right choices. 
    No clue should give full inference to complete answer. 
    The clue should be strictly a sentence having not more than 30 words.
    ";

    $messages[] = ["role" => "user", "content" => $promptClue];
    $cluesRaw = callOpenAI($messages);
    
    $clueLines = preg_split("/\R/", trim($cluesRaw));
    $clueLines = array_filter(array_map('trim', $clueLines)); 
    $clueLines = array_slice($clueLines, 0, $num_clues); 

    $clueLegends = ['L', 'M', 'N', 'H', 'A', 'B', 'C', 'D'];
    $finalClues = [];
    $clueOrder = 1;

    foreach ($clueLines as $index => $text) {
        $cleanClue = preg_replace('/^\d+[\.\)\s]+|[*•-]\s+/', '', $text);
        
        $finalClues[] = [
            "legend" => $clueLegends[$index] ?? "X",
            "score"  => "0",
            "clue"   => $cleanClue,
            "order"  => $clueOrder++
        ];
    }

    $jsonClues = json_encode($finalClues, JSON_UNESCAPED_UNICODE);
    
    $stmt = $conn->prepare("UPDATE card_group SET cg_clue = ? WHERE cg_id = ?");
    $stmt->bind_param("si", $jsonClues, $cg_id);
    $stmt->execute();
}

/* FINALIZE */
$stmt = $conn->prepare("UPDATE byteguess_user_input SET ui_cur_step = 6 WHERE ui_id = ?");
$stmt->bind_param("i", $ui_id);
$stmt->execute();

echo json_encode(["status" => "success", "cg_id" => $cg_id]);