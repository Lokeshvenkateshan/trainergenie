<?php
session_start();
require "../../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: ../../login.php");
    exit;
}

$ig_id = intval($_GET['ig_id'] ?? 0);
if ($ig_id <= 0) {
    header("Location: ../ByteGuesscategories/categories.php");
    exit;
}


$catStmt = $conn->prepare("
    SELECT ig_name
    FROM byteguess_category
    WHERE ig_id = ?
");
$catStmt->bind_param("i", $ig_id);
$catStmt->execute();
$catResult = $catStmt->get_result();

if ($catResult->num_rows === 0) {
    header("Location: ../ByteGuessCategories/categories.php");
    exit;
}

$category = $catResult->fetch_assoc();
$categoryName = $category['ig_name'];


$stmt = $conn->prepare("
    SELECT cg_id, cg_name, cg_description
    FROM card_group
    WHERE byteguess_pkid = ?
    ORDER BY cg_id ASC
");
$stmt->bind_param("i", $ig_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Card Groups</title>
    <link rel="stylesheet" href="../../assets/styles/cardgroups.css">
</head>

<body>

    <div class="container">

        <a href="../ByteGuessCategories/categories.php" class="back-btn">
            <?php include "../../assets/icons/back_arrow.php"; ?>
            Back
        </a>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="flash-success" id="flashMessage">
                <?= htmlspecialchars($_SESSION['flash_success']) ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <h1>Card Groups of <?= htmlspecialchars($categoryName) ?></h1>

        <?php if ($result->num_rows === 0): ?>
            <div class="empty">No card groups found.</div>
        <?php else: ?>
            <div class="grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">

                        <a href="../cardUnits/cardUnits.php?cg_id=<?= $row['cg_id'] ?>&ig_id=<?= $ig_id ?>"
                            class="card-link">

                            <div class="card-title">
                                <?= htmlspecialchars($row['cg_name']) ?>
                            </div>

                            <div class="card-desc">
                                <?= nl2br(htmlspecialchars($row['cg_description'] ?: 'No description')) ?>
                            </div>

                        </a>

                        <div class="card-actions">
                            <a href="../cardUnits/cardUnits.php?cg_id=<?= $row['cg_id'] ?>&ig_id=<?= $ig_id ?>"
                                class="card-link"> view card units </a>
                            <a class="view" href="cardGroup_view.php?id=<?= $row['cg_id'] ?>&ig_id=<?= $ig_id ?>">card group details</a>

                            <a class="edit"
                                href="cardGroup_edit.php?id=<?= $row['cg_id'] ?>&ig_id=<?= $ig_id ?>">
                                Edit
                            </a>
                        </div>

                    </div>

                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
        setTimeout(function() {
            var msg = document.getElementById("flashMessage");
            if (msg) {
                msg.style.display = "none";
            }
        }, 2000);
    </script>

</body>

</html>