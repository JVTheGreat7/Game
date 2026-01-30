<?php
/**
 * Orders List Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$user = getCurrentUser();
$page_title = 'My Orders';
include '../includes/header.php';

// Fetch user's orders
$orders_query = "SELECT o.order_id, o.order_date, o.total_amount, o.status 
                 FROM orders o
                 WHERE o.user_id = {$user['user_id']}
                 ORDER BY o.order_date DESC";
$orders_result = $conn->query($orders_query);
$orders = fetchAll($orders_result);
?>

<h2 class="mb-4">My Orders</h2>

<?php if (count($orders) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                        <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="/PC/pages/order-detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <p>You haven't placed any orders yet.</p>
        <a href="/PC/pages/products.php" class="btn btn-primary">Start Shopping</a>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
