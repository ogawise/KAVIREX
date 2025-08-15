<?php
require_once "../includes/database.php";
session_start();

// Set JSON header
header('Content-Type: application/json');

// Verify driver is logged in
if (!isset($_SESSION['driver_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'login_required']));
}

// Validate required parameters
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    http_response_code(400);
    die(json_encode(['error' => 'missing_parameters']));
}

$driver_id = $_SESSION['driver_id'];
$order_id = (int)$_POST['order_id'];
$new_status = $_POST['status'];

// Validate allowed status transitions
$allowed_statuses = ['on_transit', 'delivered'];
if (!in_array($new_status, $allowed_statuses)) {
    http_response_code(400);
    die(json_encode(['error' => 'invalid_status']));
}

try {
    $connection->begin_transaction();

    // Verify the order belongs to this driver
    $verify_query = "SELECT status FROM orders WHERE order_id = ? AND driver_id = ?";
    $stmt = $connection->prepare($verify_query);
    $stmt->bind_param("ii", $order_id, $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("order_not_assigned");
    }
    
    $current_order = $result->fetch_assoc();
    $current_status = $current_order['status'];
    
    // Validate status transition sequence
    if ($new_status === 'on_transit' && $current_status !== 'assigned') {
        throw new Exception("invalid_transition_start");
    }
    
    if ($new_status === 'delivered' && $current_status !== 'on_transit') {
        throw new Exception("invalid_transition_complete");
    }

    // Update order status
    $update_query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if (!$stmt->execute()) {
        throw new Exception("update_failed");
    }
    
    // For delivered orders, set delivered_at timestamp and update driver availability
    if ($new_status === 'delivered') {
        // Update delivery timestamp
        $timestamp_query = "UPDATE orders SET delivered_at = CURRENT_TIMESTAMP WHERE order_id = ?";
        $stmt = $connection->prepare($timestamp_query);
        $stmt->bind_param("i", $order_id);
        
        if (!$stmt->execute()) {
            throw new Exception("timestamp_failed");
        }
        
        // Update driver availability to available (1)
        $driver_query = "UPDATE drivers SET is_available = 1 WHERE driver_id = ?";
        $stmt = $connection->prepare($driver_query);
        $stmt->bind_param("i", $driver_id);
        
        if (!$stmt->execute()) {
            throw new Exception("driver_status_update_failed");
        }
    }
    
    $connection->commit();
    
    // Return success response
    die(json_encode([
        'success' => true,
        'message' => "Status updated to $new_status",
        'new_status' => $new_status,
        'driver_available' => ($new_status === 'delivered') ? 1 : 0
    ]));

} catch (Exception $e) {
    if (isset($connection) && $connection->in_transaction) {
        $connection->rollback();
    }
    
    http_response_code(400);
    die(json_encode([
        'error' => $e->getMessage(),
        'debug' => [
            'driver_id' => $driver_id,
            'order_id' => $order_id,
            'attempted_status' => $new_status
        ]
    ]));
}