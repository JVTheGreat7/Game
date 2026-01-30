<?php
/**
 * Admin - Manage Categories
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('admin');

$page_title = 'Manage Categories';
include '../../includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $category_name = sanitize($_POST['category_name'] ?? '', $conn);
        
        if (!empty($category_name)) {
            $insert = "INSERT INTO categories (category_name) VALUES ('$category_name')";
            if ($conn->query($insert)) {
                $message = 'Category added successfully!';
            } else {
                $message = 'Error: ' . $conn->error;
            }
        }
    }
    
    elseif ($action === 'delete') {
        $category_id = sanitize($_POST['category_id'] ?? '', $conn);
        $delete = "DELETE FROM categories WHERE category_id = '$category_id'";
        
        if ($conn->query($delete)) {
            $message = 'Category deleted successfully!';
        } else {
            $message = 'Error deleting category';
        }
    }
}

// Get all categories
$categories = fetchAll($conn->query("SELECT * FROM categories ORDER BY category_name"));
?>

<div class="row mb-4">
    <div class="col-6">
        <h2>Manage Categories</h2>
    </div>
    <div class="col-6 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            + Add Category
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($categories as $category): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
