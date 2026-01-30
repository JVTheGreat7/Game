<?php
/**
 * Shopping Cart API
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

session_start();

// Initialize cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Add to cart
if ($action === 'add') {
    $product_id = sanitize($_POST['product_id'] ?? '', $conn);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if (empty($product_id) || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        exit;
    }
    
    // Check if product exists and has stock
    $check_query = "SELECT p.product_id, i.quantity FROM products p 
                    LEFT JOIN inventory i ON p.product_id = i.product_id 
                    WHERE p.product_id = '$product_id'";
    $check_result = $conn->query($check_query);
    $product = fetchOne($check_result);
    
    if (!$product || $product['quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Product not available or out of stock']);
        exit;
    }
    
    // Add or update cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
}

// Update quantity
elseif ($action === 'update') {
    $product_id = sanitize($_POST['product_id'] ?? '', $conn);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    if (empty($product_id) || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
    
    $_SESSION['cart'][$product_id] = $quantity;
    echo json_encode(['success' => true, 'message' => 'Cart updated']);
}

// Remove from cart
elseif ($action === 'remove') {
    $product_id = sanitize($_POST['product_id'] ?? '', $conn);
    
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
}

// Clear cart
elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'message' => 'Cart cleared']);
}

// Get cart count
elseif ($action === 'get_count') {
    $count = count($_SESSION['cart']);
    echo json_encode(['count' => $count]);
}

// Get cart details
elseif ($action === 'get_cart') {
    $cart_items = [];
    $total = 0;
    
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product_id = sanitize($product_id, $conn);
            $query = "SELECT p.product_id, p.product_name, p.price, i.quantity as stock 
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
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'stock' => $product['stock'],
                    'subtotal' => $subtotal
                ];
            }
        }
    }
    
    echo json_encode([
        'items' => $cart_items,
        'total' => $total,
        'count' => count($cart_items)
    ]);
}

else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
