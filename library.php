<?php
session_start();
require "include/dataconnect.php";

if (!isset($_SESSION['team_id'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Library";
$pageCSS   = "/assets/styles/library.css";

require "layout/header.php";

$team_id = $_SESSION['team_id'];

/* STEP 1: Get ig_ids for this team */
$igIds = [];

$stmt = $conn->prepare("
    SELECT ig_id
    FROM byteguess_category
    WHERE ig_team_pkid = ?
      AND ig_status = 1
");
$stmt->bind_param("i", $team_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $igIds[] = $row['ig_id'];
}

/* If no categories, no games */
$games = [];

if (!empty($igIds)) {
    $placeholders = implode(',', array_fill(0, count($igIds), '?'));
    $types = str_repeat('i', count($igIds));

    $query = "
        SELECT cg_id, cg_name, cg_description
        FROM card_group
        WHERE byteguess_pkid IN ($placeholders)
          AND cg_status = 1
        ORDER BY cg_id DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$igIds);
    $stmt->execute();
    $games = $stmt->get_result();
}
?>

<div class="library-page">

    <h2 class="library-title">Game Library</h2>

    <div class="card-grid">

        <?php if (!empty($games) && $games->num_rows > 0): ?>
            <?php while ($game = $games->fetch_assoc()): ?>
                <div class="game-card">

                    <h3><?= htmlspecialchars($game['cg_name']) ?></h3>

                    <p>
                        <?= $game['cg_description']
                            ? htmlspecialchars($game['cg_description'])
                            : 'No description provided.' ?>
                    </p>

                    <div class="card-actions">
                        
                        <a href="library/view_game.php?cg_id=<?= $game['cg_id'] ?>"
                           class="btn secondary">
                            View
                        </a>

                        <!-- <a href="byteguess_step1.php?cg_id=<?= $game['cg_id'] ?>"
                           class="btn primary">
                            Open
                        </a> -->
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No games found for your account.</p>
        <?php endif; ?>

    </div>
</div>

<?php require "layout/footer.php"; ?>
