<?php
/**
 * Admin - Manage Users
 */
require_once '../../config/db.php';
require_once '../../includes/auth.php';

requireRole('admin');

$page_title = 'Manage Users';
include '../../includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $user_id = sanitize($_POST['user_id'] ?? '', $conn);
        $delete = "DELETE FROM users WHERE user_id = '$user_id' AND role != 'admin'";
        
        if ($conn->query($delete)) {
            $message = 'User deleted successfully!';
        } else {
            $message = 'Error deleting user';
        }
    }
}

// Get all users
$users = fetchAll($conn->query("SELECT * FROM users ORDER BY created_at DESC"));
?>

<h2 class="mb-4">Manage Users</h2>

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
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'staff' ? 'warning' : 'primary'); ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
