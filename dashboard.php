<?php
session_start();

if (!isset($_SESSION["team_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION["team_name"]; ?> </h2>
<h2>Welcome, <?php echo $_SESSION["team_id"]; ?> </h2>

<a href="logout.php">Logout</a>

</body>
</html>
