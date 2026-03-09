<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$reviews = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $customer_id = (int)$_SESSION['user_id'];
    $sql = "SELECT c.comment_id, c.rating, c.quality_rating, c.comment_text, c.comment_date, p.product_id, p.product_name
            FROM comments c
            INNER JOIN products p ON p.product_id = c.product_id
            WHERE c.customer_id = ?
            ORDER BY c.comment_date DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
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
                    <li><a href="downloads.php">Downloads</a></li>
                    <li><a href="reviews.php" class="active">My Reviews</a></li>
                    <li><a href="profile.php">Edit Profile</a></li>
                    <li><a href="addresses.php">Saved Addresses</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Reviews</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Quality</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="6">Database unavailable.</td></tr>
                    <?php elseif (empty($reviews)): ?>
                        <tr><td colspan="6">No reviews found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$review['rating']; ?>/5</td>
                                <td><?php echo (int)$review['quality_rating']; ?>/5</td>
                                <td><?php echo htmlspecialchars($review['comment_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($review['comment_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="<?php echo BASE_URL; ?>product-detail.php?id=<?php echo (int)$review['product_id']; ?>">View Product</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
