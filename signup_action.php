<?php
session_start();
header("Content-Type: application/json");
include("include/dataconnect.php");

$team_name  = trim($_POST["team_name"] ?? "");
$team_login = trim($_POST["team_login"] ?? "");
$team_org   = trim($_POST["team_org"] ?? "");
$password   = trim($_POST["team_password"] ?? "");
$confirm    = trim($_POST["confirm_password"] ?? "");

if ($password !== $confirm) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

$encoded_password = base64_encode($password);
$team_creatby = 1;

// check email
$check = $conn->prepare("SELECT team_id FROM team WHERE team_login = ?");
$check->bind_param("s", $team_login);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit;
}

// insert user
/* $stmt = $conn->prepare("
    INSERT INTO team (team_name, team_login, team_password, team_creatby)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("sssi", $team_name, $team_login, $encoded_password, $team_creatby); */

$stmt = $conn->prepare("
    INSERT INTO team (team_name, team_login, team_org, team_password, team_creatby)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssi",
    $team_name,
    $team_login,
    $team_org,
    $encoded_password,
    $team_creatby
);


if ($stmt->execute()) {

    $team_id = $stmt->insert_id;
    // LOGIN AFTER SIGNUP
    $_SESSION["team_id"] = $stmt->insert_id;
    $_SESSION["team_name"] = $team_name;
    $_SESSION["last_activity"] = time();


    $catStmt = $conn->prepare("
        INSERT INTO byteguess_category
        (ig_team_pkid, ig_name, ig_description, ig_status, createddate)
        VALUES (?, ?, ?, ?, NOW())
    ");

    $ig_description = ""; // optional / empty
    $ig_status = 1;

    $catStmt->bind_param(
        "issi",
        $team_id,
        $team_org,
        $ig_description,
        $ig_status
    );

    $catStmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Signup successful. Redirecting..."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Signup failed"
    ]);
}
