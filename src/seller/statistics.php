<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$seller_id = (int)$_SESSION['user_id'];
$summary = ['products' => 0, 'orders' => 0, 'completed' => 0, 'pending' => 0, 'cancelled' => 0, 'revenue' => 0.0];
$category_stats = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $summary_stmt = $conn->prepare("SELECT
        (SELECT COUNT(*) FROM products WHERE seller_id = ?) AS total_products,
        (SELECT COUNT(*) FROM orders WHERE seller_id = ?) AS total_orders,
        (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND order_status = 'completed') AS completed_orders,
        (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND order_status = 'pending') AS pending_orders,
        (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND order_status = 'cancelled') AS cancelled_orders,
        (SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE seller_id = ? AND order_status IN ('pending','completed')) AS total_revenue");
    if ($summary_stmt) {
        $summary_stmt->bind_param('iiiiii', $seller_id, $seller_id, $seller_id, $seller_id, $seller_id, $seller_id);
        $summary_stmt->execute();
        $row = $summary_stmt->get_result()->fetch_assoc();
        if ($row) {
            $summary['products'] = (int)$row['total_products'];
            $summary['orders'] = (int)$row['total_orders'];
            $summary['completed'] = (int)$row['completed_orders'];
            $summary['pending'] = (int)$row['pending_orders'];
            $summary['cancelled'] = (int)$row['cancelled_orders'];
            $summary['revenue'] = (float)$row['total_revenue'];
        }
        $summary_stmt->close();
    }

    $cat_stmt = $conn->prepare("SELECT p.category, COUNT(*) AS products_count
        FROM products p
        WHERE p.seller_id = ?
        GROUP BY p.category
        ORDER BY products_count DESC");
    if ($cat_stmt) {
        $cat_stmt->bind_param('i', $seller_id);
        $cat_stmt->execute();
        $result = $cat_stmt->get_result();
        while ($item = $result->fetch_assoc()) {
            $category_stats[] = $item;
        }
        $cat_stmt->close();
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
                    <li><a href="uploads.php">File Uploads</a></li>
                    <li><a href="statistics.php" class="active">Statistics</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Statistics</h2>

            <div class="dashboard-stats">
                <div class="stat-card"><h3>Total Products</h3><p class="stat-value"><?php echo $summary['products']; ?></p></div>
                <div class="stat-card"><h3>Total Orders</h3><p class="stat-value"><?php echo $summary['orders']; ?></p></div>
                <div class="stat-card"><h3>Completed</h3><p class="stat-value"><?php echo $summary['completed']; ?></p></div>
                <div class="stat-card"><h3>Revenue</h3><p class="stat-value">$<?php echo number_format($summary['revenue'], 2); ?></p></div>
            </div>

            <table class="data-table" style="margin-top:16px;">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Pending Orders</td><td><?php echo $summary['pending']; ?></td></tr>
                    <tr><td>Cancelled Orders</td><td><?php echo $summary['cancelled']; ?></td></tr>
                </tbody>
            </table>

            <h3 style="margin-top:20px;">Products by Category</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Products</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($category_stats)): ?>
                        <tr><td colspan="2">No category data found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($category_stats as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['category'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$item['products_count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
