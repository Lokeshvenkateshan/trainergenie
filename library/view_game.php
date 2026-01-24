<?php
session_start();
require "../include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: login.php");
    exit;
}

$cg_id = intval($_GET['cg_id'] ?? 0);
if ($cg_id <= 0) {
    die("Invalid game");
}

$pageTitle = "View Game";
$pageCSS   = "/library/view_game.css";
require "./../layout/header.php";

/* Fetch game details */
$stmt = $conn->prepare("
    SELECT cg_name, cg_description
    FROM card_group
    WHERE cg_id = ?
");
$stmt->bind_param("i", $cg_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) {
    die("Game not found");
}
?>

<div class="view-wrap">

    <!-- HEADER -->
    <div class="view-header">
        <a href="../library.php" class="back-link">← Back to Library</a>

        <div class="header-main">
            <h1><?= htmlspecialchars($game['cg_name']) ?></h1>
            <p><?= htmlspecialchars($game['cg_description']) ?></p>
        </div>
    </div>

    <!-- REVIEW COMPONENTS -->
    <h2 class="section-title">Review Components</h2>

    <div class="component-grid">

        <!-- Game Cards -->
        <div class="component-card">
            <div class="icon blue">🃏</div>
            <h3>Game Cards</h3>
            <p>Review and edit the generated cards.</p>

            <a href="game_cards.php?cg_id=<?= $cg_id ?>"
                class="link">
                Review & Edit →
            </a>
        </div>

        <!-- Answer Choices -->
        <div class="component-card">
            <div class="icon purple">☑️</div>
            <h3>Answer Choices</h3>
            <p>Review correct and distractor answers.</p>
            <a href="manage_choices.php?cg_id=<?= $cg_id ?>" class="link">
                Manage Choices →
            </a>
        </div>

        <!-- Answer Key -->
        <div class="component-card">
            <div class="icon green">🔑</div>
            <h3>Answer Key</h3>
            <p>Verify logic mapping and explanation.</p>
            <a href="result.php?cg_id=<?= $cg_id ?>" class="link">
                Verify →
            </a>
        </div>

        <!-- Clue (REPLACED PARTICIPANT VIEW) -->
        <div class="component-card">
            <div class="icon orange">🧩</div>
            <h3>Clue</h3>
            <p>Review the clue or hint provided to players.</p>
            <a href="#" class="link">View Clue →</a>
        </div>

    </div>
</div>

<?php require "../layout/footer.php"; ?>