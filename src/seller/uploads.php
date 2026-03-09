<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$seller_id = (int)$_SESSION['user_id'];
$uploads = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $stmt = $conn->prepare("SELECT product_id, product_name, product_image, document_path, created_at
        FROM products
        WHERE seller_id = ?
        ORDER BY created_at DESC");
    if ($stmt) {
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $uploads[] = $row;
        }
        $stmt->close();
    }
}
?>

<section class="dashboard">
    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="seller-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="products.php">My Products</a></li>
                    <li><a href="add-product.php">Add Product</a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="uploads.php" class="active">File Uploads</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>File Uploads</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Document</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="4">Database unavailable.</td></tr>
                    <?php elseif (empty($uploads)): ?>
                        <tr><td colspan="4">No uploaded files found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($uploads as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if (!empty($item['product_image'])): ?>
                                        <a href="<?php echo APP_URL . htmlspecialchars($item['product_image'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">View Image</a>
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($item['document_path'])): ?>
                                        <a href="<?php echo APP_URL . htmlspecialchars($item['document_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">View Document</a>
                                    <?php else: ?>
                                        No document
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
