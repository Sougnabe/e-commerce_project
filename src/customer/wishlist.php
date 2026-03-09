<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../public/login.php');
    exit;
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
                    <li><a href="addresses.php">Saved Addresses</a></li>
                    <li><a href="wishlist.php" class="active">Wishlist</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Wishlist</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>No wishlist items found.</td></tr>
                </tbody>
            </table>

            <p style="margin-top:12px;">Browse products from the <a href="<?php echo BASE_URL; ?>products.php">Products page</a>.</p>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
