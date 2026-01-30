<?php
/**
 * Checkout Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();
session_start();

if (empty($_SESSION['cart'])) {
    header('Location: /PC/pages/cart.php');
    exit();
}

$user = getCurrentUser();
$page_title = 'Checkout';
include '../includes/header.php';

// Calculate order total
$cart_items = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product_id = sanitize($product_id, $conn);
    $query = "SELECT p.product_id, p.product_name, p.price FROM products p WHERE p.product_id = '$product_id'";
    $result = $conn->query($query);
    $product = fetchOne($result);
    
    if ($product) {
        $cart_items[] = [
            'product_id' => $product['product_id'],
            'product_name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $product['price'] * $quantity
        ];
        $subtotal += $product['price'] * $quantity;
    }
}

$tax = $subtotal * 0.08;
$total = $subtotal + $tax;

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? 'cash';
    
    // Create order
    $order_date = date('Y-m-d H:i:s');
    $order_insert = "INSERT INTO orders (user_id, order_date, total_amount, status) 
                     VALUES ({$user['user_id']}, '$order_date', '$total', 'pending')";
    
    if ($conn->query($order_insert)) {
        $order_id = $conn->insert_id;
        
        // Add order items and update inventory
        $inventory_ok = true;
        foreach ($cart_items as $item) {
            // Insert order item
            $product_id = sanitize($item['product_id'], $conn);
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];
            
            $item_insert = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES ('$order_id', '$product_id', '$quantity', '$price')";
            $conn->query($item_insert);
            
            // Update inventory
            $update_inventory = "UPDATE inventory SET quantity = quantity - $quantity WHERE product_id = '$product_id'";
            if (!$conn->query($update_inventory)) {
                $inventory_ok = false;
            }
            
            // Log inventory change
            $log_insert = "INSERT INTO inventory_logs (product_id, change_quantity, reason) 
                          VALUES ('$product_id', -$quantity, 'Order #$order_id')";
            $conn->query($log_insert);
        }
        
        if ($inventory_ok) {
            // Create payment record
            $payment_insert = "INSERT INTO payments (order_id, payment_method, payment_status) 
                              VALUES ('$order_id', '$payment_method', 'pending')";
            $conn->query($payment_insert);
            
            // Clear cart and redirect to payment
            $_SESSION['cart'] = [];
            header("Location: /PC/pages/payment.php?order_id=$order_id");
            exit();
        } else {
            $error_message = 'Error updating inventory. Please contact support.';
        }
    } else {
        $error_message = 'Error creating order: ' . $conn->error;
    }
}
?>

<div class="row">
    <div class="col-lg-8">
        <h2 class="mb-4">Checkout</h2>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-end">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Delivery Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                </div>
                <p class="text-muted small">Contact information is from your account. To change it, please update your profile.</p>
            </div>
        </div>
    </div>
    
    <!-- Order Total -->
    <div class="col-lg-4">
        <div class="cart-summary sticky-top" style="top: 100px;">
            <h5 class="mb-4">Order Total</h5>
            
            <div class="summary-item">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($subtotal, 2); ?></span>
            </div>
            
            <div class="summary-item">
                <span>Tax (8%):</span>
                <span>$<?php echo number_format($tax, 2); ?></span>
            </div>
            
            <div class="summary-item">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            
            <div class="summary-total">
                <span>Total:</span>
                <span>$<?php echo number_format($total, 2); ?></span>
            </div>
            
            <form method="POST" action="" class="mt-4">
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" name="payment_method" id="payment_method" required>
                        <option value="cash">Cash on Delivery</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="gcash">GCash</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    Complete Order
                </button>
            </form>
            
            <a href="/PC/pages/cart.php" class="btn btn-outline-secondary w-100 mt-2">
                Back to Cart
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
