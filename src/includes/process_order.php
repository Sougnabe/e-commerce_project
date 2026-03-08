<?php
require_once '../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php?error=login_required');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/products.php');
    exit;
}

if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
    header('Location: ../../public/cart.php?error=db_unavailable');
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);
$delivery_location = trim($_POST['delivery_location'] ?? '');

if ($product_id <= 0 || $quantity <= 0) {
    header('Location: ../../public/products.php?error=invalid_order');
    exit;
}

$product_sql = "SELECT product_id, seller_id, price, quantity_available FROM products WHERE product_id = ? AND status = 'active'";
$product_stmt = $conn->prepare($product_sql);
if (!$product_stmt) {
    header('Location: ../../public/products.php?error=order_failed');
    exit;
}

$product_stmt->bind_param('i', $product_id);
$product_stmt->execute();
$product = $product_stmt->get_result()->fetch_assoc();
$product_stmt->close();

if (!$product) {
    header('Location: ../../public/products.php?error=product_not_found');
    exit;
}

if ((int)$product['quantity_available'] < $quantity) {
    header('Location: ../../public/product-detail.php?id=' . $product_id . '&error=insufficient_stock');
    exit;
}

$total_price = (float)$product['price'] * $quantity;
$customer_id = (int)$_SESSION['user_id'];
$seller_id = (int)$product['seller_id'];

$conn->begin_transaction();

try {
    $order_sql = "INSERT INTO orders (customer_id, seller_id, product_id, quantity, total_price, order_status, delivery_location) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
    $order_stmt = $conn->prepare($order_sql);
    if (!$order_stmt) {
        throw new RuntimeException('Could not create order.');
    }

    $order_stmt->bind_param('iiiids', $customer_id, $seller_id, $product_id, $quantity, $total_price, $delivery_location);
    if (!$order_stmt->execute()) {
        throw new RuntimeException('Order insert failed.');
    }
    $order_stmt->close();

    $stock_sql = "UPDATE products SET quantity_available = quantity_available - ? WHERE product_id = ?";
    $stock_stmt = $conn->prepare($stock_sql);
    if (!$stock_stmt) {
        throw new RuntimeException('Could not update stock.');
    }

    $stock_stmt->bind_param('ii', $quantity, $product_id);
    if (!$stock_stmt->execute()) {
        throw new RuntimeException('Stock update failed.');
    }
    $stock_stmt->close();

    $conn->commit();
    header('Location: ../../src/customer/orders.php?success=order_created');
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: ../../public/product-detail.php?id=' . $product_id . '&error=order_failed');
    exit;
}
