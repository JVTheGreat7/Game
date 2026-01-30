<?php
/**
 * Staff - Check Inventory
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('staff');

$page_title = 'Inventory Check';
include '../../includes/header.php';

// Get low stock items
$low_stock = fetchAll($conn->query("
    SELECT p.product_id, p.product_name, p.brand, c.category_name, i.quantity
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN inventory i ON p.product_id = i.product_id
    WHERE i.quantity < 10
    ORDER BY i.quantity ASC
"));

// Get all inventory
$all_inventory = fetchAll($conn->query("
    SELECT p.product_id, p.product_name, p.brand, c.category_name, i.quantity
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN inventory i ON p.product_id = i.product_id
    ORDER BY p.product_name
"));
?>

<h2 class="mb-4">Inventory Management</h2>

<?php if (count($low_stock) > 0): ?>
    <div class="alert alert-warning">
        <strong>Alert:</strong> <?php echo count($low_stock); ?> product(s) have low stock (< 10 units)
    </div>
<?php endif; ?>

<!-- Low Stock -->
<?php if (count($low_stock) > 0): ?>
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Low Stock Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['brand']); ?></td>
                                <td><span class="badge bg-danger"><?php echo $item['quantity']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- All Inventory -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">All Products Inventory</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_inventory as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['brand']); ?></td>
                            <td><?php echo $item['quantity'] ?? 0; ?></td>
                            <td>
                                <?php if ($item['quantity'] > 20): ?>
                                    <span class="badge bg-success">Good</span>
                                <?php elseif ($item['quantity'] > 10): ?>
                                    <span class="badge bg-info">Moderate</span>
                                <?php elseif ($item['quantity'] > 0): ?>
                                    <span class="badge bg-warning">Low</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
