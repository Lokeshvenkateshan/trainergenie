<?php
session_start();

if (!isset($_SESSION['cg_id'])) {
    header("Location: byteguess_step1.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Step 7 – Game Guidelines</title>
    
</head>
<body>

<div >
    <h3>Step 7: Game Guidelines</h3>
    <button onclick="generateGuidelines()">Give Guidelines</button>
</div>

<script>
function generateGuidelines() {
    fetch("byteguess_step7_action.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            console.log("📘 HOW TO PLAY GUIDELINES:");
            console.log(data.guidelines);
        } else {
            console.error(" Error:", data.message);
        }
    })
    .catch(err => console.error(" Fetch error:", err));
}
</script>

</body>
</html>
