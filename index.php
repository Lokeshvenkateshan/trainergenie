<?php
$pageTitle = "TrainerHub Dashboard";
$pageCSS   = ""; // optional dashboard css

require "layout/header.php";
?>

<main class="dashboard" style="padding:30px;">
    <h1>Welcome to TrainerHub</h1>
    <p>Select an exercise or create a new one.</p>

    <a href="byteguess_step1.php" class="primary-btn">
        Start New Exercise
    </a>
</main>

<?php require "layout/footer.php"; ?>
