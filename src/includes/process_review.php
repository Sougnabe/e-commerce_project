<?php
/*
 * Review Processing
 */

require_once '../../config/config.php';

session_start();

function respond_review(bool $success, string $message, int $product_id = 0): void {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }

    $query_key = $success ? 'success' : 'error';
    header('Location: ../../public/product-detail.php?id=' . (int)$product_id . '&' . $query_key . '=' . urlencode($message));
    exit;
}

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    respond_review(false, 'Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
        respond_review(false, 'Database unavailable');
    }

    $product_id = $_POST['product_id'] ?? 0;
    $customer_id = $_SESSION['user_id'];
    $rating = $_POST['rating'] ?? 0;
    $quality_rating = $_POST['quality_rating'] ?? 0;
    $comment_text = $_POST['comment'] ?? '';
    
    // Validate input
    if (empty($product_id) || empty($rating) || empty($quality_rating)) {
        respond_review(false, 'Missing required fields', (int)$product_id);
    }
    
    // Check if user has purchased this product
    $check_sql = "SELECT order_id FROM orders WHERE customer_id = ? AND product_id = ? AND order_status = 'completed'";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        respond_review(false, 'Could not validate purchases', (int)$product_id);
    }
    $check_stmt->bind_param('ii', $customer_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        respond_review(false, 'You can only review products you have purchased', (int)$product_id);
    }
    $check_stmt->close();
    
    // Check if already reviewed
    $review_check_sql = "SELECT comment_id FROM comments WHERE product_id = ? AND customer_id = ?";
    $review_check_stmt = $conn->prepare($review_check_sql);
    if (!$review_check_stmt) {
        respond_review(false, 'Could not check existing review', (int)$product_id);
    }
    $review_check_stmt->bind_param('ii', $product_id, $customer_id);
    $review_check_stmt->execute();
    $review_check_result = $review_check_stmt->get_result();
    
    if ($review_check_result->num_rows > 0) {
        // Update existing review
        $update_sql = "UPDATE comments SET rating = ?, quality_rating = ?, comment_text = ? WHERE product_id = ? AND customer_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            respond_review(false, 'Failed to update review', (int)$product_id);
        }
        $update_stmt->bind_param('iisii', $rating, $quality_rating, $comment_text, $product_id, $customer_id);
        
        if ($update_stmt->execute()) {
            respond_review(true, 'Review updated successfully', (int)$product_id);
        } else {
            respond_review(false, 'Failed to update review', (int)$product_id);
        }
        $update_stmt->close();
    } else {
        // Insert new review
        $insert_sql = "INSERT INTO comments (product_id, customer_id, rating, quality_rating, comment_text) 
                       VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            respond_review(false, 'Failed to submit review', (int)$product_id);
        }
        $insert_stmt->bind_param('iiiis', $product_id, $customer_id, $rating, $quality_rating, $comment_text);
        
        if ($insert_stmt->execute()) {
            respond_review(true, 'Review submitted successfully', (int)$product_id);
        } else {
            respond_review(false, 'Failed to submit review', (int)$product_id);
        }
        $insert_stmt->close();
    }
    
    $review_check_stmt->close();
    $conn->close();
} else {
    respond_review(false, 'Invalid request method');
}
?>
