<?php
session_start();
require "../../include/dataconnect.php";

$cg_id = intval($_POST['cg_id'] ?? 0);
$ig_id = intval($_POST['ig_id'] ?? 0);

$name = trim($_POST['cg_name'] ?? '');
$desc = trim($_POST['cg_description'] ?? '');
$result = trim($_POST['cg_result'] ?? '');

$titles  = $_POST['opt_title'] ?? [];
$answers = $_POST['opt_answer'] ?? [];

if ($cg_id <= 0 || $name === '' || count($titles) !== 4) {
    header("Location: cardGroups.php?ig_id=".$ig_id);
    exit;
}

/* Rebuild JSON */
$newAnswers = [];
foreach ($titles as $i => $t) {
    $newAnswers[] = [
        "title"  => trim($t),
        "answer" => trim($answers[$i] ?? ''),
        "order"  => $i + 1
    ];
}

$jsonAnswers = json_encode($newAnswers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$stmt = $conn->prepare("
    UPDATE card_group
    SET cg_name = ?, cg_description = ?, cg_answer = ?, cg_result = ?
    WHERE cg_id = ?
");

$stmt->bind_param(
    "ssssi",
    $name,
    $desc,
    $jsonAnswers,
    $result,
    $cg_id
);

$stmt->execute();

$_SESSION['flash_success'] = "Card group updated successfully.";

header("Location: cardGroups.php?ig_id=".$ig_id);
exit;
