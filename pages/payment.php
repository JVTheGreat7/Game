<?php
/**
 * Payment Simulation Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('Location: /PC/index.php');
    exit();
}

$order_id = sanitize($order_id, $conn);
$user = getCurrentUser();

// Fetch order
$query = "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = {$user['user_id']}";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    die('Order not found');
}

$order = fetchOne($result);

$page_title = 'Payment - Order #' . $order_id;
include '../includes/header.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'pay') {
        // Simulate payment processing
        // In real system, this would integrate with payment gateway
        
        // Update payment status
        $update_payment = "UPDATE payments SET payment_status = 'completed', paid_at = NOW() 
                          WHERE order_id = '$order_id'";
        $conn->query($update_payment);
        
        // Update order status
        $update_order = "UPDATE orders SET status = 'paid' WHERE order_id = '$order_id'";
        $conn->query($update_order);
        
        $success = true;
    }
}
?>

<?php if ($success): ?>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-success">
                <div class="card-body text-center py-5">
                    <div class="mb-3" style="font-size: 3rem; color: #28a745;">
                        ✓
                    </div>
                    <h2 class="card-title text-success">Payment Successful!</h2>
                    <p class="card-text">Thank you for your order.</p>
                    
                    <div class="alert alert-info mt-4">
                        <p class="mb-1"><strong>Order ID:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
                        <p class="mb-1"><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                    </div>
                    
                    <p class="text-muted mt-4">We will process your order and send you a confirmation email shortly.</p>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="/PC/pages/orders.php" class="btn btn-primary">View My Orders</a>
                        <a href="/PC/pages/products.php" class="btn btn-outline-secondary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="mb-0">Order Payment</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p class="mb-1"><strong>Order ID:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
                        <p class="mb-1"><strong>Total Amount:</strong> <span class="h5">$<?php echo number_format($order['total_amount'], 2); ?></span></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">Pending Payment</span></p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <!-- Fetch payment method -->
                    <?php 
                    $payment_query = "SELECT * FROM payments WHERE order_id = '$order_id'";
                    $payment_result = $conn->query($payment_query);
                    $payment = fetchOne($payment_result);
                    ?>
                    
                    <div class="mb-4">
                        <h5>Payment Method: <strong><?php echo ucfirst($payment['payment_method']); ?></strong></h5>
                    </div>
                    
                    <!-- Payment Simulation -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <?php if ($payment['payment_method'] === 'card'): ?>
                                <p class="mb-3"><strong>Credit/Debit Card</strong></p>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <input type="text" class="form-control" placeholder="Card Number" value="4111 1111 1111 1111" readonly>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <input type="text" class="form-control" placeholder="MM/YY" value="12/28" readonly>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <input type="text" class="form-control" placeholder="CVV" value="123" readonly>
                                    </div>
                                </div>
                            <?php elseif ($payment['payment_method'] === 'gcash'): ?>
                                <p class="mb-3"><strong>GCash</strong></p>
                                <p class="text-muted">Phone: 09123456789</p>
                            <?php elseif ($payment['payment_method'] === 'paypal'): ?>
                                <p class="mb-3"><strong>PayPal</strong></p>
                                <p class="text-muted">Email: customer@example.com</p>
                            <?php else: ?>
                                <p class="mb-3"><strong>Cash on Delivery</strong></p>
                                <p class="text-muted">Please pay the amount when the items are delivered.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment['payment_method']); ?>">
                        <input type="hidden" name="action" value="pay">
                        
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            Confirm Payment - $<?php echo number_format($order['total_amount'], 2); ?>
                        </button>
                    </form>
                    
                    <hr>
                    
                    <p class="text-center text-muted small">
                        This is a demo/simulation payment. In production, this would connect to a real payment gateway.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
