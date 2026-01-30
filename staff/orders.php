<?php
/**
 * Staff - Process Orders
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('staff');

$page_title = 'Process Orders';
include '../../includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $order_id = sanitize($_POST['order_id'] ?? '', $conn);
        $status = sanitize($_POST['status'] ?? '', $conn);
        
        $allowed_statuses = ['paid', 'shipped', 'delivered', 'cancelled'];
        if (in_array($status, $allowed_statuses)) {
            $update = "UPDATE orders SET status = '$status' WHERE order_id = '$order_id'";
            if ($conn->query($update)) {
                $message = 'Order status updated successfully!';
            } else {
                $message = 'Error updating order';
            }
        }
    }
}

// Get all orders
$orders = fetchAll($conn->query("
    SELECT o.*, u.full_name, u.email, COUNT(oi.order_item_id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY 
        CASE 
            WHEN o.status = 'pending' THEN 1
            WHEN o.status = 'paid' THEN 2
            WHEN o.status = 'shipped' THEN 3
            WHEN o.status = 'delivered' THEN 4
            ELSE 5
        END,
        o.order_date DESC
"));
?>

<h2 class="mb-4">Process Orders</h2>

<?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Current Status</th>
                <th>Action</th>
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
                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="status" class="form-select form-select-sm">
                                <option value="<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></option>
                                <option value="paid">Paid</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
