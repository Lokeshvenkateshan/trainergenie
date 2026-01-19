<?php
session_start();

define("SESSION_TIMEOUT", 30000); // 15 minutes

// If user not logged in
if (!isset($_SESSION["team_id"])) {
    header("Location: login.php");
    exit;
}

// Check idle timeout
if (isset($_SESSION["last_activity"]) &&
    (time() - $_SESSION["last_activity"] > SESSION_TIMEOUT)) {

    // Session expired
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION["last_activity"] = time();
