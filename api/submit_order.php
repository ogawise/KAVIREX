<?php
header('Content-Type: application/json');
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../includes/database.php';

// Authentication Check
if (empty($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(['error' => 'login_required', 'redirect' => 'userlogin.php']));
}

//  Cart Validation
if (empty($_SESSION['cart'])) {
    http_response_code(400);
    die(json_encode(['error' => 'empty_cart']));
}

// Get customer address from users table
$address_stmt = $connection->prepare("SELECT Address FROM users WHERE user_id = ?");
$address_stmt->bind_param("i", $_SESSION['userId']);
$address_stmt->execute();
$address_result = $address_stmt->get_result();

if ($address_result->num_rows === 0) {
    http_response_code(400);
    die(json_encode(['error' => 'address_not_found']));
}

$customer_address = $address_result->fetch_assoc()['Address'];

try {
    $connection->begin_transaction();

    //  Calculate Total
    $total = 0;
    $product_prices = [];
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $price_stmt = $connection->prepare("SELECT price FROM products WHERE product_id = ?");
        $price_stmt->bind_param("i", $product_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        
        if ($price_result->num_rows === 0) {
            throw new Exception("Product ID $product_id not found");
        }
        
        $price = $price_result->fetch_assoc()['price'];
        $product_prices[$product_id] = $price;
        $total += $price * $quantity;
    }

    // Insert Order 
    $order_stmt = $connection->prepare("
        INSERT INTO orders (user_id, status, created_at, customer_address)
        VALUES (?, 'pending', CURRENT_TIMESTAMP, ?)
    ");
    
    if (!$order_stmt) {
        throw new Exception("Order prepare failed: " . $connection->error);
    }
    
    $order_stmt->bind_param("is", $_SESSION['userId'], $customer_address);
    if (!$order_stmt->execute()) {
        throw new Exception("Order execute failed: " . $order_stmt->error);
    }
    
    $order_id = $connection->insert_id;

    //  Insert Order Items
    $item_stmt = $connection->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    
    if (!$item_stmt) {
        throw new Exception("Item prepare failed: " . $connection->error);
    }

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $item_stmt->bind_param("iiid", 
            $order_id, 
            $product_id, 
            $quantity, 
            $product_prices[$product_id]
        );
        
        if (!$item_stmt->execute()) {
            throw new Exception("Item execute failed: " . $item_stmt->error);
        }
    }

    //  Finalize
    unset($_SESSION['cart']);
    $connection->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'redirect' => 'order_status.php?order_id='.$order_id
    ]);

} catch (Exception $e) {
    if (isset($connection) && $connection->in_transaction) {
        $connection->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'error' => 'order_failed',
        'message' => $e->getMessage(),
        'debug_info' => [
            'user_id' => $_SESSION['userId'] ?? null,
            'cart_items' => $_SESSION['cart'] ?? null
        ]
    ]);
}