<?php
/**
 * Login Page
 */
require_once '../config/db.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /PC/index.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (loginUser($conn, $email, $password)) {
        $redirect = $_GET['redirect'] ?? '/PC/index.php';
        header('Location: ' . $redirect);
        exit();
    } else {
        $error_message = 'Invalid email or password';
    }
}

$redirect = $_GET['redirect'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PC Parts Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Login</h2>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <p class="text-center">
                            Don't have an account? <a href="register.php">Register here</a>
                        </p>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <strong>Test Accounts:</strong>
                            <ul class="mb-0">
                                <li><strong>Admin:</strong> admin@pcparts.local / admin123</li>
                                <li><strong>Staff:</strong> staff@pcparts.local / staff123</li>
                                <li><strong>Customer:</strong> john@example.com / customer123</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
