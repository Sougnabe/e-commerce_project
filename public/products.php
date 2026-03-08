<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$price = trim($_GET['price'] ?? '');

$where = ["p.status = 'active'"];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(p.product_name LIKE ? OR p.description LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($category !== '') {
    $where[] = "p.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($price === '0-100') {
    $where[] = "p.price BETWEEN 0 AND 100";
} elseif ($price === '100-500') {
    $where[] = "p.price BETWEEN 100 AND 500";
} elseif ($price === '500-1000') {
    $where[] = "p.price BETWEEN 500 AND 1000";
} elseif ($price === '1000+') {
    $where[] = "p.price >= 1000";
}

$products = [];
$categories = [];

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $cat_result = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row['category_name'];
        }
    }

    $sql = "SELECT p.product_id, p.product_name, p.price, p.product_image, p.location, p.quantity_available,
            COALESCE(AVG(c.rating), 0) AS avg_rating
            FROM products p
            LEFT JOIN comments c ON c.product_id = p.product_id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY p.product_id
            ORDER BY p.created_at DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
    }
}
?>

    <section class="products-section">
        <div class="products-header">
            <h2>All Products</h2>
            <form method="GET" action="products.php" class="filters">
                <input type="text" id="searchInput" name="search" placeholder="Search products..." class="search-box" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <select id="categoryFilter" name="category" class="filter-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="priceFilter" name="price" class="filter-select">
                    <option value="">All Prices</option>
                    <option value="0-100" <?php echo $price === '0-100' ? 'selected' : ''; ?>>$0 - $100</option>
                    <option value="100-500" <?php echo $price === '100-500' ? 'selected' : ''; ?>>$100 - $500</option>
                    <option value="500-1000" <?php echo $price === '500-1000' ? 'selected' : ''; ?>>$500 - $1000</option>
                    <option value="1000+" <?php echo $price === '1000+' ? 'selected' : ''; ?>>$1000+</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="products-grid">
            <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                <p>Database is not connected. Start MySQL and import schema to view products.</p>
            <?php elseif (empty($products)): ?>
                <p>No products found for the selected filters.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <img class="product-image" src="<?php echo APP_URL . htmlspecialchars($product['product_image'] ?: 'public/images/placeholder.svg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="product-price">$<?php echo number_format((float)$product['price'], 2); ?></p>
                            <p class="product-rating">Rating: <?php echo number_format((float)$product['avg_rating'], 1); ?>/5</p>
                            <p>Stock: <?php echo (int)$product['quantity_available']; ?></p>
                            <p>Location: <?php echo htmlspecialchars($product['location'] ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
                            <a class="btn btn-primary" href="product-detail.php?id=<?php echo (int)$product['product_id']; ?>">View Product</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

<?php include_once '../src/includes/footer.php'; ?>
