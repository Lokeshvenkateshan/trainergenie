<?php
session_start();
if (!isset($_SESSION['team_id'])) {
    header("Location: login.php");
    exit;
}

require "layout/header.php";
?>

<main class="exercise-container">
    <div class="wizard">

        <!-- STEP PROGRESS -->
<div class="wizard-steps">

    <!-- step markers (JS logic only) -->
    <?php for ($i = 1; $i <= 6; $i++): ?>
        <span
            class="step-dot <?= $i === 1 ? 'active' : '' ?>"
            id="step-dot-<?= $i ?>"
        ></span>
    <?php endfor; ?>

    <!-- step text -->
    <div class="step-text">
        <?php for ($i = 1; $i <= 6; $i++): ?>
            <span class="step-label" data-step="<?= $i ?>">
                Step <?= $i ?> of 6
            </span>
        <?php endfor; ?>
    </div>

    <!-- progress bar -->
    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>

</div>

        <!-- MAIN CONTENT -->
        <section id="wizard-box" aria-live="polite"></section>

        <!-- LOADER -->
        <div id="loader">Processing…</div>

    </div>
</main>


<link rel="stylesheet" href="assets/styles/exercise/byteguess_exercise.css?v=<?= time() ?>">

<script src="js/byteguess_exercise.js"></script>

<?php require "layout/footer.php"; ?>
