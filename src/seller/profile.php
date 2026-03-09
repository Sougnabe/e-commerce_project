<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

$seller_id = (int)$_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
        $error = 'Database unavailable. Please try again later.';
    } else {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');

        if ($first_name === '' || $last_name === '') {
            $error = 'First name and last name are required.';
        } else {
            $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, phone = ?, location = ? WHERE user_id = ? AND user_type = \'seller\'');
            if ($stmt) {
                $stmt->bind_param('ssssi', $first_name, $last_name, $phone, $location, $seller_id);
                if ($stmt->execute()) {
                    $success = 'Profile updated successfully.';
                } else {
                    $error = 'Unable to update profile right now.';
                }
                $stmt->close();
            } else {
                $error = 'Unable to update profile right now.';
            }
        }
    }
}

$user = ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'location' => ''];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $stmt = $conn->prepare('SELECT first_name, last_name, email, phone, location FROM users WHERE user_id = ? AND user_type = \'seller\'');
    if ($stmt) {
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $user = $result;
        }
        $stmt->close();
    }
}

include_once '../includes/header.php';
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
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="profile.php" class="active">My Profile</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>My Profile</h2>

            <?php if ($error !== ''): ?>
                <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success !== ''): ?>
                <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form method="POST" action="profile.php" class="auth-form" style="max-width: 600px;">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" disabled value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
