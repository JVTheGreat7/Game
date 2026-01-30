<?php
/**
 * Shopping Cart Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';



// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$page_title = 'Shopping Cart';
include '../includes/header.php';

// Fetch cart items from session and database
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product_id = sanitize($product_id, $conn);
        $query = "SELECT p.product_id, p.product_name, p.price, p.brand, i.quantity as stock,
                  (SELECT image_url FROM product_images WHERE product_id = p.product_id LIMIT 1) as image_url
                  FROM products p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.product_id = '$product_id'";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $product = fetchOne($result);
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            
            $cart_items[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'brand' => $product['brand'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'stock' => $product['stock'],
                'image_url' => $product['image_url'] ?? 'https://via.placeholder.com/100x100?text=No+Image',
                'subtotal' => $subtotal
            ];
        }
    }
}
?>

<div class="row">
    <div class="col-lg-8">
        <h2 class="mb-4">Shopping Cart</h2>
        
        <?php if (count($cart_items) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="" class="cart-item-image">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['brand']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" class="form-control" style="width: 80px;" 
                                           value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>"
                                           onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                </td>
                                <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mb-3">
                <button class="btn btn-outline-danger" onclick="clearCart()">Clear Cart</button>
                <a href="/PC/pages/products.php" class="btn btn-outline-secondary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>Your cart is empty. <a href="/PC/pages/products.php">Start shopping</a></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Cart Summary -->
    <?php if (count($cart_items) > 0): ?>
        <div class="col-lg-4">
            <div class="cart-summary">
                <h5 class="mb-4">Order Summary</h5>
                
                <div class="summary-item">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                
                <div class="summary-item">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                
                <div class="summary-item">
                    <span>Tax (estimated):</span>
                    <span>$<?php echo number_format($total * 0.08, 2); ?></span>
                </div>
                
                <div class="summary-total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total * 1.08, 2); ?></span>
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <a href="/PC/pages/checkout.php" class="btn btn-primary w-100 mt-3">
                        Proceed to Checkout
                    </a>
                <?php else: ?>
                    <a href="/PC/auth/login.php" class="btn btn-primary w-100 mt-3">
                        Login to Checkout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
