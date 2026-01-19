<?php
include("include/session_check.php");
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
    <a href="logout.php">Logout</a>

    <h2>Welcome, <?php echo $_SESSION["team_name"]; ?> </h2>
    <h2>Welcome, <?php echo $_SESSION["team_id"]; ?> </h2>

    <h2>AI Learning Card Game</h2>

    <!-- STEP 1 -->
    <div class="box" id="step1">
        <h3>Step 1: Game Structure</h3>

        <label>Total Cards (C)</label><br>
        <input type="number" id="c" min="6"><br><br>

        <label>Cards Drawn (D)</label><br>
        <input type="number" id="d"><br><br>

        <button onclick="sendStep1()">Next</button>
    </div>

    <!-- STEP 2 -->
    <div class="box hidden" id="step2">
        <h3>Step 2: Game Scenario</h3>

        <label>Training Topic</label><br>
        <input type="text" id="topic"><br><br>

        <label>Industry</label><br>
        <input type="text" id="industry"><br><br>

        <label>Card Game Objective</label><br>
        <input type="text" id="objective"><br><br>

        <button onclick="sendStep2()">Next</button>
    </div>

    <!-- RESPONSE -->
    <div class="box">
        <h3>AI Response</h3>
        <textarea id="response" readonly></textarea>
    </div>

    <script src="js/game.js"></script>
</body>

</html>