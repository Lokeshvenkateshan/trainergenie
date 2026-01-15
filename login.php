<?php
session_start();

if (isset($_SESSION["team_id"])) {
    header("Location: dashboard.php");
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
            // redirect after login
            setTimeout(() => {
                window.location.href = "dashboard.php";
            }, 1000);
        }
    });
});
</script>

</body>
</html>
