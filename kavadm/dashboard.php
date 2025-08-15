<?php
require_once 'auth_check.php';
require_once '../includes/database.php';

// Fetch data
$stats = $connection->query("SELECT 
    SUM(status = 'pending') AS pending,
    SUM(status = 'assigned') AS assigned,
    SUM(status = 'delivered') AS delivered 
    FROM orders")->fetch_assoc();


$orders = $connection->query("
    SELECT 
        o.order_id,
        o.created_at,
        o.status,
        u.fullName AS customer_name,
        u.Address As customer_address,
        GROUP_CONCAT(
            CONCAT(p.name, ' (', oi.quantity, ' x $', oi.price, ')') 
            SEPARATOR '<br>'
        ) AS products,
        SUM(oi.quantity * oi.price) AS order_total
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC 
    LIMIT 8
");

$drivers = $connection->query("SELECT * FROM drivers WHERE is_available = 1");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kavirex Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles/auth.css">
     <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
     <style>.refresh-container {
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
}</style>
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="user-profile">
            <div class="user-avatar">A</div>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="brand">
                <div class="brand-logo">
                    <img src="../assets/images/logo.png" alt="">
                </div>
                <h3>Kavirex</h3>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Orders</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Drivers</span>
                </a>
                <a href="logs.php" class="nav-item">
                    <i class="fas fa-history"></i>
                    <span>Activity Log</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="greeting">
                <h1>Welcome back, Admin!</h1>
                <p>Here's what's happening with your system today</p>
            </div>

            <!-- diferent Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card pending">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?= $stats['pending'] ?></div>
                            <div class="stat-title">Pending Orders</div>
                        </div>
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card assigned">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?= $stats['assigned'] ?></div>
                            <div class="stat-title">Assigned Orders</div>
                        </div>
                        <div class="stat-icon assigned">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card delivered">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?= $stats['delivered'] ?></div>
                            <div class="stat-title">Delivered Orders</div>
                        </div>
                        <div class="stat-icon delivered">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="section-header">
                <h2 class="section-title">Recent Orders</h2>
                <a href="#" class="view-all">View All</a> <!--link to order.php-->
            </div>

            <!-- Desktop Table -->
<table class="orders-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Location</th>
            <th>Products</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $orders->data_seek(0); // Reset pointer
        while($order = $orders->fetch_assoc()): 
        ?>
        <tr>
            <td class="order-id">#<?= $order['order_id'] ?></td>
            <td><?= $order['customer_name'] ?></td>
             <td><?= $order['customer_address'] ?></td>
            <td><?= $order['products'] ?></td>
            <td>$<?= number_format($order['order_total'], 2) ?></td>
            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
            <td>
                <?php if ($order['status'] === 'pending'): ?>
                    <span class="status-badge status-pending">Pending</span>
                <?php elseif ($order['status'] === 'assigned'): ?>
                    <span class="status-badge status-assigned">Assigned</span>
             <?php elseif ($order['status'] === 'on_transit'): ?>
                    <span class="status-badge status-assigned">On Transit</span>
                <?php else: ?>
                    <span class="status-badge status-delivered">Delivered</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($order['status'] === 'pending'): ?>
                    <button class="assign-btn" onclick="assignDriver(<?= $order['order_id'] ?>)">
                        Assign Driver
                    </button>
                <?php else: ?>
                    <span style="color: #64748b;">Completed</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

            <!-- Mobile Cards -->
         
<div class="order-cards">
    <?php 
    $orders->data_seek(0); // Reset pointer
    while($order = $orders->fetch_assoc()): 
    ?>
    <div class="order-card">
        <div class="card-row">
            <span class="order-id">#<?= $order['order_id'] ?></span>
            <span class="status-badge <?= 'status-' . $order['status'] ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </div>
        <div class="card-row">
            <strong><?= $order['customer_name'] ?></strong>
        </div>
          <div class="card-row">
            <strong><?= $order['customer_address'] ?></strong>
        </div>
        <div class="card-row">
            <?= $order['products'] ?>
        </div>
        <div class="card-row">
            <strong>Total: $<?= number_format($order['order_total'], 2) ?></strong>
        </div>
        <div class="card-row">
            <span style="color: #64748b; font-size: 14px;">
                <?= date('M d, Y', strtotime($order['created_at'])) ?>
            </span>
        </div>
        <?php if ($order['status'] === 'pending'): ?>
            <button class="assign-btn" onclick="assignDriver(<?= $order['order_id'] ?>)">
                Assign Driver
            </button>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>

    <!-- Driver Assignment Modal -->
    <div class="modal-overlay" id="driverModal">
        <div class="modal-content">
            <h3 class="modal-title">Assign Driver</h3>
            <p>Select a driver for Order #<span id="modalOrderId"></span></p>
            
            <select class="modal-select" id="driverSelect">
                <option value="">Select Driver</option>
                <?php 
                $drivers->data_seek(0); // Reset pointer
                while($driver = $drivers->fetch_assoc()): 
                ?>
                    <option value="<?= $driver['driver_id'] ?>"><?= $driver['userName'] ?></option>
                <?php endwhile; ?>
            </select>
            
            <div class="modal-actions">
                <button class="modal-btn modal-cancel" onclick="closeModal()">Cancel</button>
                <button class="modal-btn modal-confirm" onclick="confirmAssignment()">Assign</button>
            </div>
        </div>
    </div>
       <div class="refresh-container">
    <div class="refresh-progress">
        <div class="refresh-progress-bar" id="refresh-progress"></div>
    </div>
    <p>Auto-refreshing in <span id="refresh-countdown">30</span>s 
       (<a href="#" onclick="location.reload();return false;">refresh now</a>)</p>
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
        // Toggle sidebar on mobile
           function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    // Close sidebar when clicking outside
     document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.querySelector('.menu-toggle');
    
    // Only run this if sidebar is currently open
    if (sidebar.classList.contains('active')) {
        // Check if click is outside sidebar AND not on menu toggle
        if (!sidebar.contains(event.target) && event.target !== menuToggle && !menuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});

        // Driver assignment modal
        let currentOrderId = 0;
        
        function assignDriver(orderId) {
            currentOrderId = orderId;
            document.getElementById('modalOrderId').textContent = orderId;
            document.getElementById('driverModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('driverModal').style.display = 'none';
        }
        
        function confirmAssignment() {
            const driverId = document.getElementById('driverSelect').value;
            if (!driverId) {
                alert('Please select a driver');
                return;
            }
            
            // Submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'assign_drivers.php';
            
            const orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'order_id';
            orderInput.value = currentOrderId;
            form.appendChild(orderInput);
            
            const driverInput = document.createElement('input');
            driverInput.type = 'hidden';
            driverInput.name = 'driver_id';
            driverInput.value = driverId;
            form.appendChild(driverInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>