<?php
session_start();
require "../../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: ../../login.php");
    exit;
}

$ig_id = intval($_GET['id'] ?? 0);

if ($ig_id <= 0) {
    header("Location: categories.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT ig_name, ig_description
    FROM byteguess_category
    WHERE ig_id = ? AND ig_team_pkid = ?
");
$stmt->bind_param("ii", $ig_id, $_SESSION['team_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: categories.php");
    exit;
}

$category = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="../../assets/styles/categories.css">
</head>

<body>

<div class="container">
    <a href="categories.php" class="back-btn">
        <?php include "../../assets/icons/back_arrow.php"; ?>
        Back
    </a>

    <h1>Edit Category</h1>

    <form method="post" action="category_edit_action.php">
        <input type="hidden" name="ig_id" value="<?= $ig_id ?>">

        <label>Category Name</label>
        <input type="text" name="ig_name"
               value="<?= htmlspecialchars($category['ig_name']) ?>"
               required
               style="width:100%; padding:8px; margin-top:6px;">

        <br><br>

        <label>Description</label>
        <textarea name="ig_description"
                  style="width:100%; padding:8px; height:120px; margin-top:6px;"><?= htmlspecialchars($category['ig_description']) ?></textarea>

        <br><br>

        <button type="submit"
                style="padding:10px 16px; background:#007bff; color:#fff; border:none; border-radius:6px; cursor:pointer;">
            Save
        </button>
    </form>
</div>

</body>
</html>
