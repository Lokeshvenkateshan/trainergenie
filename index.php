<?php
$pageTitle = "TrainerHub Dashboard";
$pageCSS   = "./assets/styles/index.css"; 

require "layout/header.php";


require "./data/templates.php";
    

?>

 <div class="index-container">
    <div class="bg-img-container">
         <img class="bg-pic" src="./assets/images/index-bg-pic.png" alt="index-bg-pic">


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
        <?php foreach (array_slice($templates, 0, 4) as $template): ?>
            <div class="template-card">

                <div class="card-img">
                    <span class="tag"><?= $template['tag'] ?></span>
                    <img src="<?= $template['image'] ?>" alt="">
                </div>

                <div class="card-body">
                    <h3><?= $template['title'] ?></h3>
                    <p><?= $template['desc'] ?></p>
                    <button onclick="window.location.href='byteguess_exercise.php'">Create Session</button>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</section>



 </div>

<?php require "layout/footer.php"; ?>
