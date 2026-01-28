<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? "Admin Panel" ?></title>

  

    <!-- Page Specific CSS -->
    <?php if (!empty($pageCSS)): ?>
        <link rel="stylesheet" href="<?= $pageCSS ?>">
    <?php endif; ?>
</head>
<body>

