<?php
session_start();
if (!isset($_SESSION['team_id'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "New Exercise";
$pageCSS   = "/assets/styles/exercise/byteguess_exercise.css";
require "layout/header.php";
?>

<div class="exercise-wrapper">

    <div class="step-bar">
        <?php for ($i=1; $i<=7; $i++): ?>
            <div class="step-dot" id="step-dot-<?= $i ?>"><?= $i ?></div>
        <?php endfor; ?>
    </div>

    <div class="wizard-box" id="wizard-box">
        <!-- JS loads steps here -->
    </div>

    <div class="loader" id="loader">Processing… please wait</div>
</div>

<script src="js/byteguess_exercise.js"></script>

<?php require "layout/footer.php"; ?>
