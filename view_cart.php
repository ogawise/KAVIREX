<?php
session_start();
require_once 'includes/database.php';

// Initialize variables
$items = [];
$total = 0;

// Fetch cart items from session
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $stmt = $connection->prepare("
        SELECT product_id, name, price, image_path 
        FROM products 
        WHERE product_id IN ($placeholders)
    ");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        while ($product = $result->fetch_assoc()) {
            $items[] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image_path' => $product['image_path'],
                'quantity' => $_SESSION['cart'][$product['product_id']]
            ];
            $total += $product['price'] * $_SESSION['cart'][$product['product_id']];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - KAVIREX</title>
    <link rel="stylesheet" href="assets/styles/style.css">
    <link rel="stylesheet" href="assets/styles/cart.css">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo.png">
    <style>
        .cart_count { display: none; }
        .cart_summary { border: none; }
        #empty-cart-message { 
            text-align: center; 
            padding: 40px; 
            font-size: 1.2em; 
            a{
            text-decoration:underline;
            }
        }
        .cart-item-removing {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <section style="min-height: 90vh;">
        <?php include('includes/header.php'); ?>
        <h1>Your Cart</h1>
        <main class="cart-container">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                <div class="cart-item" data-product-id="<?= $item['product_id'] ?>" data-price="<?= $item['price'] ?>">
                    <img src="<?= $item['image_path'] ?>" alt="<?= $item['name'] ?>">
                    <div class="item-details">
                        <h3><?= $item['name'] ?></h3>
                        <p class="price">FCFA <?= number_format($item['price'], 2) ?></p>
                    </div>
                    <div class="item-controls">
                        <div class="qty-controls">
                            <button class="qty-btn minus">-</button>
                            <span class="qty-display"><?= $item['quantity'] ?></span>
                            <button class="qty-btn plus">+</button>
                        </div>
                        <button class="remove-btn" data-product-id="<?= $item['product_id'] ?>">Remove</button>
                        <span class="subtotal">FCFA <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <p id="empty-cart-message">
                Your cart is empty. <br>
                <a href="products.php">Browse products</a>
            </p>
        </main>
        
        <div class="action">
            <div class="cart_total">
                Total: FCFA <?= number_format($total, 2) ?>
            </div>
            <div class="cart_summary">g
                <span class="cart_count"><?= array_sum($_SESSION['cart'] ?? []) ?></span>
            </div>
            <a href="order_status.php" id="place-order-btn" class="checkout-btn" 
               style="display: <?= empty($items) ? 'none' : 'block' ?>">
                Place Order
            </a>
        </div>
    </section>

    <?php include('includes/footer.php'); ?>
    
    <script>
        // Pass initial cart data to JavaScript
        const initialCart = <?= json_encode($_SESSION['cart'] ?? []) ?>;
    </script>
    <script src="./assets/javascript/view_cart.js" defer></script>
</body>
</html>