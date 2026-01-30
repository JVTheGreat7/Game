<?php
/**
 * Admin - Get Product API
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('admin');

$product_id = sanitize($_GET['id'] ?? '', $conn);

if (empty($product_id)) {
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = $conn->query($query);
$product = fetchOne($result);

if ($product) {
    echo json_encode($product);
} else {
    echo json_encode(['error' => 'Product not found']);
}
?>
