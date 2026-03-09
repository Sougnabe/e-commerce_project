<?php
/*
 * Registration Processing
 */

require_once '../../config/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!defined('DB_CONNECTED') || !DB_CONNECTED || !($conn instanceof mysqli)) {
        header('Location: ../../public/register.php?error=db_unavailable');
        exit;
    }

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'customer';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        header('Location: ../../public/register.php?error=empty_fields');
        exit;
    }
    
    // Check password match
    if ($password !== $confirm_password) {
        header('Location: ../../public/register.php?error=password_mismatch');
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../../public/register.php?error=invalid_email');
        exit;
    }
    
    // Check if username already exists
    $check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if (!$check_stmt) {
        header('Location: ../../public/register.php?error=registration_failed');
        exit;
    }
    
    $check_stmt->bind_param('ss', $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        header('Location: ../../public/register.php?error=user_exists');
        exit;
    }
    $check_stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $insert_sql = "INSERT INTO users (username, email, password, user_type, first_name, last_name, phone, location, status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
    $insert_stmt = $conn->prepare($insert_sql);
    
    if (!$insert_stmt) {
        header('Location: ../../public/register.php?error=registration_failed');
        exit;
    }
    
    $insert_stmt->bind_param('ssssssss', $username, $email, $hashed_password, $user_type, $first_name, $last_name, $phone, $location);
    
    if ($insert_stmt->execute()) {
        session_start();
        $_SESSION['user_id'] = $insert_stmt->insert_id;
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['email'] = $email;

        header('Location: ../../public/index.php');
        exit;
    } else {
        header('Location: ../../public/register.php?error=registration_failed');
        exit;
    }
    
    $insert_stmt->close();
    $conn->close();
} else {
    header('Location: ../../public/register.php');
    exit;
}
?>
