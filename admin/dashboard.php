<?php

$pageTitle = "TrainerGenie Admin Dashboard";
$pageCSS   = "./styles/dashboard.css";

session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require "layout/header.php";


$adminName = $_SESSION['admin_name'];
?>


<div class="dashboard">
    <div class="dashboard-card">
        <h1>Dashboard</h1>
        <p>Welcome, <strong><?= htmlspecialchars($adminName) ?></strong></p>

        <a href="dashboard.php?logout=1" class="logout-btn">Logout</a>
    </div>
</div>


<?php require "layout/footer.php"; ?>
