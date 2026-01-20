<?php
session_start();
require "../../include/dataconnect.php";

$cu_id = intval($_GET['id'] ?? 0);
$cg_id = intval($_GET['cg_id'] ?? 0);

if ($cu_id <= 0 || $cg_id <= 0) {
    header("Location: cardUnits.php?cg_id=".$cg_id);
    exit;
}

$stmt = $conn->prepare("
    SELECT cu_name, cu_description
    FROM card_unit
    WHERE cu_id = ?
");
$stmt->bind_param("i", $cu_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: cardUnits.php?cg_id=".$cg_id);
    exit;
}

$data = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Card Unit</title>
    <link rel="stylesheet" href="../../assets/styles/cardunits.css">
</head>
<body>

<div class="container">

    <a href="cardUnits.php?cg_id=<?= $cg_id ?>" class="back-btn">
        <?php include "../../assets/icons/back_arrow.php"; ?>
        Back
    </a>

    <h1>Edit Card Unit</h1>

    <form method="post" action="cardUnit_edit_action.php">
        <input type="hidden" name="cu_id" value="<?= $cu_id ?>">
        <input type="hidden" name="cg_id" value="<?= $cg_id ?>">

        <label>Card Name</label>
        <input type="text"
               name="cu_name"
               value="<?= htmlspecialchars($data['cu_name'] ?? '') ?>"
               required>

        <label>Description</label>
        <textarea name="cu_description" required><?= htmlspecialchars($data['cu_description'] ?? '') ?></textarea>

        <button type="submit">Save</button>
    </form>

</div>

</body>
</html>
