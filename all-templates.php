<?php
$pageTitle = "All Templates";
$pageCSS   = "../assets/styles/index.css";

require "layout/header.php";
require "./data/templates.php";

?>



<section class="templates-section">

    <div class="section-header">
        <h2>All Exercise Templates</h2>
    </div>

    <div class="templates-grid">
        <?php foreach ($templates as $template): ?>
            <div class="template-card">

                <div class="card-img">
                    <span class="tag"><?= $template['tag'] ?></span>
                    <img src="<?= $template['image'] ?>" alt="">
                </div>

                <div class="card-body">
                    <h3><?= $template['title'] ?></h3>
                    <p><?= $template['desc'] ?></p>
                    <button onclick="window.location.href='byteguess_step1.php'">Create Session</button>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</section>


<?php require "layout/footer.php"; ?>

