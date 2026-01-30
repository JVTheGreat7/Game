<?php
/**
 * Product Detail Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: /PC/pages/products.php');
    exit();
}

$product_id = sanitize($product_id, $conn);

// Fetch product
$query = "SELECT p.*, c.category_name, i.quantity 
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.category_id
          LEFT JOIN inventory i ON p.product_id = i.product_id
          WHERE p.product_id = '$product_id'";
$result = $conn->query($query);
$product = fetchOne($result);

if (!$product) {
    header('Location: /PC/pages/products.php');
    exit();
}

$page_title = $product['product_name'];
include '../includes/header.php';

// Fetch images
$images_query = "SELECT image_url FROM product_images WHERE product_id = '$product_id'";
$images_result = $conn->query($images_query);
$images = fetchAll($images_result);

// Fetch reviews
$reviews_query = "SELECT r.*, u.full_name FROM reviews r 
                  JOIN users u ON r.user_id = u.user_id
                  WHERE r.product_id = '$product_id'
                  ORDER BY r.review_date DESC";
$reviews_result = $conn->query($reviews_query);
$reviews = fetchAll($reviews_result);

// Calculate average rating
$avg_rating_query = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = '$product_id'";
$avg_result = $conn->query($avg_rating_query);
$avg_data = fetchOne($avg_result);
$avg_rating = $avg_data['avg_rating'] ? round($avg_data['avg_rating'], 1) : 0;
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/PC/index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="/PC/pages/products.php">Products</a></li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_name']); ?></li>
    </ol>
</nav>

<div class="row">
    <!-- Images -->
    <div class="col-lg-5">
        <?php 
        $main_image = $images ? $images[0]['image_url'] : 'https://via.placeholder.com/400x400?text=No+Image';
        ?>
        <div class="mb-3">
            <img src="<?php echo htmlspecialchars($main_image); ?>" class="img-fluid rounded" id="mainImage" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        </div>
        
        <?php if (count($images) > 1): ?>
            <div class="row g-2">
                <?php foreach ($images as $img): ?>
                    <div class="col-3">
                        <img src="<?php echo htmlspecialchars($img['image_url']); ?>" class="img-fluid rounded cursor-pointer" 
                             onclick="document.getElementById('mainImage').src = this.src" alt="Product image">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Product Details -->
    <div class="col-lg-7">
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        
        <div class="mb-3">
            <span class="badge bg-info"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <span class="badge bg-secondary"><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></span>
        </div>
        
        <div class="mb-3">
            <?php if ($avg_rating > 0): ?>
                <div class="product-rating">
                    ★ <?php echo $avg_rating; ?> / 5.0 (<?php echo count($reviews); ?> reviews)
                </div>
            <?php else: ?>
                <p class="text-muted">No reviews yet</p>
            <?php endif; ?>
        </div>
        
        <div class="mb-4">
            <h2 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h2>
            
            <?php if ($product['quantity'] > 0): ?>
                <p class="text-success"><strong><?php echo $product['quantity']; ?> in stock</strong></p>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </div>
            <?php else: ?>
                <p class="text-danger"><strong>Out of Stock</strong></p>
                <button class="btn btn-secondary btn-lg" disabled>Currently Unavailable</button>
            <?php endif; ?>
        </div>
        
        <hr>
        
        <!-- Specifications -->
        <h5>Specifications</h5>
        <div class="card mb-4">
            <div class="card-body">
                <pre><?php echo htmlspecialchars($product['specifications'] ?? 'No specifications available'); ?></pre>
            </div>
        </div>
    </div>
</div>

<hr>

<!-- Reviews Section -->
<div class="row mt-5">
    <div class="col-lg-8">
        <h3>Customer Reviews (<?php echo count($reviews); ?>)</h3>
        
        <?php if (isLoggedIn() && getCurrentUser()['role'] === 'customer'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Write a Review</h5>
                </div>
                <div class="card-body">
                    <form onsubmit="return submitReview(<?php echo $product_id; ?>, this)">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" name="rating" id="rating" required>
                                <option value="">Select rating...</option>
                                <option value="5">★★★★★ Excellent</option>
                                <option value="4">★★★★☆ Very Good</option>
                                <option value="3">★★★☆☆ Good</option>
                                <option value="2">★★☆☆☆ Fair</option>
                                <option value="1">★☆☆☆☆ Poor</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea class="form-control" name="comment" id="comment" rows="4" placeholder="Share your experience with this product..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        <?php elseif (!isLoggedIn()): ?>
            <div class="alert alert-info">
                <a href="/PC/auth/login.php">Login</a> to write a review.
            </div>
        <?php endif; ?>
        
        <!-- Display Reviews -->
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-user"><?php echo htmlspecialchars($review['full_name']); ?></div>
                    <div class="review-rating">
                        <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                    </div>
                    <div class="review-date"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></div>
                    <p class="mt-2"><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No reviews yet. Be the first to review!</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
