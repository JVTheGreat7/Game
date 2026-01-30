<?php
/**
 * Home Page
 */
require_once 'config/db.php';
$page_title = 'Home';
include 'includes/header.php';

// Get featured products (highest rated)
$featured_query = "SELECT p.*, c.category_name, i.quantity,
                   (SELECT AVG(rating) FROM reviews WHERE product_id = p.product_id) as avg_rating,
                   (SELECT COUNT(*) FROM reviews WHERE product_id = p.product_id) as review_count,
                   (SELECT image_url FROM product_images WHERE product_id = p.product_id LIMIT 1) as image_url
                   FROM products p
                   LEFT JOIN categories c ON p.category_id = c.category_id
                   LEFT JOIN inventory i ON p.product_id = i.product_id
                   ORDER BY avg_rating DESC, p.product_id DESC
                   LIMIT 8";
$featured_result = $conn->query($featured_query);
$featured_products = fetchAll($featured_result);

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY category_name LIMIT 6";
$categories_result = $conn->query($categories_query);
$categories = fetchAll($categories_result);
?>

<!-- Hero Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="bg-primary text-white p-5 rounded" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);">
            <h1 class="display-4 fw-bold mb-3">PC Parts Store</h1>
            <p class="lead mb-4">Your one-stop shop for high-quality PC components and parts</p>
            <div class="d-grid gap-2 d-sm-flex">
                <a href="/PC/pages/products.php" class="btn btn-light btn-lg px-4 gap-3">Shop Now</a>
                <a href="#categories" class="btn btn-outline-light btn-lg px-4">Browse Categories</a>
            </div>
        </div>
    </div>
</div>

<!-- Categories -->
<div class="row mb-5" id="categories">
    <div class="col-12">
        <h2 class="mb-4">Shop by Category</h2>
    </div>
    
    <?php foreach ($categories as $category): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <a href="/PC/pages/products.php?category=<?php echo $category['category_id']; ?>" class="card text-decoration-none text-dark h-100" style="transition: transform 0.3s;">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #f0f5ff 0%, #e7f0ff 100%);">
                    <h5 class="card-title"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                    <p class="text-muted">Browse Products →</p>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Featured Products -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="mb-4">Featured Products</h2>
    </div>
    
    <?php foreach ($featured_products as $product): ?>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card product-card h-100">
                <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300x300?text=No+Image'); ?>" class="product-image card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                
                <div class="product-info">
                    <h6 class="product-brand"><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></h6>
                    <h5 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                    
                    <?php if ($product['avg_rating']): ?>
                        <div class="product-rating">
                            ★ <?php echo number_format($product['avg_rating'], 1); ?> (<?php echo $product['review_count']; ?>)
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    
                    <?php if ($product['quantity'] > 0): ?>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                Add to Cart
                            </button>
                            <a href="/PC/pages/product-detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-sm w-100" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Info Section -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div style="font-size: 2rem; color: #0d6efd; margin-bottom: 1rem;">📦</div>
                <h5 class="card-title">Fast Shipping</h5>
                <p class="card-text">Quick and reliable delivery to your doorstep</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div style="font-size: 2rem; color: #0d6efd; margin-bottom: 1rem;">💳</div>
                <h5 class="card-title">Secure Payment</h5>
                <p class="card-text">Multiple payment options for your convenience</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div style="font-size: 2rem; color: #0d6efd; margin-bottom: 1rem;">🛡️</div>
                <h5 class="card-title">Quality Guaranteed</h5>
                <p class="card-text">All products are authentic and come with warranty</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
