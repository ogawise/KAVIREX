<?php
session_start();
require_once 'includes/database.php';

function formatStatus($status) {
    $statusMap = [
        'pending' => 'Pending',
        'assigned' => 'Assigned to Driver',
        'on_transit' => 'On Transit',
        'delivered' => 'Delivered'
    ];
    return $statusMap[strtolower($status)] ?? ucfirst($status);
}

// Check login
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}
$refreshInterval = 30; // Refresh every 30 seconds
$nextRefresh = time() + $refreshInterval;
// Handle order listing if no specific order is requested
if (!isset($_GET['order_id'])) {
    // Fetch all orders for this user
    $orders_query = $connection->prepare("
        SELECT o.order_id, o.status, o.created_at 
        FROM orders o 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC
    ");
    $orders_query->bind_param("i", $_SESSION['userId']);
    $orders_query->execute();
    $orders = $orders_query->get_result()->fetch_all(MYSQLI_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Orders</title>
        <link rel="stylesheet" href="assets/styles/style.css">
         <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/logo.png">
        <style>
            .orders-container {
                max-width: 800px;
                margin: 2rem auto;
                padding: 1.5rem;
            }
            .order-card {
                background: white;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .order-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }
            .status-badge {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: bold;
            }
            .status-badge.pending { background: #FFF3CD; color: #856404; }
            .status-badge.assigned { background: #CCE5FF; color: #004085; }
            .status-badge.on_transit { background: #D4EDDA; color: #155724; }
            .status-badge.delivered { background: #D1E7DD; color: #0F5132; }
            .view-btn {
                display: inline-block;
                margin-top: 0.5rem;
                padding: 0.3rem 0.8rem;
                background: #007bff;
                color: white;
                border-radius: 4px;
                text-decoration: none;
                font-size: 0.9rem;
            }
            .no-orders {
                text-align: center;
                padding: 2rem;
                color: #666;
            }
              
        </style>
    </head>
    <body>
        <?php include('includes/header.php'); ?>
        
        <div class="orders-container">
            <h1>My Orders</h1>
            
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?= $order['order_id'] ?></h3>
                            <span class="status-badge <?= $order['status'] ?>">
                                <?= formatStatus($order['status']) ?>
                            </span>
                        </div>
                        <p><strong>Date:</strong> <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
                    
                        <a href="order_status.php?order_id=<?= $order['order_id'] ?>" class="view-btn">
                            View Details
                        </a>
                    </div>
                
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-orders">
                    <p>You haven't placed any orders yet.</p>
                    <a href="products.php" class="view-btn">Browse Products</a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php include('includes/footer.php'); ?>
    </body>
    </html>
    <?php
    exit;
}

$order_id = (int)$_GET['order_id'];

if ($order_id < 1) {
    header("Location: order_status.php");
    exit;
}

// Fetch order details
$stmt = $connection->prepare("
    SELECT o.*, d.userName, d.phoneNumber, d.yourImage 
    FROM orders o
    LEFT JOIN drivers d ON o.driver_id = d.driver_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $_SESSION['userId']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: order_status.php");
    exit;
}

// Fetch order items
$items_query = $connection->prepare("
    SELECT p.name, oi.quantity, oi.price 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$items_query->bind_param("i", $order_id);
$items_query->execute();
$order_items = $items_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate total
$order_total = 0;
foreach ($order_items as $item) {
    $order_total += $item['price'] * $item['quantity'];
}

// Fetch status updates
$updates = [];
$status_query = $connection->prepare("
    SELECT status, updated_at 
    FROM order_status_updates 
    WHERE order_id = ?
    ORDER BY updated_at DESC
");
$status_query->bind_param("i", $order_id);
$status_query->execute();
$updates = $status_query->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Order #<?= $order_id ?> Status</title>
    <link rel="stylesheet" href="assets/styles/style.css">
    <link rel="stylesheet" href="assets/styles/status.css">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo.png">
<style>
.refresh-container {
    margin: 15px 0;
    text-align: center;
    font-size: 14px;
}

.refresh-progress {
    width: 100%;
    height: 4px;
    background: #e0e0e0;
    border-radius: 2px;
    margin-bottom: 5px;
    overflow: hidden;
}

.refresh-progress-bar {
    height: 100%;
    width: 100%;
    background: #4CAF50;
    transition: width 1s linear;
}
</style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="status-container">
        <div class="refresh-container">
    <div class="refresh-progress">
        <div class="refresh-progress-bar" id="refresh-progress"></div>
    </div>
    <p>Auto-refreshing in <span id="refresh-countdown">30</span>s 
       (<a href="#" onclick="location.reload();return false;">refresh now</a>)</p>
    </div>
        <h1>Order #<?= $order_id ?></h1>
        
        <div class="status-badge <?= $order['status'] ?>">
            <?= formatStatus($order['status']) ?>
        </div>
        
        <div class="order-summary">
            <p><strong>Order Date:</strong> <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
            <p><strong>Total Amount:</strong> $<?= number_format($order_total, 2) ?></p>
        </div>
        
        <div class="timeline">
            <div class="step <?= in_array($order['status'], ['pending', 'assigned', 'on_transit', 'delivered']) ? 'active' : '' ?>">
                <div class="dot"></div>
                <div class="label">Order Placed</div>
                <div class="time"><?= date('M j, g:i A', strtotime($order['created_at'])) ?></div>
            </div>
            
            <div class="step <?= in_array($order['status'], ['assigned', 'on_transit', 'delivered']) ? 'active' : '' ?>">
                <div class="dot"></div>
                <div class="label">Driver Assigned</div>
                <?php if (in_array($order['status'], ['assigned', 'on_transit', 'delivered'])): ?>
                    <div class="time"><?= date('M j, g:i A', strtotime($order['assigned_at'])) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="step <?= in_array($order['status'], ['on_transit', 'delivered']) ? 'active' : '' ?>">
                <div class="dot"></div>
                <div class="label">On The Way</div>
                <?php if ($order['status'] === 'on_transit'): ?>
                    <div class="time"><?= date('M j, g:i A', strtotime($order['on_transit_at'] ?? $order['assigned_at'])) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="step <?= $order['status'] === 'delivered' ? 'active' : '' ?>">
                <div class="dot"></div>
                <div class="label">Delivered</div>
                <?php if ($order['status'] === 'delivered'): ?>
                    <div class="time"><?= date('M j, g:i A', strtotime($order['delivered_at'])) ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($order['userName']): ?>
        <div class="driver-card">
            <h3>Your Driver</h3>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <?php if ($order['yourImage']): ?>
                <img class="picture" src="profiles/<?= htmlspecialchars($order['yourImage']) ?>" alt="Driver">
                <?php endif; ?>
                <div>
                    <p><strong><?= htmlspecialchars($order['userName']) ?></strong></p>
                    <p><?= htmlspecialchars($order['phoneNumber']) ?></p>
                    <p><a href="tel:<?= htmlspecialchars($order['phoneNumber']) ?>" class="btn">Call Driver</a></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="order-items">
            <h3>Order Items</h3>
            <?php foreach ($order_items as $item): ?>
            <div class="order-item">
                <span><?= htmlspecialchars($item['name']) ?></span>  |
                <span><?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="update-history">
            <h3>Status Updates</h3>
            <?php foreach ($updates as $update): ?>
            <div class="update-item">
                <span><?= formatStatus($update['status']) ?></span>
                <span><?= date('M j, g:i A', strtotime($update['updated_at'])) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <a href="order_status.php" class="back-link">← Back to My Orders</a>
            
    </div>
    <script>
const refreshInterval = 30; // seconds
let timeLeft = refreshInterval;

function updateRefreshUI() {
    timeLeft--;
    document.getElementById('refresh-countdown').textContent = timeLeft;
    document.getElementById('refresh-progress').style.width = `${(timeLeft/refreshInterval)*100}%`;
    
    if (timeLeft <= 0) {
        window.location.reload();
    } else {
        setTimeout(updateRefreshUI, 1000);
    }
}

// Start the countdown
updateRefreshUI();
</script>
    <?php include('includes/footer.php'); ?>
    
</body>
</html>