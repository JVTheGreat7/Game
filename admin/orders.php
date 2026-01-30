<?php
/**
 * Admin - View Orders
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('admin');

$page_title = 'View Orders';
include '../../includes/header.php';

// Get all orders
$orders = fetchAll($conn->query("
    SELECT o.*, u.full_name, u.email, COUNT(oi.order_item_id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
"));
?>

<h2 class="mb-4">All Orders</h2>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                    <td>
                        <div><?php echo htmlspecialchars($order['full_name']); ?></div>
                        <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                    <td><?php echo $order['item_count']; ?></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <span class="order-status status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
