<?php
session_start();
require "../../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: ../../login.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT ig_id, ig_name, ig_description
    FROM byteguess_category
    WHERE ig_team_pkid = ?
    ORDER BY ig_id ASC
");
$stmt->bind_param("i", $_SESSION['team_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Your ByteGuess Categories</title>
    <link rel="stylesheet" href="../../assets/styles/categories.css">

</head>



<body>

    <div class="container">
        <a href="../../byteguess_step1.php" class="back-btn">
            <?php include "../../assets/icons/back_arrow.php"; ?>
            Back
        </a>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="flash-success" id="flashMessage">
                <?= htmlspecialchars($_SESSION['flash_success']) ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>



        <h1>Your ByteGuess Categories</h1>

        <?php if ($result->num_rows === 0): ?>
            <div class="empty">
                No categories created yet.
            </div>
        <?php else: ?>
            <div class="grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">

                        <a href="../cardGroups/cardGroups.php?ig_id=<?= $row['ig_id'] ?>"
                            class="card-link">

                            <div class="card-title">
                                <?= htmlspecialchars($row['ig_name']) ?>
                            </div>

                            <div class="card-desc">
                                <?= nl2br(htmlspecialchars($row['ig_description'] ?: 'No description provided.')) ?>
                            </div>

                        </a>

                        <div class="card-actions">
                            <a href="../cardGroups/cardGroups.php?ig_id=<?= $row['ig_id'] ?>"
                                class="card-link"> view card groups </a>
                            <a class="card-link" href="category_edit.php?id=<?= $row['ig_id'] ?>">
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