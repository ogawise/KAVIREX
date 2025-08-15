<?php
header('Content-Type: application/json');
session_start();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['product_id'])) {
        throw new Exception('Missing product ID');
    }
    
    $productId = (int)$input['product_id'];
    
    // Check if item exists in cart
    if (!isset($_SESSION['cart'][$productId])) {
        throw new Exception('Item not in cart');
    }
    
    // Remove item
    unset($_SESSION['cart'][$productId]);
    
    // Calculate new total
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        require_once '../includes/database.php';
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        $stmt = $connection->prepare("
            SELECT product_id, price FROM products 
            WHERE product_id IN ($placeholders)
        ");
        $stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($product = $result->fetch_assoc()) {
            $total += $product['price'] * $_SESSION['cart'][$product['product_id']];
        }
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'new_total' => number_format($total, 2),
        'cart_count' => array_sum($_SESSION['cart'])
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}