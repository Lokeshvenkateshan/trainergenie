<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="assets/css/navbar.css">

<nav class="navbar">
    <!-- LEFT : LOGO -->
    <div class="nav-left">
        <img src="assets/images/logo.png" class="logo" alt="Logo">
    </div>

    <!-- CENTER : MENU -->
    <div class="nav-center">
        <a href="index.php"
           class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
           Home
        </a>

        <a href="byteguess_step1.php"
           class="<?= $currentPage === 'byteguess_step1.php' ? 'active' : '' ?>">
           My Exercises
        </a>

        <a href="library.php"
           class="<?= $currentPage === 'library.php' ? 'active' : '' ?>">
           Library
        </a>

        <a href="reports.php"
           class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>">
           Reports
        </a>
    </div>

    <!-- RIGHT : PROFILE + LOGOUT -->
    <div class="nav-right">
        <img src="assets/images/user.png" class="nav-user" alt="User">

        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>
