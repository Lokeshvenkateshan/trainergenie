<?php
session_start();
require "include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch existing orgs
$stmt = $conn->prepare("
    SELECT ig_id, ig_name
    FROM byteguess_category
    WHERE ig_team_pkid = ?
    ORDER BY ig_id DESC
");
$stmt->bind_param("i", $_SESSION['team_id']);
$stmt->execute();
$orgs = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Step 1 - Organization</title>
    <link rel="stylesheet" href="./assets/styles/byteguess_step1.css">
</head>

<body>
<form action="logout.php" method="post" class="logout-wrap">
    <button class="btn-logout">Logout</button>
</form>

<div class="page">

    <div class="box">
        <h3>Step 1: Choose ByteGuess Category</h3>

        <label>Select Existing ByteGuess Category</label>
        <select id="existing_org">
            <option value="">-- Select --</option>
            <?php while ($row = $orgs->fetch_assoc()): ?>
                <option value="<?= $row['ig_id'] ?>">
                    <?= htmlspecialchars($row['ig_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <div class="divider">OR</div>

        <label>Create ByteGuess Category</label>
        <input type="text" id="ig_name" placeholder="Category name">
        <textarea id="ig_description" placeholder="Description (optional)"></textarea>

        <button class="primary-btn" onclick="submitOrg()">Continue</button>
    </div>

    <div class="preview-wrap">
        <button class="btn-preview" onclick="handlePreview()">Preview Your Games</button>
    </div>

</div>


    <script>
        function handlePreview() {
            window.location.href = "preview/ByteGuessCategories/categories.php";
        }

        function submitOrg() {
            const existing = document.getElementById("existing_org").value;
            const name = document.getElementById("ig_name").value.trim();
            const desc = document.getElementById("ig_description").value.trim();

            if (!existing && name === "") {
                alert("Select an organization or create a new one");
                return;
            }

            fetch("byteguess_step1_action.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        ig_id: existing,
                        ig_name: name,
                        ig_description: desc
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = "byteguess_step2.php";
                    } else {
                        alert(data.message);
                    }
                });
        }
    </script>

</body>

</html>