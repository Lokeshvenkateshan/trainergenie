<?php
session_start();
require "../../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: ../../login.php");
    exit;
}

$cg_id = intval($_GET['cg_id'] ?? 0);
if ($cg_id <= 0) {
    header("Location: ../cardGroup/cardGroups.php");
    exit;
}

/* Fetch card group name */
$gstmt = $conn->prepare("SELECT cg_name FROM card_group WHERE cg_id = ?");
$gstmt->bind_param("i", $cg_id);
$gstmt->execute();
$gres = $gstmt->get_result();

if ($gres->num_rows === 0) {
    header("Location: ../cardGroup/cardGroups.php");
    exit;
}
$group = $gres->fetch_assoc();

/* Fetch card units */
$stmt = $conn->prepare("
    SELECT cu_id, cu_name, cu_description
    FROM card_unit
    WHERE cu_card_group_pkid = ?
    ORDER BY cu_sequence ASC
");
$stmt->bind_param("i", $cg_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Card Units</title>
    <link rel="stylesheet" href="../../assets/styles/cardunits.css">
</head>
<body>

<div class="container">

    <a href="../cardGroups/cardGroups.php?ig_id=<?= htmlspecialchars($_GET['ig_id'] ?? '') ?>" class="back-btn">
        <?php include "../../assets/icons/back_arrow.php"; ?>
        Back
    </a>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-success" id="flashMessage">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <h1>Card Units of <?= htmlspecialchars($group['cg_name']) ?></h1>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty">No card units found.</div>
    <?php else: ?>
        <div class="grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-title">
                        <?= htmlspecialchars($row['cu_name'] ?: 'Untitled Card') ?>
                    </div>

                    <div class="card-desc">
                        <?= nl2br(htmlspecialchars($row['cu_description'] ?: 'No description available.')) ?>
                    </div>

                    <div class="card-actions">
                        <a class="edit"
                           href="cardUnit_edit.php?id=<?= $row['cu_id'] ?>&cg_id=<?= $cg_id ?>">
                            Edit
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

</div>

<script>
setTimeout(function(){
    var msg = document.getElementById("flashMessage");
    if(msg){ msg.style.display="none"; }
},2000);
</script>

</body>
</html>
