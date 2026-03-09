<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$seller_id = (int)$_SESSION['user_id'];
$status = $_GET['status'] ?? 'all';
$allowed = ['all', 'pending', 'completed', 'cancelled'];
if (!in_array($status, $allowed, true)) {
    $status = 'all';
}

$orders = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $sql = "SELECT o.order_id, o.quantity, o.total_price, o.order_status, o.order_date, p.product_name, u.username AS customer_name
        FROM orders o
        INNER JOIN products p ON p.product_id = o.product_id
        INNER JOIN users u ON u.user_id = o.customer_id
        WHERE o.seller_id = ?";

    if ($status !== 'all') {
        $sql .= " AND o.order_status = ?";
    }
    $sql .= " ORDER BY o.order_date DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if ($status === 'all') {
            $stmt->bind_param('i', $seller_id);
        } else {
            $stmt->bind_param('is', $seller_id, $status);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
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
                    <li><a href="orders.php" class="active">My Orders</a></li>
                    <li><a href="uploads.php">File Uploads</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Orders</h2>

            <div class="filter-section">
                <a class="btn <?php echo $status === 'all' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=all">All</a>
                <a class="btn <?php echo $status === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=pending">Pending</a>
                <a class="btn <?php echo $status === 'completed' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=completed">Completed</a>
                <a class="btn <?php echo $status === 'cancelled' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=cancelled">Cancelled</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="7">Database unavailable.</td></tr>
                    <?php elseif (empty($orders)): ?>
                        <tr><td colspan="7">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo (int)$order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$order['quantity']; ?></td>
                                <td>$<?php echo number_format((float)$order['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
