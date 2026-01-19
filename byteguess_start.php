<?php
session_start();
if (!isset($_SESSION['team_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ByteGuess – Create Organization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .box {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,.15);
        }
        input, textarea, button {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            box-sizing: border-box;
        }
        button {
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <form method="post" action="logout.php" style="position:absolute; top:20px; right:20px;">
    <button type="submit" style="
        padding:6px 12px;
        background:#dc3545;
        color:#fff;
        border:none;
        border-radius:4px;
        cursor:pointer;
    ">
        Logout
    </button>
</form>


<div class="box">
    <h3>Create Organization</h3>

    <label>Organization Name</label>
    <input type="text" id="ig_name" placeholder="Enter organization name">

    <label>Description</label>
    <textarea id="ig_description" placeholder="Enter description"></textarea>

    <button onclick="createOrg()">Start</button>
</div>

<script>
function createOrg() {
    const igName = document.getElementById("ig_name").value.trim();
    const igDesc = document.getElementById("ig_description").value.trim();

    if (igName === "") {
        alert("Organization name is required");
        return;
    }

    fetch("byteguess_start_action.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "ig_name=" + encodeURIComponent(igName) +
            "&ig_description=" + encodeURIComponent(igDesc)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = "byteguess_step1.php";
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        alert("Something went wrong");
        console.error(err);
    });
}
</script>

</body>
</html>
