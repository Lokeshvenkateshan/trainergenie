<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 🔐 Protect all pages except login */
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['team_id']) && $currentPage !== 'login.php') {
    header("Location: /login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'TrainerHub' ?></title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Global CSS -->
    <link rel="stylesheet" href="/assets/css/global.css">

    <!-- Page specific CSS -->
    <?php if (!empty($pageCSS)): ?>
        <link rel="stylesheet" href="<?= $pageCSS ?>">
    <?php endif; ?>
</head>

<body>
    

<?php include __DIR__ . "/../components/navbar.php"; ?>
