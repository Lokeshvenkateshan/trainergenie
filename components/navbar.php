<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="/trainergenie/assets/css/navbar.css">

<nav class="navbar">
    <!-- LEFT : LOGO -->
    <div class="nav-left">
        <img src="/trainergenie/assets/images/logo.png" class="logo" alt="Logo">
    </div>

    <!-- CENTER : MENU -->
    <div class="nav-center">
        <a href="/trainergenie/index.php"
           class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
           Home
        </a>

        <a href="/trainergenie/byteguess_exercise.php"
           class="<?= $currentPage === 'byteguess_exercise.php' ? 'active' : '' ?>">
           My Exercises
        </a>

        <a href="/trainergenie/library.php"
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
        <img src="/trainergenie/assets/images/user.png" class="nav-user" alt="User">

        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</nav>
