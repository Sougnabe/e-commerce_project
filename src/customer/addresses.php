<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php');
    exit;
}

$customer_id = (int)$_SESSION['user_id'];
$location = '';

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $stmt = $conn->prepare('SELECT location FROM users WHERE user_id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $location = $result['location'] ?? '';
        $stmt->close();
    }
}

include_once '../includes/header.php';
?>

<section class="dashboard">
    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="customer-nav">
                <ul>
                    <li><a href="dashboard.php">My Account</a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="downloads.php">Downloads</a></li>
                    <li><a href="reviews.php">My Reviews</a></li>
                    <li><a href="profile.php">Edit Profile</a></li>
                    <li><a href="addresses.php" class="active">Saved Addresses</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Saved Addresses</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="2">Database unavailable.</td></tr>
                    <?php elseif ($location === ''): ?>
                        <tr><td colspan="2">No saved address found.</td></tr>
                    <?php else: ?>
                        <tr>
                            <td>Default</td>
                            <td><?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <p style="margin-top:12px;">Tip: Update your location from <a href="profile.php">Edit Profile</a>.</p>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
