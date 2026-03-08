<?php
require_once '../../config/config.php';

// Check if user is logged in and is seller
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

$categories = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $cat_result = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row['category_name'];
        }
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
                        <li><a href="add-product.php" class="active">Add Product</a></li>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="uploads.php">File Uploads</a></li>
                        <li><a href="statistics.php">Statistics</a></li>
                        <li><a href="profile.php">My Profile</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="dashboard-content">
                <h2>Add New Product</h2>
                <?php if (!empty($_GET['success'])): ?>
                    <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;">Product added successfully.</p>
                <?php elseif (!empty($_GET['error'])): ?>
                    <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;">Could not add product.</p>
                <?php endif; ?>
                
                <form id="addProductForm" enctype="multipart/form-data" method="POST" action="../includes/process_product.php" class="product-form">
                    <div class="form-group">
                        <label for="product_name">Product Name:</label>
                        <input type="text" id="product_name" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($):</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity Available:</label>
                        <input type="number" id="quantity" name="quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="product_images">Product Images:</label>
                        <input type="file" id="product_images" name="product_images[]" multiple accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="document_file">Document/File (optional):</label>
                        <input type="file" id="document_file" name="document_file" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </main>
        </div>
    </section>

<?php include_once '../includes/footer.php'; ?>
