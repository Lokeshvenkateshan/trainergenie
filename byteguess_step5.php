<?php
session_start();

if (!isset($_SESSION['cg_id']) || !isset($_SESSION['messages'])) {
    header("Location: byteguess_step4.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Step 5 - Hypothesis Options</title>
</head>
<body>

<h3>Step 5: Hypothesis Options</h3>

<label>Numeric value for Fully Right (F1)</label><br>
<input type="number" id="f1" min="1"><br><br>

<label>Numeric value for Partially Right (F2)</label><br>
<input type="number" id="f2" min="1"><br><br>

<label>Numeric value for Distractors (F3)</label><br>
<input type="number" id="f3" min="1"><br><br>

<button onclick="generateHypotheses()">Generate Options</button>

<script>
function generateHypotheses() {
    fetch("byteguess_step5_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            f1: f1.value,
            f2: f2.value,
            f3: f3.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            console.log("===== STEP 5 AI RESPONSE =====");
            // console.log(data.ai_raw);
            window.location.href ="byteguess_step6.php";
        } else {
            console.error(data.message);
        }
    })
    .catch(err => console.error("Fetch error:", err));
}
</script>

</body>
</html>
