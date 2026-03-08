<?php
/*
 * Contact Form Processing
 */

require_once '../../config/config.php';

function respond_contact(bool $success, string $message): void {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }

    $param = $success ? 'success' : 'error';
    header('Location: ../../public/contact.php?' . $param . '=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        respond_contact(false, 'All fields are required');
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond_contact(false, 'Invalid email address');
    }
    
    // Send email (basic implementation)
    $to = 'info@nameeshopping.com';
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $email_body = "Name: " . $name . "\r\n";
    $email_body .= "Email: " . $email . "\r\n";
    $email_body .= "Subject: " . $subject . "\r\n\r\n";
    $email_body .= "Message:\r\n" . $message;
    
    // In a production environment, use a proper mail service
    if (mail($to, 'Contact Form: ' . $subject, $email_body, $headers)) {
        respond_contact(true, 'Message sent successfully. We will be in touch soon.');
    } else {
        respond_contact(false, 'Failed to send message. Please try again later.');
    }
} else {
    respond_contact(false, 'Invalid request method');
}
?>
