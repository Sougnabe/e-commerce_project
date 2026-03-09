<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

function redirect_products(string $type, string $message): void {
    header('Location: ../seller/products.php?' . $type . '=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_products('error', 'Invalid request method');
}

if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
    redirect_products('error', 'Database unavailable');
}

$seller_id = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    redirect_products('error', 'Invalid product ID');
}

if ($action === 'delete') {
    $stmt = $conn->prepare('DELETE FROM products WHERE product_id = ? AND seller_id = ?');
    if (!$stmt) {
        redirect_products('error', 'Database error');
    }

    $stmt->bind_param('ii', $product_id, $seller_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
        redirect_products('success', 'Product deleted successfully');
    }

    redirect_products('error', 'Product not found or unauthorized');
}

if ($action === 'update') {
    $product_name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    $price = isset($_POST['price']) ? (float)$_POST['price'] : -1;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : -1;

    if ($product_name === '' || $description === '' || $category === '' || $location === '') {
        redirect_products('error', 'Missing required fields');
    }
    if ($price < 0 || $quantity < 0) {
        redirect_products('error', 'Invalid price or quantity');
    }
    if (!in_array($status, ['active', 'inactive', 'archived'], true)) {
        $status = 'active';
    }

    $stmt = $conn->prepare('UPDATE products
        SET product_name = ?, description = ?, category = ?, price = ?, quantity_available = ?, location = ?, status = ?
        WHERE product_id = ? AND seller_id = ?');
    if (!$stmt) {
        redirect_products('error', 'Database error');
    }

    $stmt->bind_param('sssdissii', $product_name, $description, $category, $price, $quantity, $location, $status, $product_id, $seller_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected >= 0) {
        redirect_products('success', 'Product updated successfully');
    }

    redirect_products('error', 'Product not found or unauthorized');
}

redirect_products('error', 'Unknown action');
