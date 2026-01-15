<!DOCTYPE html>
<html>
<head>
    <title>Team Signup (AJAX)</title>
</head>
<body>

<h2>Team Signup</h2>

<form id="signupForm">

    <label>Name</label><br>
    <input type="text" name="team_name" required><br><br>

    <label>Email</label><br>
    <input type="email" name="team_login" required><br><br>

    <label>Password</label><br>
    <input type="password" name="team_password" id="team_password" required><br><br>

    <label>Confirm Password</label><br>
    <input type="password" name="confirm_password" id="confirm_password" required><br><br>

    <button type="submit">Sign Up</button>
</form>

<p id="msg"></p>

<script>
document.getElementById("signupForm").addEventListener("submit", function(e) {
    e.preventDefault(); // stop normal submit

    const password = document.getElementById("team_password").value;
    const confirm  = document.getElementById("confirm_password").value;
    const msg      = document.getElementById("msg");

    if (password !== confirm) {
        msg.style.color = "red";
        msg.innerText = "Password and Confirm Password do not match";
        return;
    }

    const formData = new FormData(this);

    fetch("signup_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        msg.style.color = data.status === "success" ? "green" : "red";
        msg.innerText = data.message;

        if (data.status === "success") {
            document.getElementById("signupForm").reset();
        }
    });
});
</script>

</body>
</html>
