<?php
session_start();

if (
    !isset($_SESSION['cg_id']) ||
    !isset($_SESSION['messages']) ||
    !isset($_SESSION['c']) ||
    !isset($_SESSION['d'])
) {
    header("Location: byteguess_step3.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Step 4 - Generate Cards</title>
</head>
<body>

<h3>Step 4: Card Structure</h3>

<label>Card structure (E)</label><br>
<input type="text" id="E" style="width:400px"><br><br>

<button onclick="generateCards()">Generate Cards</button>

<script>
function generateCards() {
    fetch("byteguess_step4_action.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({ E: E.value })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            // console.log("AI FULL RESPONSE (DEV):\n", data.ai_raw);
            // alert("Cards generated and saved");
            window.location.href ="byteguess_step5.php";
        } else {
            alert(data.message);
        }
    });
}
</script>

</body>
</html>
