<?php
require_once '../../config/config.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

$seller_id = (int)$_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($product_id <= 0) {
    header('Location: products.php?error=' . urlencode('Invalid product ID'));
    exit;
}

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $stmt = $conn->prepare('SELECT product_id, product_name, description, category, price, quantity_available, location, status FROM products WHERE product_id = ? AND seller_id = ?');
    if ($stmt) {
        $stmt->bind_param('ii', $product_id, $seller_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

if (!$product) {
    header('Location: products.php?error=' . urlencode('Product not found or unauthorized'));
    exit;
}

$categories = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $cat_result = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row['category_name'];
        }
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
                    <li><a href="products.php" class="active">My Products</a></li>
                    <li><a href="add-product.php">Add Product</a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="uploads.php">File Uploads</a></li>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Edit Product</h2>

            <form method="POST" action="../includes/process_seller_product.php" class="product-form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">

                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" required value="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (($product['category'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price ($):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required value="<?php echo htmlspecialchars((string)$product['price'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity Available:</label>
                    <input type="number" id="quantity" name="quantity" min="0" required value="<?php echo (int)$product['quantity_available']; ?>">
                </div>

                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($product['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="active" <?php echo ($product['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($product['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="archived" <?php echo ($product['status'] === 'archived') ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            </form>
        </main>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
