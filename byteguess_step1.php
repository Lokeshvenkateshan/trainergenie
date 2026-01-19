<?php
session_start();
if (!isset($_SESSION['ig_id'])) {
    header("Location: byteguess_start.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ByteGuess Step 1</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; }
        .box {
            background:#fff;
            width:400px;
            margin:60px auto;
            padding:20px;
            border-radius:6px;
            box-shadow:0 0 10px #ccc;
        }
        input, button {
            width:100%;
            padding:8px;
            margin-top:8px;
        }
    </style>
</head>
<body>
    <button onclick="window.location.href='logout.php'"
        style="background:#dc3545; color:white;">
    Logout
</button>


<div class="box">
    <h3>Step 1: Game Structure</h3>

    <label>Total Cards (C)</label>
    <input type="number" id="c" min="6">

    <label>Cards Drawn (D)</label>
    <input type="number" id="d">

    <button onclick="sendStep1()">Next</button>
</div>

<script>
function sendStep1() {
    fetch("byteguess_step1_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            step: 1,
            c: c.value,
            d: d.value
        })
    })
    .then(res => res.json())
    .then(data => alert(data.message));
}
</script>

</body>
</html>
