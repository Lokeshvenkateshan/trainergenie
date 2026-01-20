<?php
session_start();

if (isset($_SESSION["team_id"])) {
    // User already logged in
    header("Location: byteguess_step1.php");
    exit;
} else {
    // Not logged in
    header("Location: login.php");
    exit;
}
