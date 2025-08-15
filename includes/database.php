<?php
if (headers_sent()) {
    die('Headers already sent in database.php');}
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "kavirex";

// Create connection
$connection = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Set charset to prevent security issues
$connection->set_charset("utf8mb4");
?>
