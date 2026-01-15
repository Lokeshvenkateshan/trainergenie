<?php
session_start();

if (isset($_SESSION["team_id"])) {
    // User already logged in
    header("Location: dashboard.php");
    exit;
} else {
    // Not logged in
    header("Location: login.php");
    exit;
}
