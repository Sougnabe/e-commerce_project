<?php
/*
 * Login Processing
 */

require_once '../../config/config.php';

session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
        header('Location: ../../public/login.php?error=db_unavailable');
        exit;
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password) || empty($user_type)) {
        header('Location: ../../public/login.php?error=empty_fields');
        exit;
    }
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND user_type = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        header('Location: ../../public/login.php?error=system_error');
        exit;
    }
    
    $stmt->bind_param('sss', $username, $username, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['email'] = $user['email'];
            
            header('Location: ../../public/index.php');
            exit;
        } else {
            header('Location: ../../public/login.php?error=invalid_password');
            exit;
        }
    } else {
        header('Location: ../../public/login.php?error=user_not_found');
        exit;
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: ../../public/login.php');
    exit;
}
?>
