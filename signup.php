<!DOCTYPE html>
<html>
<head>
    <title>Team Signup</title>
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

<!-- Load external JS -->
<script src="js/signup.js"></script>

</body>
</html>
