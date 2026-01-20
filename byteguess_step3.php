<?php
session_start();

if (
    !isset($_SESSION['ig_id']) ||
    !isset($_SESSION['cg_id']) ||
    !isset($_SESSION['messages'])
) {
    header("Location: byteguess_step2.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Step 3 - Game Context</title>
</head>
<body>

<h3>Step 3: Game Context</h3>

<label>Training Topic / Participants (A)</label><br>
<input type="text" id="A" style="width:400px"><br><br>

<label>Industry (A1)</label><br>
<input type="text" id="A1" style="width:400px"><br><br>

<label>Game Objective (B)</label><br>
<input type="text" id="B" style="width:400px"><br><br>

<label>Hypothesis / Assessment (B1)</label><br>
<input type="text" id="B1" style="width:400px"><br><br>

<button onclick="submitStep3()">Next</button>

<script>
function submitStep3() {
    fetch("byteguess_step3_action.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({
            A: A.value,
            A1: A1.value,
            B: B.value,
            B1: B1.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = "byteguess_step4.php";
        } else {
            alert(data.message);
        }
    });
}
</script>

</body>
</html>
