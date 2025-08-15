<?php
require_once 'auth_check.php';
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)$_POST['order_id'];
    $driver_id = (int)$_POST['driver_id'];
    $admin_id = (int)$_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];

    // 1. Assign driver
    $connection->query("
        UPDATE orders 
        SET driver_id = $driver_id, 
            status = 'assigned',
            assigned_at = NOW() 
        WHERE order_id = $order_id
    ");

    // 2. Mark driver as busy
    $connection->query("
        UPDATE drivers 
        SET is_available = 0 
        WHERE driver_id = $driver_id
    ");

    // 3. Log this action
    $connection->query("
        INSERT INTO admin_logs 
        (admin_id, action_type, target_id, ip_address) 
        VALUES ($admin_id, 'driver_assigned', $order_id, '$ip')
    ");

    header("Location: dashboard.php?success=1");
    exit;
}
?>