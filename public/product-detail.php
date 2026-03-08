<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$product_id = (int)($_GET['id'] ?? 0);
$product = null;
$images = [];
$reviews = [];
$can_review = false;

if ($product_id > 0 && defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $sql = "SELECT p.*, u.username AS seller_name, u.email AS seller_email,
            COALESCE(AVG(c.rating), 0) AS avg_rating
            FROM products p
            INNER JOIN users u ON u.user_id = p.seller_id
            LEFT JOIN comments c ON c.product_id = p.product_id
            WHERE p.product_id = ? AND p.status = 'active'
            GROUP BY p.product_id";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($product) {
        $img_stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ? ORDER BY image_id ASC");
        if ($img_stmt) {
            $img_stmt->bind_param('i', $product_id);
            $img_stmt->execute();
            $img_result = $img_stmt->get_result();
            while ($row = $img_result->fetch_assoc()) {
                $images[] = $row['image_path'];
            }
            $img_stmt->close();
        }

        $review_stmt = $conn->prepare("SELECT c.rating, c.quality_rating, c.comment_text, c.created_at, u.username FROM comments c INNER JOIN users u ON u.user_id = c.customer_id WHERE c.product_id = ? ORDER BY c.created_at DESC");
        if ($review_stmt) {
            $review_stmt->bind_param('i', $product_id);
            $review_stmt->execute();
            $review_result = $review_stmt->get_result();
            while ($row = $review_result->fetch_assoc()) {
                $reviews[] = $row;
            }
            $review_stmt->close();
        }

        if (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'customer') {
            $check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE customer_id = ? AND product_id = ? AND order_status = 'completed' LIMIT 1");
            if ($check_stmt) {
                $customer_id = (int)$_SESSION['user_id'];
                $check_stmt->bind_param('ii', $customer_id, $product_id);
                $check_stmt->execute();
                $can_review = $check_stmt->get_result()->num_rows > 0;
                $check_stmt->close();
            }
        }
    }
}
?>

    <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
        <section class="page-section"><p>Database is not connected. Start MySQL and import schema to view product details.</p></section>
    <?php elseif (!$product): ?>
        <section class="page-section"><p>Product not found.</p></section>
    <?php else: ?>
    <section class="product-detail">
        <?php if (!empty($_GET['error'])): ?>
            <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif (!empty($_GET['success'])): ?>
            <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <div class="product-container">
            <div class="product-images">
                <div class="main-image">
                    <img id="mainImage" src="<?php echo APP_URL . htmlspecialchars($product['product_image'] ?: 'public/images/placeholder.svg', ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image">
                </div>
                <div class="thumbnail-images">
                    <?php foreach ($images as $image): ?>
                        <img src="<?php echo APP_URL . htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="Thumbnail">
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="product-info">
                <h2 id="productName"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <div class="rating">
                    <span id="averageRating"><?php echo number_format((float)$product['avg_rating'], 1); ?></span> / 5 stars
                </div>
                <div class="price">
                    <span class="currency">$</span>
                    <span id="productPrice"><?php echo number_format((float)$product['price'], 2); ?></span>
                </div>
                <p id="productDescription"><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                
                <div class="location-info">
                    <strong>Location:</strong> <span id="productLocation"><?php echo htmlspecialchars($product['location'] ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <p><strong>Available:</strong> <?php echo (int)$product['quantity_available']; ?></p>
                
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'customer'): ?>
                    <form method="POST" action="../src/includes/process_order.php">
                        <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="<?php echo (int)$product['quantity_available']; ?>" value="1" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery_location">Delivery location (optional)</label>
                            <input type="text" id="delivery_location" name="delivery_location" placeholder="Enter delivery location">
                        </div>
                        <button class="btn btn-primary" type="submit">Place Order</button>
                    </form>
                    <?php if (!empty($product['document_path'])): ?>
                        <p style="margin-top:12px;"><a class="btn btn-secondary" href="../src/includes/process_download.php?product_id=<?php echo (int)$product['product_id']; ?>">Download Product Document</a></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><a href="login.php">Login as customer</a> to order and download documents.</p>
                <?php endif; ?>
                
                <div class="seller-info">
                    <h3>Seller Information</h3>
                    <p id="sellerName"><?php echo htmlspecialchars($product['seller_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p id="sellerContact"><?php echo htmlspecialchars($product['seller_email'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>

        <section class="reviews-section">
            <h3>Customer Reviews</h3>
            <div class="reviews-list" id="reviewsList">
                <?php if (empty($reviews)): ?>
                    <p>No reviews yet.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="content-box" style="margin-bottom:10px;">
                            <p><strong><?php echo htmlspecialchars($review['username'], ENT_QUOTES, 'UTF-8'); ?></strong> - <?php echo htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p>Rating: <?php echo (int)$review['rating']; ?>/5 | Quality: <?php echo (int)$review['quality_rating']; ?>/5</p>
                            <p><?php echo nl2br(htmlspecialchars($review['comment_text'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if ($can_review): ?>
            <div class="add-review">
                <h4>Leave a Review</h4>
                <form id="reviewForm" method="POST" action="../src/includes/process_review.php">
                    <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <select id="rating" name="rating" required>
                            <option value="">Select Rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Very Poor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quality_rating">Product Quality:</label>
                        <select id="quality_rating" name="quality_rating" required>
                            <option value="">Select Quality Rating</option>
                            <option value="5">5 - Excellent Quality</option>
                            <option value="4">4 - Good Quality</option>
                            <option value="3">3 - Average Quality</option>
                            <option value="2">2 - Poor Quality</option>
                            <option value="1">1 - Very Poor Quality</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea id="comment" name="comment" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
            <?php elseif (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'customer'): ?>
                <p>You can review after a completed purchase.</p>
            <?php endif; ?>
        </section>
    </section>
    <?php endif; ?>

<?php include_once '../src/includes/footer.php'; ?>
