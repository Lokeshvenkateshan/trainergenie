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
    header("Location: cardGroups.php?ig_id=" . $ig_id);
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
    header("Location: cardGroups.php?ig_id=" . $ig_id);
    exit;
}

$data = $res->fetch_assoc();

/* ---------- SAFE PARSE ANSWERS ---------- */
$answers = [];

if (!empty($data['cg_answer'])) {
    $decoded = json_decode($data['cg_answer'], true);
    if (is_array($decoded)) {
        $answers = $decoded;
    }
}

/* Ensure exactly 4 options */
for ($i = count($answers); $i < 4; $i++) {
    $answers[] = [
        "title"  => "",
        "answer" => "",
        "order"  => $i + 1
    ];
}

/* Safe result */
$resultText = $data['cg_result'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Card Group</title>
    <link rel="stylesheet" href="../../assets/styles/cardgroups.css">
</head>

<body>

<div class="container">

    <a href="cardGroups.php?ig_id=<?= $ig_id ?>" class="back-btn">
        <?php include "../../assets/icons/back_arrow.php"; ?>
        Back
    </a>

    <h1>Edit Card Group</h1>

    <form method="post" action="cardGroup_edit_action.php">

        <input type="hidden" name="cg_id" value="<?= $cg_id ?>">
        <input type="hidden" name="ig_id" value="<?= $ig_id ?>">

        <label>Game Name</label>
        <input
            type="text"
            name="cg_name"
            value="<?= htmlspecialchars($data['cg_name'] ?? '') ?>"
            required
        >

        <label>Description</label>
        <textarea name="cg_description"><?= htmlspecialchars($data['cg_description'] ?? '') ?></textarea>

       

        <hr>

        <h3>Hypothesis Options</h3>

        <?php foreach ($answers as $i => $opt): ?>
            <div style="margin-bottom:18px;">
                <label>Option <?= $i + 1 ?> Title</label>
                <input
                    type="text"
                    name="opt_title[]"
                    value="<?= htmlspecialchars($opt['title']) ?>"
                    required
                >

                <label>Option <?= $i + 1 ?> Answer</label>
                <textarea name="opt_answer[]" required><?= htmlspecialchars($opt['answer']) ?></textarea>
            </div>
        <?php endforeach; ?>

        <hr>

        <h3>Answer Key</h3>
        <textarea
            name="cg_result"
            style="height:140px;"
            required><?= htmlspecialchars($resultText) ?></textarea>

        <button type="submit">Save Changes</button>

    </form>

</div>

</body>
</html>
