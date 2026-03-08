<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty_fields':
            $error_message = 'Please fill in all required fields.';
            break;
        case 'password_mismatch':
            $error_message = 'Passwords do not match.';
            break;
        case 'invalid_email':
            $error_message = 'Please enter a valid email address.';
            break;
        case 'user_exists':
            $error_message = 'Username or email already exists.';
            break;
        case 'db_unavailable':
            $error_message = 'Database is not available. Start MySQL and import schema.sql, then try again.';
            break;
        case 'registration_failed':
            $error_message = 'Registration failed. Please try again.';
            break;
    }
}
?>

    <section class="auth-section">
        <div class="auth-container">
            <h2>Register</h2>
            <?php if (!empty($error_message)): ?>
                <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;">
                    <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>
            <form id="registerForm" method="POST" action="../src/includes/process_register.php" class="auth-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="user_type">Register as:</label>
                    <select id="user_type" name="user_type" required>
                        <option value="customer">Customer</option>
                        <option value="seller">Seller</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location">
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </section>

<?php include_once '../src/includes/footer.php'; ?>
