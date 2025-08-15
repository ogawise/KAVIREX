<?php
session_start();

// 1. Check if admin is logged in
if (!isset($_SESSION['is_admin'])) {
    header("Location: login.php?error=not_logged_in");
    exit;
}

// 2. Basic hijack protection
$current_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$current_ip = $_SERVER['REMOTE_ADDR'] ?? '';

// Only check these if they were previously set
if (isset($_SESSION['user_agent'])) {
    if ($_SESSION['user_agent'] !== $current_user_agent) {
        session_destroy();
        header("Location: login.php?error=session_error");
        exit;
    }
} else {
    // Set them if not exists
    $_SESSION['user_agent'] = $current_user_agent;
    $_SESSION['ip_address'] = $current_ip;
}

// 3. Verify admin still exists in DB 
require_once '../includes/database.php';
$stmt = $connection->prepare("SELECT 1 FROM admins WHERE admin_id = ? AND is_active = TRUE");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    session_destroy();
    header("Location: login.php?error=account_inactive");
    exit;
}
?>