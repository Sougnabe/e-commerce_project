<?php
require_once '../../config/config.php';

// Check if user is logged in and is customer
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$status = $_GET['status'] ?? 'all';
$allowed = ['all', 'pending', 'completed', 'cancelled'];
if (!in_array($status, $allowed, true)) {
    $status = 'all';
}

$orders = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $sql = "SELECT o.order_id, o.quantity, o.total_price, o.order_status, o.order_date, p.product_id, p.product_name, s.username AS seller_name
            FROM orders o
            INNER JOIN products p ON p.product_id = o.product_id
            INNER JOIN users s ON s.user_id = o.seller_id
            WHERE o.customer_id = ?";
    if ($status !== 'all') {
        $sql .= " AND o.order_status = ?";
    }
    $sql .= " ORDER BY o.order_date DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $customer_id = (int)$_SESSION['user_id'];
        if ($status === 'all') {
            $stmt->bind_param('i', $customer_id);
        } else {
            $stmt->bind_param('is', $customer_id, $status);
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
                <nav class="customer-nav">
                    <ul>
                        <li><a href="dashboard.php">My Account</a></li>
                        <li><a href="orders.php" class="active">My Orders</a></li>
                        <li><a href="downloads.php">Downloads</a></li>
                        <li><a href="reviews.php">My Reviews</a></li>
                        <li><a href="profile.php">Edit Profile</a></li>
                        <li><a href="addresses.php">Saved Addresses</a></li>
                        <li><a href="wishlist.php">Wishlist</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="dashboard-content">
                <h2>My Orders</h2>
                
                <div class="filter-section">
                    <a class="btn <?php echo $status === 'all' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=all">All Orders</a>
                    <a class="btn <?php echo $status === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=pending">Pending</a>
                    <a class="btn <?php echo $status === 'completed' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=completed">Completed</a>
                    <a class="btn <?php echo $status === 'cancelled' ? 'btn-primary' : 'btn-secondary'; ?>" href="orders.php?status=cancelled">Cancelled</a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Seller</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                            <tr><td colspan="8">Database unavailable.</td></tr>
                        <?php elseif (empty($orders)): ?>
                            <tr><td colspan="8">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo (int)$order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($order['seller_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo (int)$order['quantity']; ?></td>
                                    <td>$<?php echo number_format((float)$order['total_price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><a href="<?php echo BASE_URL; ?>product-detail.php?id=<?php echo (int)$order['product_id']; ?>">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </section>

<?php include_once '../includes/footer.php'; ?>
