<?php
require_once '../../config/config.php';

// Check if user is logged in and is seller
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$stats = ['products' => 0, 'sales' => 0, 'earnings' => 0, 'pending' => 0];
$recent_orders = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $seller_id = (int)$_SESSION['user_id'];

    $stat_stmt = $conn->prepare("SELECT
        (SELECT COUNT(*) FROM products WHERE seller_id = ?) AS total_products,
        (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND order_status IN ('pending','completed')) AS total_sales,
        (SELECT COALESCE(SUM(total_price),0) FROM orders WHERE seller_id = ? AND order_status IN ('pending','completed')) AS earnings,
        (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND order_status = 'pending') AS pending_orders");
    if ($stat_stmt) {
        $stat_stmt->bind_param('iiii', $seller_id, $seller_id, $seller_id, $seller_id);
        $stat_stmt->execute();
        $data = $stat_stmt->get_result()->fetch_assoc();
        if ($data) {
            $stats['products'] = (int)$data['total_products'];
            $stats['sales'] = (int)$data['total_sales'];
            $stats['earnings'] = (float)$data['earnings'];
            $stats['pending'] = (int)$data['pending_orders'];
        }
        $stat_stmt->close();
    }

    $recent_stmt = $conn->prepare("SELECT o.order_id, p.product_name, u.username AS customer_name, o.quantity, o.total_price, o.order_status
        FROM orders o
        INNER JOIN products p ON p.product_id = o.product_id
        INNER JOIN users u ON u.user_id = o.customer_id
        WHERE o.seller_id = ?
        ORDER BY o.order_date DESC
        LIMIT 8");
    if ($recent_stmt) {
        $recent_stmt->bind_param('i', $seller_id);
        $recent_stmt->execute();
        $result = $recent_stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recent_orders[] = $row;
        }
        $recent_stmt->close();
    }
}
?>

    <section class="dashboard">
        <div class="dashboard-container">
            <aside class="sidebar">
                <nav class="seller-nav">
                    <ul>
                        <li><a href="dashboard.php" class="active">Dashboard</a></li>
                        <li><a href="products.php">My Products</a></li>
                        <li><a href="add-product.php">Add Product</a></li>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="uploads.php">File Uploads</a></li>
                        <li><a href="statistics.php">Statistics</a></li>
                        <li><a href="profile.php">My Profile</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="dashboard-content">
                <h2>Seller Dashboard</h2>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Total Products</h3>
                        <p class="stat-value" id="totalProducts"><?php echo $stats['products']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Sales</h3>
                        <p class="stat-value" id="totalSales"><?php echo $stats['sales']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Earnings</h3>
                        <p class="stat-value" id="earnings">$<?php echo number_format($stats['earnings'], 2); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Pending Orders</h3>
                        <p class="stat-value" id="pendingOrders"><?php echo $stats['pending']; ?></p>
                    </div>
                </div>

                <section class="recent-orders">
                    <h3>Recent Orders</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersTable">
                            <?php if (empty($recent_orders)): ?>
                                <tr><td colspan="6">No orders yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo (int)$order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo (int)$order['quantity']; ?></td>
                                        <td>$<?php echo number_format((float)$order['total_price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </main>
        </div>
    </section>

<?php include_once '../includes/footer.php'; ?>
