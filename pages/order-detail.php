<?php
/**
 * Order Detail Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$user = getCurrentUser();
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header('Location: /PC/pages/orders.php');
    exit();
}

$order_id = sanitize($order_id, $conn);

// Fetch order
$query = "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = {$user['user_id']}";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    die('Order not found');
}

$order = fetchOne($result);

// Fetch order items
$items_query = "SELECT oi.*, p.product_name, p.brand, 
                (SELECT image_url FROM product_images WHERE product_id = p.product_id LIMIT 1) as image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = '$order_id'";
$items_result = $conn->query($items_query);
$items = fetchAll($items_result);

// Fetch payment info
$payment_query = "SELECT * FROM payments WHERE order_id = '$order_id'";
$payment_result = $conn->query($payment_query);
$payment = fetchOne($payment_result);

$page_title = 'Order #' . $order_id;
include '../includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/PC/pages/dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/PC/pages/orders.php">Orders</a></li>
        <li class="breadcrumb-item active">Order #<?php echo $order_id; ?></li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Order #<?php echo $order_id; ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i A', strtotime($order['order_date'])); ?></p>
                        <p><strong>Order Status:</strong> <span class="order-status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Payment Method:</strong> <?php echo ucfirst($payment['payment_method']); ?></p>
                        <p><strong>Payment Status:</strong> <span class="order-status status-<?php echo $payment['payment_status']; ?>"><?php echo ucfirst($payment['payment_status']); ?></span></p>
                    </div>
                </div>
                
                <h6 class="mb-3">Items Ordered</h6>
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/50x50'); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['brand']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-end">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-end"><strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="summary-item">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($order['total_amount'] / 1.08, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (8%):</span>
                    <span>$<?php echo number_format($order['total_amount'] - ($order['total_amount'] / 1.08), 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <a href="/PC/pages/orders.php" class="btn btn-outline-secondary w-100 mt-3">Back to Orders</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
