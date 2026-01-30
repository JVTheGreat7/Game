<?php
/**
 * Admin Dashboard - Main
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('admin');

$page_title = 'Admin Dashboard';
include '../../includes/header.php';

// Get statistics
$total_users = fetchValue($conn->query("SELECT COUNT(*) FROM users"));
$total_products = fetchValue($conn->query("SELECT COUNT(*) FROM products"));
$total_orders = fetchValue($conn->query("SELECT COUNT(*) FROM orders"));
$total_revenue = fetchValue($conn->query("SELECT SUM(total_amount) FROM orders WHERE status IN ('paid', 'shipped', 'delivered')"));
?>

<h2 class="mb-4">Admin Dashboard</h2>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Users</h6>
                <div class="h3 text-primary"><?php echo $total_users; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Products</h6>
                <div class="h3 text-success"><?php echo $total_products; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Orders</h6>
                <div class="h3 text-warning"><?php echo $total_orders; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Revenue</h6>
                <div class="h3 text-info">$<?php echo number_format($total_revenue ?? 0, 2); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Menu -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Management</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/PC/admin/products.php" class="btn btn-outline-primary">
                        <i class="bi bi-box"></i> Manage Products
                    </a>
                    <a href="/PC/admin/categories.php" class="btn btn-outline-primary">
                        <i class="bi bi-tags"></i> Manage Categories
                    </a>
                    <a href="/PC/admin/inventory.php" class="btn btn-outline-primary">
                        <i class="bi bi-collection"></i> Manage Inventory
                    </a>
                    <a href="/PC/admin/users.php" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Reports</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/PC/admin/orders.php" class="btn btn-outline-secondary">
                        <i class="bi bi-graph-up"></i> View All Orders
                    </a>
                    <a href="/PC/admin/sales.php" class="btn btn-outline-secondary">
                        <i class="bi bi-bar-chart"></i> Sales Report
                    </a>
                    <a href="/PC/admin/reviews.php" class="btn btn-outline-secondary">
                        <i class="bi bi-star"></i> Product Reviews
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
