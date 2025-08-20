<?php
require_once "../includes/database.php";
session_start();

// Check if driver is logged in
if (!isset($_SESSION['driver_id'])) {
    header("Location: d_login.php");
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Fetch driver info using prepared statement
$sql = "SELECT yourImage, userName, email, phoneNumber FROM drivers WHERE driver_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();

if (!$driver) {
    echo "Driver not found.";
    exit;
}

// Get current active order with customer details and order items
$activeOrderQuery = "
    SELECT 
        o.order_id, 
        o.status, 
        o.created_at, 
        o.customer_address,
        u.fullName AS customer_name,
        u.phoneNumber AS customer_phone,
        u.email AS customer_email,
        COUNT(oi.item_id) AS total_items,
        SUM(oi.quantity * oi.price) AS order_total
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.driver_id = ? AND o.status IN ('assigned', 'on_transit') 
    GROUP BY o.order_id
    LIMIT 1
";
$stmt = $connection->prepare($activeOrderQuery);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$activeOrder = $stmt->get_result()->fetch_assoc();

// Get order items for active order
$orderItems = [];
if ($activeOrder) {
    $itemsQuery = "
        SELECT 
            p.name AS product_name,
            oi.quantity,
            oi.price,
            (oi.quantity * oi.price) AS item_total
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
        ORDER BY p.name
    ";
    $stmt = $connection->prepare($itemsQuery);
    $stmt->bind_param("i", $activeOrder['order_id']);
    $stmt->execute();
    $orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get all orders for the driver
$allOrdersQuery = "
    SELECT 
        o.order_id, 
        o.status, 
        o.created_at, 
        o.customer_address,
        u.fullName AS customer_name,
        COUNT(oi.item_id) AS total_items,
        SUM(oi.quantity * oi.price) AS order_total
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.driver_id = ? 
    GROUP BY o.order_id
    ORDER BY o.order_id DESC
";
$stmt = $connection->prepare($allOrdersQuery);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$allOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Determine driver status
$status = $activeOrder ? "Busy" : "Available";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver's Profile</title>
    <link rel="stylesheet" href="../assets/styles/style.css">
    <link rel="stylesheet" href="../assets/styles/d_style.css">  
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">

</head>
<body>
    <?php include('../includes/d_header.php') ?>

    <main style="min-height: 70vh; margin-top: 10px;"> 
        <div class="profiles">
            <img class="picture" src="../profiles/<?= htmlspecialchars($driver['yourImage'] ?? 'default.jpg') ?>" alt="profile picture">
            <h2><?= htmlspecialchars($driver['userName']) ?></h2>
            <p class="email_info">Email: <?= htmlspecialchars($driver['email']) ?></p>
            <p class="email_info">Phone: <?= htmlspecialchars($driver['phoneNumber'] ?? 'Not provided') ?></p>

            <div class="profile-extra">
                <p>Status: <span class="status"><?= $status ?></span></p>
                
                <?php if ($activeOrder): ?>
                    <div class="current-order">
                        <h3>Current Order #<?= $activeOrder['order_id'] ?></h3>
                        
                        <!-- Customer Information -->
                        <div class="customer-info">
                            <h4>👤 Customer Details</h4>
                            <div class="info-item">
                                <strong>Name:</strong> <?= htmlspecialchars($activeOrder['customer_name']) ?>
                            </div>
                            <div class="info-item">
                                <strong>Phone:</strong> 
                                <a href="tel:<?= htmlspecialchars($activeOrder['customer_phone']) ?>" 
                                   class="contact-btn">
                                   📞 <?= htmlspecialchars($activeOrder['customer_phone']) ?>
                                </a>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong> <?= htmlspecialchars($activeOrder['customer_email']) ?>
                            </div>
                            <div class="info-item">
                                <strong>Address:</strong> <?= htmlspecialchars($activeOrder['customer_address']) ?>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <div class="order-items">
                            <h4>🛒 Order Items (<?= $activeOrder['total_items'] ?> items)</h4>
                            <?php foreach ($orderItems as $item): ?>
                                <div class="item-row">
                                    <span><?= htmlspecialchars($item['product_name']) ?></span>
                                    <span><?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></span>
                                    <span><?= number_format($item['item_total'], 2) ?>Frs</span>
                                </div>
                            <?php endforeach; ?>
                            <div class="item-row total-row">
                                <span><strong>Order Total:</strong></span>
                                <span></span>
                                <span><strong><?= number_format($activeOrder['order_total'], 2) ?>Frs</strong></span>
                            </div>
                        </div>
                        
                        <!-- Order Progress Timeline -->
                        <div class="timeline">
                            <div class="timeline-step">
                                <div class="timeline-dot <?= in_array($activeOrder['status'], ['assigned', 'on_transit', 'delivered']) ? 'active' : '' ?>"></div>
                                <div>Order Assigned</div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-dot <?= in_array($activeOrder['status'], ['on_transit', 'delivered']) ? 'active' : '' ?>"></div>
                                <div>On Transit</div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-dot <?= $activeOrder['status'] === 'delivered' ? 'active' : '' ?>"></div>
                                <div>Delivered</div>
                            </div>
                        </div>
                        
                        <!-- Status Update Buttons -->
                        <div class="order-actions">
                            <?php if ($activeOrder['status'] === 'assigned'): ?>
                                <button onclick="updateOrderStatus(<?= $activeOrder['order_id'] ?>, 'on_transit')" 
                                        class="status-btn">
                                    🚚 Start Delivery
                                </button>
                            <?php elseif ($activeOrder['status'] === 'on_transit'): ?>
                                <button onclick="updateOrderStatus(<?= $activeOrder['order_id'] ?>, 'delivered')" 
                                        class="status-btn">
                                    ✅ Mark as Delivered
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No active orders assigned</p>
                    <hr>
                <?php endif; ?>
                
                <!-- Order History -->
                <div id="orders-section">
                    <h3>📋 Order History</h3>
                    <?php if (count($allOrders) > 0): ?>
                        <ul>
                            <?php foreach ($allOrders as $order): ?>
                                <li>
                                    <strong>Order #<?= $order['order_id'] ?></strong><br>
                                    <strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
                                    <strong>Status:</strong> <span class="status"><?= ucfirst($order['status']) ?></span><br>
                                    <strong>Date:</strong> <?= date('M j, Y', strtotime($order['created_at'])) ?><br>
                                    <strong>Items:</strong> <?= $order['total_items'] ?> items<br>
                                    <strong>Total:</strong> <?= number_format($order['order_total'], 2) ?>Frs<br>
                                    <strong>Address:</strong> <?= htmlspecialchars($order['customer_address']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No order history found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include('../includes/d_footer.php') ?>
    <script>
    function updateOrderStatus(orderId, newStatus) {
        const button = event.target;
        button.disabled = true;
        button.textContent = 'Processing...';
        
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `order_id=${orderId}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (newStatus === 'delivered') {
                    // Update driver status display if order was delivered
                    const driverStatusElement = document.querySelector('.profile-extra .status');
                    if (driverStatusElement) {
                        driverStatusElement.textContent = 'Available';
                    }
                }
                location.reload(); // Reload to show updated status
            } else {
                alert(data.error || 'Failed to update status');
                button.disabled = false;
                button.textContent = newStatus === 'on_transit' ? 'Start Delivery' : 'Mark as Delivered';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
            button.disabled = false;
            button.textContent = newStatus === 'on_transit' ? 'Start Delivery' : 'Mark as Delivered';
        });
    }
    </script>
</body>
</html>