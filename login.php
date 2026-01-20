<?php
session_start();
if (isset($_GET["timeout"])) {
    echo "<p style='color:red;'>Session expired. Please login again.</p>";
}

if (isset($_SESSION["team_id"])) {
    header("Location: byteguess_step1.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Login</title>
</head>
<body>

<h2>Team Login</h2>

<form id="loginForm">

    <label>Email</label><br>
    <input type="email" name="team_login" required><br><br>

    <label>Password</label><br>
    <input type="password" name="team_password" required><br><br>

    <button type="submit">Login</button>
</form>

<br>

<!-- Signup button -->
<button onclick="window.location.href='signup.php'">
    Create New Account
</button>

<p id="msg"></p>

<script>
document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const msg = document.getElementById("msg");
    const formData = new FormData(this);

    fetch("login_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        msg.style.color = data.status === "success" ? "green" : "red";
        msg.innerText = data.message;

        if (data.status === "success") {
            setTimeout(() => {
                window.location.href = "byteguess_step1.php";
            }, 1000);
        }
    });
});
</script>

</body>
</html>
