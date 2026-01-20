<?php
session_start();
header("Content-Type: application/json");

require "include/dataconnect.php";
require "config.php";

if (
    !isset($_SESSION['cg_id']) ||
    !isset($_SESSION['messages']) ||
    !isset($_SESSION['c']) ||
    !isset($_SESSION['d'])
) {
    echo json_encode(["status"=>"error","message"=>"Invalid session"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$E = trim($input['E'] ?? '');

if ($E === '') {
    echo json_encode(["status"=>"error","message"=>"Card structure missing"]);
    exit;
}

$c = $_SESSION['c'];
$d = $_SESSION['d'];

$prompt = "
Using the previously agreed game structure and company context, create exactly {$c} cards.

Each card:
• May include: {$E}
• 4-5 statements
• Indirect language
• No training keywords

Format strictly:
**Card 1: Title**
Paragraph
";

$_SESSION['messages'][] = [
    "role"=>"user",
    "content"=>$prompt
];

require "openai_call.php";
$ai = callOpenAI($_SESSION['messages']);

$_SESSION['messages'][] = [
    "role"=>"assistant",
    "content"=>$ai
];

/* --- PARSE CARDS --- */
$blocks = preg_split('/\*\*Card\s+\d+:/', $ai);
array_shift($blocks);

$stmt = $conn->prepare("
    INSERT INTO card_unit
    (cu_card_group_pkid, cu_sequence, cu_name, cu_image, cu_description, cu_status)
    VALUES (?, ?, ?, 'cu_image.jpg', ?, 1)
");

$seq = 1;

foreach ($blocks as $block) {

    $block = trim($block);

    // Split lines
    $lines = preg_split("/\r\n|\n|\r/", $block);

    // Extract title from first line
    // 
    $titleLine = array_shift($lines);
    $cardName = trim(str_replace("**", "", $titleLine));

    // Remaining lines = card content
    $cardText = trim(implode("\n", $lines));

    if ($cardText && $cardName) {
        $stmt->bind_param(
            "iiss",
            $_SESSION['cg_id'],
            $seq,
            $cardName,
            $cardText
        );
        $stmt->execute();
        $seq++;
    }
}


echo json_encode([
    "status"=>"success",
    "ai_raw"=>$ai
]);
