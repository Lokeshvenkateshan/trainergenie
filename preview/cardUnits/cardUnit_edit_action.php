<?php
session_start();
require "../../include/dataconnect.php";

$cu_id = intval($_POST['cu_id'] ?? 0);
$cg_id = intval($_POST['cg_id'] ?? 0);
$name  = trim($_POST['cu_name'] ?? '');
$desc  = trim($_POST['cu_description'] ?? '');

if ($cu_id <= 0 || $name === '') {
    header("Location: cardUnits.php?cg_id=".$cg_id);
    exit;
}

$stmt = $conn->prepare("
    UPDATE card_unit
    SET cu_name = ?, cu_description = ?
    WHERE cu_id = ?
");
$stmt->bind_param("ssi", $name, $desc, $cu_id);
$stmt->execute();

$_SESSION['flash_success'] = "Card unit updated successfully.";

header("Location: cardUnits.php?cg_id=".$cg_id);
exit;
