<?php
/**
 * Header Template with Navigation
 */
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>PC Parts Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/PC/assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/PC/index.php">
                <i class="bi bi-pc-display"></i> PC Parts Store
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/PC/pages/products.php">Products</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/PC/pages/cart.php">
                            <i class="bi bi-cart"></i> Cart
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="badge bg-danger"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php $user = getCurrentUser(); ?>
                        
                        <?php if ($user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/PC/admin/dashboard.php">Admin Panel</a>
                            </li>
                        <?php elseif ($user['role'] === 'staff'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/PC/staff/dashboard.php">Staff Panel</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user['full_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php if ($user['role'] === 'customer'): ?>
                                    <li><a class="dropdown-item" href="/PC/pages/dashboard.php">My Dashboard</a></li>
                                    <li><a class="dropdown-item" href="/PC/pages/orders.php">My Orders</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/PC/auth/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/PC/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/PC/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Container -->
    <main class="py-4">
        <div class="container">
