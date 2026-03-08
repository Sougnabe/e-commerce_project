<?php
require_once '../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php?error=login_required');
    exit;
}

if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
    header('Location: ../../public/products.php?error=db_unavailable');
    exit;
}

$product_id = (int)($_GET['product_id'] ?? 0);
if ($product_id <= 0) {
    header('Location: ../../public/products.php?error=invalid_download');
    exit;
}

$sql = "SELECT p.document_path FROM products p INNER JOIN orders o ON o.product_id = p.product_id WHERE p.product_id = ? AND o.customer_id = ? AND o.order_status IN ('pending','completed') LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Location: ../../public/product-detail.php?id=' . $product_id . '&error=download_unavailable');
    exit;
}

$customer_id = (int)$_SESSION['user_id'];
$stmt->bind_param('ii', $product_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$result || empty($result['document_path'])) {
    header('Location: ../../public/product-detail.php?id=' . $product_id . '&error=download_unavailable');
    exit;
}

$relative_path = str_replace(['..\\', '../'], '', $result['document_path']);
$file_path = realpath(__DIR__ . '/../../' . $relative_path);
$root_path = realpath(__DIR__ . '/../../');

if (!$file_path || strpos($file_path, $root_path) !== 0 || !is_file($file_path)) {
    header('Location: ../../public/product-detail.php?id=' . $product_id . '&error=file_missing');
    exit;
}

$log_sql = "INSERT INTO downloads (customer_id, product_id, document_path) VALUES (?, ?, ?)";
$log_stmt = $conn->prepare($log_sql);
if ($log_stmt) {
    $log_stmt->bind_param('iis', $customer_id, $product_id, $relative_path);
    $log_stmt->execute();
    $log_stmt->close();
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
