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
    <style>
        body { font-family: Arial; background:#f4f4f4; }
        .box {
            background:#fff;
            width:420px;
            margin:60px auto;
            padding:20px;
            border-radius:6px;
            box-shadow:0 0 10px #ccc;
        }
        input, textarea, select, button {
            width:100%;
            padding:8px;
            margin-top:8px;
        }
        .divider {
            text-align:center;
            margin:15px 0;
            font-weight:bold;
            color:#666;
        }
    </style>
</head>
<body>

<form action="logout.php" method="post" style="position:absolute;top:20px;right:20px;">
    <button style="background:#dc3545;color:#fff;">Logout</button>
</form>

<div class="box">
    <h3>Step 1: Choose Organization</h3>

    <label>Select Existing Organization</label>
    <select id="existing_org">
        <option value="">-- Select --</option>
        <?php while ($row = $orgs->fetch_assoc()): ?>
            <option value="<?= $row['ig_id'] ?>">
                <?= htmlspecialchars($row['ig_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <div class="divider">OR</div>

    <label>Create New Organization</label>
    <input type="text" id="ig_name" placeholder="Organization name">
    <textarea id="ig_description" placeholder="Description (optional)"></textarea>

    <button onclick="submitOrg()">Continue</button>
</div>

<script>
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
        headers: {"Content-Type":"application/json"},
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
