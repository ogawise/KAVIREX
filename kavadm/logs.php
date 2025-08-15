<?php
require_once 'auth_check.php';
require_once '../includes/database.php';

$logs = $connection->query("
    SELECT l.*, a.username 
    FROM admin_logs l
    JOIN admins a ON l.admin_id = a.admin_id
    ORDER BY l.created_at DESC
    LIMIT 50
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Log</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Activity Log</h1>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    
    <table>
        <tr>
            <th>Date</th>
            <th>Admin</th>
            <th>Action</th>
            <th>Target ID</th>
            <th>IP Address</th>
        </tr>
        <?php while($log = $logs->fetch_assoc()): ?>
        <tr>
            <td><?= $log['created_at'] ?></td>
            <td><?= $log['username'] ?></td>
            <td><?= $log['action_type'] ?></td>
            <td><?= $log['target_id'] ?? 'N/A' ?></td>
            <td><?= $log['ip_address'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>