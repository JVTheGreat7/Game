<?php
/**
 * Customer Dashboard - Orders and Profile
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$user = getCurrentUser();
if ($user['role'] !== 'customer') {
    die('Access denied');
}

$page_title = 'My Dashboard';
include '../includes/header.php';

// Fetch user's orders
$orders_query = "SELECT o.order_id, o.order_date, o.total_amount, o.status, 
                 COUNT(oi.order_item_id) as item_count
                 FROM orders o
                 LEFT JOIN order_items oi ON o.order_id = oi.order_id
                 WHERE o.user_id = {$user['user_id']}
                 GROUP BY o.order_id
                 ORDER BY o.order_date DESC";
$orders_result = $conn->query($orders_query);
$orders = fetchAll($orders_result);
?>

<h2 class="mb-4">My Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="dashboard-card">
            <h5>Profile Information</h5>
            <div class="mb-3">
                <label class="form-label text-muted">Full Name</label>
                <p><?php echo htmlspecialchars($user['full_name']); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Email Address</label>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Member Since</label>
                <p><?php echo date('F d, Y'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="dashboard-card">
            <h5>Quick Stats</h5>
            <div class="row">
                <div class="col-6 text-center mb-3">
                    <div class="h3 text-primary"><?php echo count($orders); ?></div>
                    <p class="text-muted">Total Orders</p>
                </div>
                <div class="col-6 text-center mb-3">
                    <div class="h3 text-success">
                        $<?php 
                        $total_spent = 0;
                        foreach ($orders as $order) {
                            $total_spent += $order['total_amount'];
                        }
                        echo number_format($total_spent, 2);
                        ?>
                    </div>
                    <p class="text-muted">Total Spent</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <h5>Recent Orders</h5>
    
    <?php if (count($orders) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                            <td><?php echo $order['item_count']; ?> item(s)</td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="order-status status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/PC/pages/order-detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">You haven't placed any orders yet. <a href="/PC/pages/products.php">Start shopping</a></p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
