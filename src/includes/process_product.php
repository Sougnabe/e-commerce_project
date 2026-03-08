<?php
/*
 * Product Processing
 */

require_once '../../config/config.php';

session_start();

function respond_product(bool $success, string $message, int $product_id = 0): void {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message, 'product_id' => $product_id]);
        exit;
    }

    if ($success) {
        header('Location: ../seller/add-product.php?success=1');
    } else {
        header('Location: ../seller/add-product.php?error=' . urlencode($message));
    }
    exit;
}

// Check if user is logged in and is a seller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    respond_product(false, 'Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
        respond_product(false, 'Database unavailable');
    }

    $seller_id = $_SESSION['user_id'];
    $product_name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    $location = $_POST['location'] ?? '';
    
    // Validate input
    if (empty($product_name) || empty($description) || empty($price) || empty($quantity)) {
        respond_product(false, 'Missing required fields');
    }
    
    // Insert product
    $sql = "INSERT INTO products (seller_id, product_name, description, category, price, quantity_available, location, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        respond_product(false, 'Database error');
    }
    
    $stmt->bind_param('isssdis', $seller_id, $product_name, $description, $category, $price, $quantity, $location);
    
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        $first_uploaded_image = null;
        
        // Handle image uploads
        if (isset($_FILES['product_images'])) {
            $upload_dir = UPLOAD_DIR . 'products/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['product_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = uniqid() . '_' . basename($_FILES['product_images']['name'][$key]);
                    $filepath = $upload_dir . $filename;
                    $relative_path = 'uploads/products/' . $filename;
                    
                    if (move_uploaded_file($tmp_name, $filepath)) {
                        if ($first_uploaded_image === null) {
                            $first_uploaded_image = $relative_path;
                        }
                        // Insert image record
                        $img_sql = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                        $img_stmt = $conn->prepare($img_sql);
                        $img_stmt->bind_param('is', $product_id, $relative_path);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        
        // Handle document upload
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = UPLOAD_DIR . 'documents/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $filename = uniqid() . '_' . basename($_FILES['document_file']['name']);
            $filepath = $upload_dir . $filename;
            $relative_path = 'uploads/documents/' . $filename;
            
            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $filepath)) {
                // Update product with document path
                $doc_sql = "UPDATE products SET document_path = ? WHERE product_id = ?";
                $doc_stmt = $conn->prepare($doc_sql);
                $doc_stmt->bind_param('si', $relative_path, $product_id);
                $doc_stmt->execute();
                $doc_stmt->close();
            }
        }

        if ($first_uploaded_image !== null) {
            $cover_sql = "UPDATE products SET product_image = ? WHERE product_id = ?";
            $cover_stmt = $conn->prepare($cover_sql);
            if ($cover_stmt) {
                $cover_stmt->bind_param('si', $first_uploaded_image, $product_id);
                $cover_stmt->execute();
                $cover_stmt->close();
            }
        }
        
        respond_product(true, 'Product added successfully', $product_id);
    } else {
        respond_product(false, 'Failed to add product');
    }
    
    $stmt->close();
    $conn->close();
} else {
    respond_product(false, 'Invalid request method');
}
?>
