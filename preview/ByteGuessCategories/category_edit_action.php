<?php
session_start();
require "../../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: ../login.php");
    exit;
}

$ig_id = intval($_POST['ig_id'] ?? 0);
$name  = trim($_POST['ig_name'] ?? '');
$desc  = trim($_POST['ig_description'] ?? '');

if ($ig_id <= 0 || $name === '') {
    header("Location: categories.php");
    exit;
}

$stmt = $conn->prepare("
    UPDATE byteguess_category
    SET ig_name = ?, ig_description = ?
    WHERE ig_id = ? AND ig_team_pkid = ?
");

$stmt->bind_param(
    "ssii",
    $name,
    $desc,
    $ig_id,
    $_SESSION['team_id']
);

$stmt->execute();

$_SESSION['flash_success'] = "Category updated successfully.";


header("Location: categories.php");
exit;
