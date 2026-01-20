<?php
session_start();

if (!isset($_SESSION['ig_id'])) {
    header("Location: byteguess_step1.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Step 2 - Game Structure</title>
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

<div class="box">
    <h3>Step 2: Game Structure</h3>

    <label>Total Cards (C)</label>
    <input type="number" id="c" min="6">

    <label>Cards Drawn (D)</label>
    <input type="number" id="d">

    <button onclick="submitStep2()">Next</button>
</div>

<script>
function submitStep2() {
    const c = document.getElementById("c").value;
    const d = document.getElementById("d").value;

    fetch("byteguess_step2_action.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({ c, d })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = "byteguess_step3.php";
        } else {
            alert(data.message);
        }
    });
}
</script>

</body>
</html>
