<?php
require_once '../../config/config.php';

// Check if user is logged in and is customer
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$user = null;
$stats = ['orders' => 0, 'downloads' => 0, 'reviews' => 0];
$recent_orders = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $customer_id = (int)$_SESSION['user_id'];

    $user_stmt = $conn->prepare("SELECT first_name, last_name, email, phone, location FROM users WHERE user_id = ?");
    if ($user_stmt) {
        $user_stmt->bind_param('i', $customer_id);
        $user_stmt->execute();
        $user = $user_stmt->get_result()->fetch_assoc();
        $user_stmt->close();
    }

    $count_stmt = $conn->prepare("SELECT
            (SELECT COUNT(*) FROM orders WHERE customer_id = ?) AS total_orders,
            (SELECT COUNT(*) FROM downloads WHERE customer_id = ?) AS total_downloads,
            (SELECT COUNT(*) FROM comments WHERE customer_id = ?) AS total_reviews");
    if ($count_stmt) {
        $count_stmt->bind_param('iii', $customer_id, $customer_id, $customer_id);
        $count_stmt->execute();
        $counts = $count_stmt->get_result()->fetch_assoc();
        if ($counts) {
            $stats['orders'] = (int)$counts['total_orders'];
            $stats['downloads'] = (int)$counts['total_downloads'];
            $stats['reviews'] = (int)$counts['total_reviews'];
        }
        $count_stmt->close();
    }

    $recent_stmt = $conn->prepare("SELECT o.order_id, p.product_name, o.total_price, o.order_status, o.order_date
            FROM orders o INNER JOIN products p ON p.product_id = o.product_id
            WHERE o.customer_id = ? ORDER BY o.order_date DESC LIMIT 5");
    if ($recent_stmt) {
        $recent_stmt->bind_param('i', $customer_id);
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
                <nav class="customer-nav">
                    <ul>
                        <li><a href="dashboard.php" class="active">My Account</a></li>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="downloads.php">Downloads</a></li>
                        <li><a href="reviews.php">My Reviews</a></li>
                        <li><a href="profile.php">Edit Profile</a></li>
                        <li><a href="addresses.php">Saved Addresses</a></li>
                        <li><a href="wishlist.php">Wishlist</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="dashboard-content">
                <h2>My Account</h2>
                
                <section class="account-info">
                    <h3>Account Information</h3>
                    <div class="info-box">
                        <p><strong>Name:</strong> <span id="userName"><?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <p><strong>Email:</strong> <span id="userEmail"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <p><strong>Phone:</strong> <span id="userPhone"><?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <p><strong>Location:</strong> <span id="userLocation"><?php echo htmlspecialchars($user['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></p>
                    </div>
                </section>

                <section class="quick-stats">
                    <h3>Quick Stats</h3>
                    <div class="stats-grid">
                        <div class="stat-box">
                            <p class="stat-label">Total Orders</p>
                            <p class="stat-value" id="totalOrders"><?php echo $stats['orders']; ?></p>
                        </div>
                        <div class="stat-box">
                            <p class="stat-label">Downloads</p>
                            <p class="stat-value" id="totalDownloads"><?php echo $stats['downloads']; ?></p>
                        </div>
                        <div class="stat-box">
                            <p class="stat-label">Reviews Posted</p>
                            <p class="stat-value" id="totalReviews"><?php echo $stats['reviews']; ?></p>
                        </div>
                        <div class="stat-box">
                            <p class="stat-label">Wishlist Items</p>
                            <p class="stat-value" id="wishlistItems">0</p>
                        </div>
                    </div>
                </section>

                <section class="recent-activities">
                    <h3>Recent Orders</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersTable">
                            <?php if (empty($recent_orders)): ?>
                                <tr><td colspan="5">No recent orders.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo (int)$order['order_id']; ?></td>
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
