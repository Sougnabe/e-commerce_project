<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$seller_id = (int)$_SESSION['user_id'];
$products = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $stmt = $conn->prepare("SELECT p.product_id, p.product_name, p.category, p.price, p.quantity_available, p.status, p.created_at,
        (SELECT COUNT(*) FROM orders o WHERE o.product_id = p.product_id) AS total_orders
        FROM products p
        WHERE p.seller_id = ?
        ORDER BY p.created_at DESC");
    if ($stmt) {
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
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
                    <li><a href="products.php" class="active">My Products</a></li>
                    <li><a href="add-product.php">Add Product</a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="uploads.php">File Uploads</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Products</h2>

            <?php if (!empty($_GET['success'])): ?>
                <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php elseif (!empty($_GET['error'])): ?>
                <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Orders</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="8">Database unavailable.</td></tr>
                    <?php elseif (empty($products)): ?>
                        <tr><td colspan="8">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?php echo (int)$product['product_id']; ?></td>
                                <td><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($product['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo number_format((float)$product['price'], 2); ?></td>
                                <td><?php echo (int)$product['quantity_available']; ?></td>
                                <td><?php echo htmlspecialchars($product['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$product['total_orders']; ?></td>
                                <td>
                                    <a href="edit-product.php?id=<?php echo (int)$product['product_id']; ?>">Edit</a>
                                    <form method="POST" action="../includes/process_seller_product.php" style="display:inline; margin-left:8px;" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">
                                        <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
