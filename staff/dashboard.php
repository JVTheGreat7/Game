<?php
/**
 * Staff Dashboard - Main
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('staff');

$page_title = 'Staff Dashboard';
include '../../includes/header.php';

// Get pending orders
$pending_orders = fetchAll($conn->query("
    SELECT COUNT(*) as count FROM orders WHERE status = 'pending'
"));

// Get total paid orders
$paid_orders = fetchAll($conn->query("
    SELECT COUNT(*) as count FROM orders WHERE status = 'paid'
"));

$pending_count = $pending_orders[0]['count'] ?? 0;
$paid_count = $paid_orders[0]['count'] ?? 0;
?>

<h2 class="mb-4">Staff Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Pending Orders</h6>
                <div class="h3 text-warning"><?php echo $pending_count; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Orders Ready to Ship</h6>
                <div class="h3 text-info"><?php echo $paid_count; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Tasks</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/PC/staff/orders.php" class="btn btn-primary">
                        <i class="bi bi-box"></i> Process Orders
                    </a>
                    <a href="/PC/staff/inventory.php" class="btn btn-outline-primary">
                        <i class="bi bi-collection"></i> Check Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
