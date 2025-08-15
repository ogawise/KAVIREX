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

// Get current active order\
$activeOrderQuery = "SELECT order_id, status, created_at, customer_address FROM orders WHERE driver_id = ? AND status IN ('assigned', 'on_transit') LIMIT 1";
$stmt = $connection->prepare($activeOrderQuery);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$activeOrder = $stmt->get_result()->fetch_assoc();

// Get all orders for the driver
$allOrdersQuery = "SELECT order_id, status, created_at, customer_address FROM orders WHERE driver_id = ? ORDER BY order_id DESC";
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
    <style>
 .profiles {
        margin-top: 5px;
        padding: 8px 12px;
        width: 90%;
        max-width: 400px;
        margin: auto;
        box-shadow: 0 8px 24px hsla(215, 49%, 25%, 0.15);
        text-align: center;
    }
    .picture {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }
    h2 {
        margin-bottom: 2px;
    }
    .email_info {
        margin-bottom: 8px;
    }
    .status {
        font-weight: bold;
        color: hsl(18, 100%, 60%);
    }
    #orders-section ul {
        list-style: none;
        padding: 0;
    }
    #orders-section li {
        background: white;
        padding: 8px;
        margin-bottom: 5px;
        border-radius: 5px;
    }
    .timeline {
        margin: 20px 0;
        position: relative;
    }
    .timeline-step {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .timeline-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ccc;
        margin-right: 10px;
    }
    .timeline-dot.active {
        background: hsl(18, 100%, 60%);
    }
    .status-btn {
        background: hsl(18, 100%, 60%);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        margin: 5px;
        cursor: pointer;
    }
    .status-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    } 
    </style>
</head>
<body>
    <?php include('../includes/d_header.php') ?>

    <main style="min-height: 70vh; margin-top: 10%;"> 
        <div class="profiles">
            <img class="picture" src="../profiles/<?= htmlspecialchars($driver['yourImage'] ?? 'default.jpg') ?>" alt="profile picture">
            <h2><?= htmlspecialchars($driver['userName']) ?></h2>
            <p class="email_info">Email: <?= htmlspecialchars($driver['email']) ?></p>
            <p class="email_info">Phone: <?= htmlspecialchars($driver['phone'] ?? 'Not provided') ?></p>

            <div class="profile-extra">
                <p>Status: <span class="status"><?= $status ?></span></p>
                
                <?php if ($activeOrder): ?>
                    <div class="current-order">
                        <h3>Current Order #<?= $activeOrder['order_id'] ?></h3>
                        <p>Delivery Address: <?= htmlspecialchars($activeOrder['customer_address']) ?></p>
                        
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
                                Start Delivery
                            </button>
                        <?php elseif ($activeOrder['status'] === 'on_transit'): ?>
                            <button onclick="updateOrderStatus(<?= $activeOrder['order_id'] ?>, 'delivered')" 
                                    class="status-btn">
                                Mark as Delivered
                            </button>
                        <?php endif; ?>
                    </div>
                    </div>
                <?php else: ?>
                    <p>No active orders assigned</p>
                <?php endif; ?>
                
                <!-- Order History -->
                <div id="orders-section">
                    <h3>Order History</h3>
                    <?php if (count($allOrders) > 0): ?>
                        <ul>
                            <?php foreach ($allOrders as $order): ?>
                                <li>
                                    Order #<?= $order['order_id'] ?> - 
                                    Status: <span class="status"><?= ucfirst($order['status']) ?></span><br>
                                    Date: <?= date('M j, Y', strtotime($order['created_at'])) ?><br>
                                   
                                    Address: <?= htmlspecialchars($order['customer_address']) ?>
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
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
    });
}
</script>
</body>
</html>