<?php
session_start();
require "../../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: ../../login.php");
    exit;
}

$cg_id = intval($_GET['id'] ?? 0);
$ig_id = intval($_GET['ig_id'] ?? 0);

if ($cg_id <= 0 || $ig_id <= 0) {
    header("Location: cardGroups.php?ig_id=".$ig_id);
    exit;
}

$stmt = $conn->prepare("
    SELECT cg_name, cg_description, cg_max, cg_answer, cg_result
    FROM card_group
    WHERE cg_id = ?
");
$stmt->bind_param("i", $cg_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: cardGroups.php?ig_id=".$ig_id);
    exit;
}

$data = $res->fetch_assoc();

/* Safe parse answers */
$answers = [];
if (!empty($data['cg_answer'])) {
    $decoded = json_decode($data['cg_answer'], true);
    if (is_array($decoded)) {
        $answers = $decoded;
    }
}

/* Ensure 4 slots */
for ($i = count($answers); $i < 4; $i++) {
    $answers[] = ["title"=>"", "answer"=>"", "order"=>$i+1];
}

$resultText = $data['cg_result'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Card Group</title>
    <link rel="stylesheet" href="../../assets/styles/cardgroups.css">
</head>
<body>

<div class="container">

    <a href="cardGroups.php?ig_id=<?= $ig_id ?>" class="back-btn">
        <?php include "../../assets/icons/back_arrow.php"; ?>
        Back
    </a>

    <h1><?= htmlspecialchars($data['cg_name']) ?></h1>

    <div class="view-box">
        <p class="muted"><?= nl2br(htmlspecialchars($data['cg_description'] ?: 'No description')) ?></p>

        <div class="meta">
            <strong>Drawable Cards:</strong> <?= (int)$data['cg_max'] ?>
        </div>
    </div>

    <h3>Hypothesis Options</h3>

    <div class="options">
        <?php foreach ($answers as $i => $opt): ?>
            <div class="option-card">
                <div class="option-title">
                    Option <?= $i+1 ?>: <?= htmlspecialchars($opt['title'] ?: '—') ?>
                </div>
                <div class="option-desc">
                    <?= nl2br(htmlspecialchars($opt['answer'] ?: 'No content')) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h3>Answer Key</h3>

    <div class="result-box">
        <?= nl2br(htmlspecialchars($resultText ?: 'Not generated yet')) ?>
    </div>

    <div style="text-align:right; margin-top:20px;">
        <a href="cardGroup_edit.php?id=<?= $cg_id ?>&ig_id=<?= $ig_id ?>" class="edit-btn">
            Edit Card Group
        </a>
    </div>

</div>

</body>
</html>
