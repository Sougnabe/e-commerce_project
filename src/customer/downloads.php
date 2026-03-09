<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$downloads = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $customer_id = (int)$_SESSION['user_id'];
    $sql = "SELECT d.download_id, d.download_date, p.product_id, p.product_name
            FROM downloads d
            INNER JOIN products p ON p.product_id = d.product_id
            WHERE d.customer_id = ?
            ORDER BY d.download_date DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $downloads[] = $row;
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
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="downloads.php" class="active">Downloads</a></li>
                    <li><a href="reviews.php">My Reviews</a></li>
                    <li><a href="profile.php">Edit Profile</a></li>
                    <li><a href="addresses.php">Saved Addresses</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Downloads</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Download Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="4">Database unavailable.</td></tr>
                    <?php elseif (empty($downloads)): ?>
                        <tr><td colspan="4">No downloads found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($downloads as $download): ?>
                            <tr>
                                <td>#<?php echo (int)$download['download_id']; ?></td>
                                <td><?php echo htmlspecialchars($download['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($download['download_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="<?php echo BASE_URL; ?>product-detail.php?id=<?php echo (int)$download['product_id']; ?>">View Product</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
