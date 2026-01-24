<?php
$pageTitle = "TrainerGenie Dashboard";
$pageCSS   = "./assets/styles/index.css";

require "layout/header.php";


require "include/dataconnect.php";

$stmt = $conn->prepare("
    SELECT ex_id, ex_name, ex_des, ex_tag, ex_image, ex_type
    FROM genie_exercises
    ORDER BY ex_id ASC
    LIMIT 4
");
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="index-container">
    <div class="bg-img-container">
        <img class="bg-pic" src="./upload-images/index-bg-pic.png" alt="index-bg-pic">


        <div class="bg-text">
            <h1>Ready to empower your team?
            </h1>
            <p> Select a proven template below or start from scratch to build interactive training sessions in minutes.</p>
        </div>

    </div>

    <section class="templates-section">

        <div class="section-header">
            <h2>Popular Exercise Templates</h2>
            <a href="all-templates.php" class="view-all">
                View all templates →
            </a>
        </div>

        <div class="templates-grid">
            <?php if ($result->num_rows === 0): ?>
                <p>No templates available.</p>
            <?php else: ?>
                <?php while ($template = $result->fetch_assoc()): ?>
                    <div class="template-card">

                        <div class="card-img">
                            <span class="tag"><?= htmlspecialchars($template['ex_tag']) ?></span>
                            <img src="./upload-images/<?= htmlspecialchars($template['ex_image']) ?>" alt="">
                        </div>

                        <div class="card-body">
                            <h3><?= htmlspecialchars($template['ex_name']) ?></h3>
                            <p><?= htmlspecialchars($template['ex_des']) ?></p>

                            <?php
                            $exerciseRoutes = [
                                1 => "digihunt_exercise.php",
                                2 => "byteguess_exercise.php",
                                3 => "pixelquest_exercise.php",
                                4 => "bitbargain_exercise.php"
                            ];

                            $redirectPage = $exerciseRoutes[$template['ex_type']] ?? "index.php";
                            ?>

                            <button onclick="window.location.href='<?= $redirectPage ?>'">
                                Create Session
                            </button>
                        </div>


                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

    </section>




</div>

<?php require "layout/footer.php"; ?>