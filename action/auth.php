<?php
require_once "../includes/function.php";
require_once "../includes/database.php";

// Check request method first
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $queryString = http_build_query(["error" => "invalid_request_method"]);
    redirect("../driver/d_login.php", $queryString);
    exit;
}

// Validate inputs
$email = trim($_POST["email"]);
$password = $_POST["password"];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $queryString = http_build_query(["error" => "invalid_email"]);
    redirect("../driver/d_login.php", $queryString);
    exit;
}

if (empty($email) || empty($password)) {
    $queryString = http_build_query(["error" => "empty_fields"]);
    redirect("../driver/d_login.php", $queryString);
    exit;
}

// Use prepared statement to prevent SQL injection
$query = "SELECT * FROM drivers WHERE email = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $queryString = http_build_query(["error" => "invalid_credentials"]);
    redirect("../driver/d_login.php", $queryString);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    $queryString = http_build_query(["error" => "invalid_credentials"]);
    redirect("../driver/d_login.php", $queryString);
    exit;
}

// Start session and set session variables
session_start();
$_SESSION['driver_id'] = $user['driver_id']; // Changed from userId to driver_id
$_SESSION['driver_name'] = $user['userName'];
$_SESSION['driver_email'] = $user['email'];
// Don't store password in session
$_SESSION['driver_image'] = $user['yourImage']; 

// Successful login - redirect to profile
redirect("../driver/driverProfile.php");
exit;
?>