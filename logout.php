<?php
session_start();

// Determine user type from session
$user_type = '';
if (isset($_SESSION['admin_logged_in'])) {
    $user_type = 'admin';
} elseif (isset($_SESSION['driver_id'])) {
    $user_type = 'driver';
} elseif (isset($_SESSION['user_id'])) {
    $user_type = 'customer';
}

// Unset and destroy session
$_SESSION = array();
session_destroy();

// Redirect based on user type
switch ($user_type) {
    case 'admin':
        header("Location: kavadm/login.php");
        break;
    case 'driver':
        header("Location: driver/d_login.php");
        break;
    default:
        header("Location: userlogin.php");
}

exit;
?>