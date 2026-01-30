<?php
/**
 * Add Review API
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = sanitize($_POST['product_id'] ?? '', $conn);
$rating = (int)($_POST['rating'] ?? 0);
$comment = sanitize($_POST['comment'] ?? '', $conn);

// Validate inputs
if (empty($product_id) || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating']);
    exit;
}

// Check if product exists
$product_check = "SELECT product_id FROM products WHERE product_id = '$product_id'";
$result = $conn->query($product_check);
if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Check if user has purchased this product
$purchase_check = "SELECT COUNT(*) as count FROM order_items oi
                   JOIN orders o ON oi.order_id = o.order_id
                   WHERE o.user_id = {$user['user_id']} AND oi.product_id = '$product_id'";
$purchase_result = $conn->query($purchase_check);
$purchase_data = fetchOne($purchase_result);

if ($purchase_data['count'] === 0) {
    echo json_encode(['success' => false, 'message' => 'You can only review products you have purchased']);
    exit;
}

// Check if user already reviewed this product
$existing_review = "SELECT review_id FROM reviews WHERE product_id = '$product_id' AND user_id = {$user['user_id']}";
$existing_result = $conn->query($existing_review);
if ($existing_result && $existing_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this product']);
    exit;
}

// Insert review
$insert_query = "INSERT INTO reviews (product_id, user_id, rating, comment, review_date) 
                VALUES ('$product_id', {$user['user_id']}, '$rating', '$comment', NOW())";

if ($conn->query($insert_query)) {
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error submitting review: ' . $conn->error]);
}
?>
