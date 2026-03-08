<?php
require_once '../../config/config.php';

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$stats = ['users' => 0, 'products' => 0, 'orders' => 0, 'revenue' => 0];
$recent_orders = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $summary = $conn->query("SELECT
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM products) AS total_products,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COALESCE(SUM(total_price),0) FROM orders WHERE order_status IN ('pending','completed')) AS total_revenue");
    if ($summary) {
        $row = $summary->fetch_assoc();
        if ($row) {
            $stats['users'] = (int)$row['total_users'];
            $stats['products'] = (int)$row['total_products'];
            $stats['orders'] = (int)$row['total_orders'];
            $stats['revenue'] = (float)$row['total_revenue'];
        }
    }

    $orders_result = $conn->query("SELECT o.order_id, u.username AS customer_name, p.product_name, o.total_price, o.order_status, o.order_date
            FROM orders o
            INNER JOIN users u ON u.user_id = o.customer_id
            INNER JOIN products p ON p.product_id = o.product_id
            ORDER BY o.order_date DESC
            LIMIT 10");
    if ($orders_result) {
        while ($row = $orders_result->fetch_assoc()) {
            $recent_orders[] = $row;
        }
    }
}
?>

    <section class="dashboard">
        <div class="dashboard-container">
            <aside class="sidebar">
                <nav class="admin-nav">
                    <ul>
                        <li><a href="dashboard.php" class="active">Dashboard</a></li>
                        <li><a href="manage-users.php">Manage Users</a></li>
                        <li><a href="manage-products.php">Manage Products</a></li>
                        <li><a href="manage-categories.php">Manage Categories</a></li>
                        <li><a href="orders.php">View Orders</a></li>
                        <li><a href="reports.php">Reports</a></li>
                        <li><a href="settings.php">Settings</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="dashboard-content">
                <h2>Admin Dashboard</h2>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p class="stat-value" id="totalUsers"><?php echo $stats['users']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Products</h3>
                        <p class="stat-value" id="totalProducts"><?php echo $stats['products']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Orders</h3>
                        <p class="stat-value" id="totalOrders"><?php echo $stats['orders']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Revenue</h3>
                        <p class="stat-value" id="totalRevenue">$<?php echo number_format($stats['revenue'], 2); ?></p>
                    </div>
                </div>

                <section class="recent-orders">
                    <h3>Recent Orders</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersTable">
                            <?php if (empty($recent_orders)): ?>
                                <tr><td colspan="6">No orders yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo (int)$order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>$<?php echo number_format((float)$order['total_price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
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
