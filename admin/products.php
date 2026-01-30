<?php
/**
 * Admin - Manage Products
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('admin');

$page_title = 'Manage Products';
include '../../includes/header.php';

$action = $_GET['action'] ?? '';
$product_id = $_GET['id'] ?? null;

// Handle product operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $category_id = sanitize($_POST['category_id'] ?? '', $conn);
        $product_name = sanitize($_POST['product_name'] ?? '', $conn);
        $brand = sanitize($_POST['brand'] ?? '', $conn);
        $price = (float)($_POST['price'] ?? 0);
        $specifications = sanitize($_POST['specifications'] ?? '', $conn);
        
        $insert = "INSERT INTO products (category_id, product_name, brand, price, specifications)
                   VALUES ('$category_id', '$product_name', '$brand', '$price', '$specifications')";
        
        if ($conn->query($insert)) {
            $message = 'Product added successfully!';
            $new_product_id = $conn->insert_id;
            
            // Add inventory
            $inv_insert = "INSERT INTO inventory (product_id, quantity) VALUES ('$new_product_id', 0)";
            $conn->query($inv_insert);
        } else {
            $message = 'Error adding product';
        }
    }
    
    elseif ($action === 'update') {
        $product_id = sanitize($_POST['product_id'] ?? '', $conn);
        $category_id = sanitize($_POST['category_id'] ?? '', $conn);
        $product_name = sanitize($_POST['product_name'] ?? '', $conn);
        $brand = sanitize($_POST['brand'] ?? '', $conn);
        $price = (float)($_POST['price'] ?? 0);
        $specifications = sanitize($_POST['specifications'] ?? '', $conn);
        $status = sanitize($_POST['status'] ?? 'available', $conn);
        
        $update = "UPDATE products SET category_id='$category_id', product_name='$product_name', 
                   brand='$brand', price='$price', specifications='$specifications', status='$status'
                   WHERE product_id='$product_id'";
        
        if ($conn->query($update)) {
            $message = 'Product updated successfully!';
        } else {
            $message = 'Error updating product';
        }
    }
    
    elseif ($action === 'delete') {
        $product_id = sanitize($_POST['product_id'] ?? '', $conn);
        $delete = "DELETE FROM products WHERE product_id='$product_id'";
        
        if ($conn->query($delete)) {
            $message = 'Product deleted successfully!';
        } else {
            $message = 'Error deleting product';
        }
    }
}

// Get all products
$products_query = "SELECT p.*, c.category_name, i.quantity FROM products p
                   LEFT JOIN categories c ON p.category_id = c.category_id
                   LEFT JOIN inventory i ON p.product_id = i.product_id
                   ORDER BY p.product_name";
$products_result = $conn->query($products_query);
$products = fetchAll($products_result);

// Get categories for dropdown
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_query);
$categories = fetchAll($categories_result);
?>

<div class="row mb-4">
    <div class="col-6">
        <h2>Manage Products</h2>
    </div>
    <div class="col-6 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            + Add Product
        </button>
    </div>
</div>

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
                <th>Product Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['quantity'] ?? 0; ?></td>
                    <td><span class="badge bg-<?php echo $product['status'] === 'available' ? 'success' : 'warning'; ?>"><?php echo ucfirst($product['status']); ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal" 
                                onclick="loadProduct(<?php echo $product['product_id']; ?>)">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['product_id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="product_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand">
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" name="price" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="specifications" class="form-label">Specifications</label>
                        <textarea class="form-control" name="specifications" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="edit_category_id" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="product_name" id="edit_product_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand" id="edit_brand">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" name="price" id="edit_price" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Specifications</label>
                        <textarea class="form-control" name="specifications" id="edit_specifications" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status">
                            <option value="available">Available</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadProduct(id) {
    // Fetch product data via AJAX or load from inline data
    fetch('/PC/admin/api/get-product.php?id=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit_product_id').value = data.product_id;
            document.getElementById('edit_category_id').value = data.category_id;
            document.getElementById('edit_product_name').value = data.product_name;
            document.getElementById('edit_brand').value = data.brand;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_specifications').value = data.specifications;
            document.getElementById('edit_status').value = data.status;
        });
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="product_id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
