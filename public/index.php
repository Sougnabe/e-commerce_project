<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$featured_products = [];
$home_categories = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $product_sql = "SELECT product_id, product_name, price, product_image FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 6";
    $product_result = $conn->query($product_sql);
    if ($product_result) {
        while ($row = $product_result->fetch_assoc()) {
            $featured_products[] = $row;
        }
    }

    $category_result = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC LIMIT 8");
    if ($category_result) {
        while ($row = $category_result->fetch_assoc()) {
            $home_categories[] = $row['category_name'];
        }
    }
}
?>

    <section class="hero">
        <div class="hero-content">
            <h2>Welcome to radji e-shopping</h2>
            <p>Your trusted online marketplace for quality products</p>
            <a href="products.php" class="btn btn-primary">Shop Now</a>
        </div>
    </section>

    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="products-grid">
            <?php if (empty($featured_products)): ?>
                <p>No products yet.</p>
            <?php else: ?>
                <?php foreach ($featured_products as $item): ?>
                    <article class="product-card">
                        <img class="product-image" src="<?php echo APP_URL . htmlspecialchars($item['product_image'] ?: 'public/images/placeholder.svg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="product-price">$<?php echo number_format((float)$item['price'], 2); ?></p>
                            <a class="btn btn-primary" href="product-detail.php?id=<?php echo (int)$item['product_id']; ?>">View Product</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="categories">
        <h2>Shop by Category</h2>
        <div class="category-grid">
            <?php if (empty($home_categories)): ?>
                <p>No categories yet.</p>
            <?php else: ?>
                <?php foreach ($home_categories as $cat): ?>
                    <a class="btn btn-secondary" href="products.php?category=<?php echo urlencode($cat); ?>"><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

<?php include_once '../src/includes/footer.php'; ?>
