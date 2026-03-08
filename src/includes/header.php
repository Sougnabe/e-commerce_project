<?php
/*
 * Header file - included in all pages
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>radji e-shopping</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>radji e-shopping</h1>
            </div>
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>products.php">Products</a></li>
                    
                    <?php if ($is_logged_in): ?>
                        <?php if ($user_type === 'seller'): ?>
                            <li><a href="<?php echo APP_URL; ?>src/seller/dashboard.php">Seller Dashboard</a></li>
                        <?php elseif ($user_type === 'admin'): ?>
                            <li><a href="<?php echo APP_URL; ?>src/admin/dashboard.php">Admin Dashboard</a></li>
                        <?php elseif ($user_type === 'customer'): ?>
                            <li><a href="<?php echo APP_URL; ?>src/customer/dashboard.php">My Account</a></li>
                            <li><a href="<?php echo APP_URL; ?>src/customer/orders.php">Orders</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo APP_URL; ?>src/includes/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>login.php">Login</a></li>
                        <li><a href="<?php echo BASE_URL; ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <div class="container">
