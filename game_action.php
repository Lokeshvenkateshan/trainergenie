<?php
session_start();
header("Content-Type: application/json");

require_once "config.php";
loadEnv(__DIR__ . "/.env");


$input = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION["messages"])) {
    $_SESSION["messages"] = [];
}

$apiKey = getenv("OPENAI_API_KEY");


if ($input["step"] == 1) {

    $c = intval($input["c"]);
    $d = intval($input["d"]);

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

    $_SESSION["messages"][] = ["role" => "user", "content" => $prompt];
}

if ($input["step"] == 2) {

    $topic = $input["topic"];
    $industry = $input["industry"];
    $objective = $input["objective"];

    $prompt = "
Using the previously agreed game structure, create a fictitious company scenario.

Training topic / game subject: $topic
Industry: $industry
Card game objective: $objective

Task:
Create a fictitious company name and a short company introduction.
The introduction should be 7–8 statements in one paragraph.
Avoid explicit game terminology.
Avoid directly naming the learning objective.
Await further input.
";

    $_SESSION["messages"][] = ["role" => "user", "content" => $prompt];
}

/* ChatGPT API */
$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => $_SESSION["messages"]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_SSL_VERIFYPEER => false // LOCAL ONLY
]);

$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);
$reply = $data["choices"][0]["message"]["content"];

$_SESSION["messages"][] = ["role" => "assistant", "content" => $reply];

echo json_encode(["reply" => $reply]);
