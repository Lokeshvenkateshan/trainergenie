<?php
session_start();

if (!isset($_SESSION['cg_id'])) {
    header("Location: byteguess_step5.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Step 6 – Answer Key</title>
    
</head>
<body>

<div >
    <h3>Step 6: Generate Answer Key</h3>
    <button onclick="generateAnswerKey()">Generate Answer Key</button>
</div>

<script>
function generateAnswerKey() {
    fetch("byteguess_step6_action.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            // console.log("Answer Key:");
            // console.log(data.answer_key);
            window.location.href ="byteguess_step7.php";
        } else {
            console.error("Error:", data.message);
        }
    })
    .catch(err => console.error("Fetch error:", err));
}
</script>

</body>
</html>
