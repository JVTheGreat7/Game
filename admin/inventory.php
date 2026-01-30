<?php
/**
 * Admin - Manage Inventory
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('admin');

$page_title = 'Manage Inventory';
include '../../includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        $product_id = sanitize($_POST['product_id'] ?? '', $conn);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $reason = sanitize($_POST['reason'] ?? 'Manual adjustment', $conn);
        
        // Get current quantity
        $current = fetchOne($conn->query("SELECT quantity FROM inventory WHERE product_id = '$product_id'"));
        $current_qty = $current['quantity'] ?? 0;
        $change = $quantity - $current_qty;
        
        // Update inventory
        $update = "UPDATE inventory SET quantity = '$quantity' WHERE product_id = '$product_id'";
        if ($conn->query($update)) {
            // Log the change
            $log = "INSERT INTO inventory_logs (product_id, change_quantity, reason) 
                   VALUES ('$product_id', '$change', '$reason')";
            $conn->query($log);
            $message = 'Inventory updated successfully!';
        } else {
            $message = 'Error updating inventory';
        }
    }
}

// Get inventory
$query = "SELECT p.product_id, p.product_name, p.brand, c.category_name, i.quantity
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.category_id
          LEFT JOIN inventory i ON p.product_id = i.product_id
          ORDER BY p.product_name";
$inventory = fetchAll($conn->query($query));
?>

<h2 class="mb-4">Manage Inventory</h2>

<?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Current Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventory as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['brand']); ?></td>
                    <td>
                        <strong><?php echo $item['quantity'] ?? 0; ?></strong>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateInventoryModal"
                                onclick="setInventoryProduct(<?php echo $item['product_id']; ?>, '<?php echo htmlspecialchars($item['product_name']); ?>', <?php echo $item['quantity'] ?? 0; ?>)">
                            Update
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Update Inventory Modal -->
<div class="modal fade" id="updateInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Update Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" id="inv_product_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="inv_product_name" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Quantity</label>
                        <input type="number" class="form-control" id="inv_current_qty" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inv_new_qty" class="form-label">New Quantity</label>
                        <input type="number" class="form-control" id="inv_new_qty" name="quantity" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inv_reason" class="form-label">Reason for Change</label>
                        <input type="text" class="form-control" name="reason" placeholder="e.g., Stock received, damaged items, etc.">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setInventoryProduct(id, name, qty) {
    document.getElementById('inv_product_id').value = id;
    document.getElementById('inv_product_name').value = name;
    document.getElementById('inv_current_qty').value = qty;
    document.getElementById('inv_new_qty').value = qty;
}
</script>

<?php include '../../includes/footer.php'; ?>
