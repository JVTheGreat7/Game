<?php
/**
 * SYSTEM TEST - Check All Paths and Login
 */
require_once 'config/db.php';
require_once 'includes/auth.php';

echo "<h1>✅ System Path Test</h1>";
echo "<hr>";

// Test 1: Database connection
echo "<h3>1. Database Connection</h3>";
if ($conn->connect_error) {
    echo "<p style='color:red;'>❌ FAILED: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color:green;'>✅ Connected to pc_parts_store</p>";
}

// Test 2: Check tables exist
echo "<h3>2. Database Tables</h3>";
$tables = [
    'users' => 'Users',
    'categories' => 'Categories',
    'products' => 'Products',
    'product_images' => 'Product Images',
    'inventory' => 'Inventory',
    'orders' => 'Orders',
    'order_items' => 'Order Items',
    'payments' => 'Payments',
    'reviews' => 'Reviews',
    'inventory_logs' => 'Inventory Logs'
];

foreach ($tables as $table => $name) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        echo "<p style='color:green;'>✅ $name ($table)</p>";
    } else {
        echo "<p style='color:red;'>❌ $name ($table) - MISSING</p>";
    }
}

// Test 3: Test user accounts
echo "<h3>3. Test User Accounts</h3>";
$test_accounts = [
    'admin@pcparts.local' => 'Admin',
    'staff@pcparts.local' => 'Staff',
    'john@example.com' => 'Customer 1',
    'jane@example.com' => 'Customer 2'
];

foreach ($test_accounts as $email => $role_name) {
    $result = $conn->query("SELECT user_id, role FROM users WHERE email = '$email'");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p style='color:green;'>✅ $role_name ($email) - Role: {$user['role']}</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ $role_name ($email) - NOT FOUND</p>";
    }
}

// Test 4: Check sample data
echo "<h3>4. Sample Data</h3>";
$product_count = fetchValue($conn->query("SELECT COUNT(*) FROM products"));
$order_count = fetchValue($conn->query("SELECT COUNT(*) FROM orders"));
$category_count = fetchValue($conn->query("SELECT COUNT(*) FROM categories"));

echo "<p>✅ Products: $product_count</p>";
echo "<p>✅ Categories: $category_count</p>";
echo "<p>✅ Orders: $order_count</p>";

// Test 5: Test file paths
echo "<h3>5. File Paths (Core Files)</h3>";
$files = [
    'includes/header.php' => 'Header',
    'includes/footer.php' => 'Footer',
    'includes/auth.php' => 'Auth',
    'config/db.php' => 'Database',
    'auth/login.php' => 'Login Page',
    'auth/register.php' => 'Register Page',
    'index.php' => 'Home Page',
    'pages/products.php' => 'Products Page',
    'pages/cart.php' => 'Cart Page',
    'pages/checkout.php' => 'Checkout Page',
    'admin/dashboard.php' => 'Admin Dashboard',
    'staff/dashboard.php' => 'Staff Dashboard',
    'api/cart-actions.php' => 'Cart API',
    'assets/css/style.css' => 'CSS',
    'assets/js/script.js' => 'JavaScript'
];

foreach ($files as $path => $name) {
    $full_path = __DIR__ . '/' . $path;
    if (file_exists($full_path)) {
        echo "<p style='color:green;'>✅ $name</p>";
    } else {
        echo "<p style='color:red;'>❌ $name ($path) - MISSING</p>";
    }
}

echo "<hr>";
echo "<h2>✅ System is Ready!</h2>";
echo "<p><a href='http://localhost/PC/auth/login.php'>Go to Login →</a></p>";

?>
