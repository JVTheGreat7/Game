<?php
/**
 * Products Listing Page
 */
require_once '../config/db.php';
$page_title = 'Products';
include '../includes/header.php';

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_query);
$categories = fetchAll($categories_result);

// Get selected category
$category_id = $_GET['category'] ?? null;
$search = $_GET['search'] ?? '';

// Build products query
$query = "SELECT p.*, c.category_name, i.quantity, 
          (SELECT AVG(rating) FROM reviews WHERE product_id = p.product_id) as avg_rating,
          (SELECT COUNT(*) FROM reviews WHERE product_id = p.product_id) as review_count
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.category_id
          LEFT JOIN inventory i ON p.product_id = i.product_id
          WHERE 1=1";

if ($category_id) {
    $category_id = sanitize($category_id, $conn);
    $query .= " AND p.category_id = '$category_id'";
}

if ($search) {
    $search = sanitize($search, $conn);
    $query .= " AND (p.product_name LIKE '%$search%' OR p.brand LIKE '%$search%')";
}

$query .= " ORDER BY p.product_name ASC";
$products_result = $conn->query($query);
$products = fetchAll($products_result);
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
                
                <!-- Categories -->
                <h6 class="mb-3">Categories</h6>
                <div class="list-group">
                    <a href="/PC/pages/products.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                        All Products
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/PC/pages/products.php?category=<?php echo $cat['category_id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo $category_id == $cat['category_id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="col-lg-9">
        <h2 class="mb-4">
            <?php if ($category_id && $categories_result->num_rows > 0): ?>
                <?php 
                foreach ($categories as $cat) {
                    if ($cat['category_id'] == $category_id) {
                        echo htmlspecialchars($cat['category_name']);
                        break;
                    }
                }
                ?>
            <?php elseif ($search): ?>
                Search Results for "<?php echo htmlspecialchars($search); ?>"
            <?php else: ?>
                All Products
            <?php endif; ?>
        </h2>
        
        <?php if (count($products) > 0): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card product-card h-100">
                            <?php 
                            $image_query = "SELECT image_url FROM product_images WHERE product_id = {$product['product_id']} LIMIT 1";
                            $image_result = $conn->query($image_query);
                            $image = $image_result ? fetchOne($image_result) : null;
                            $image_url = $image ? $image['image_url'] : 'https://via.placeholder.com/300x300?text=No+Image';
                            ?>
                            <img src="<?php echo htmlspecialchars($image_url); ?>" class="product-image card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            
                            <div class="product-info">
                                <h6 class="product-brand"><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></h6>
                                <h5 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                
                                <?php if ($product['avg_rating']): ?>
                                    <div class="product-rating">
                                        ★ <?php echo number_format($product['avg_rating'], 1); ?> 
                                        (<?php echo $product['review_count']; ?> reviews)
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
        <?php else: ?>
            <div class="alert alert-info">
                <p>No products found. <?php if ($search): ?>Try a different search term.<?php endif; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
