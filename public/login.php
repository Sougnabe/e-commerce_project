<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$login_error = '';
$login_success = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty_fields':
            $login_error = 'Please enter username/email, password, and user type.';
            break;
        case 'invalid_password':
        case 'user_not_found':
            $login_error = 'Invalid credentials.';
            break;
        case 'db_unavailable':
            $login_error = 'Database is not available. Start MySQL and import schema.sql.';
            break;
        case 'system_error':
            $login_error = 'System error. Please try again.';
            break;
        case 'login_required':
            $login_error = 'Please login to continue.';
            break;
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $login_success = 'Registration successful. Please login.';
}
?>

    <section class="auth-section">
        <div class="auth-container">
            <h2>Login</h2>
            <?php if ($login_error !== ''): ?>
                <p style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($login_success !== ''): ?>
                <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;"><?php echo htmlspecialchars($login_success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form id="loginForm" method="POST" action="../src/includes/process_login.php" class="auth-form">
                <div class="form-group">
                    <label for="username">Username or Email:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="user_type">Login as:</label>
                    <select id="user_type" name="user_type" required>
                        <option value="customer">Customer</option>
                        <option value="seller">Seller</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </section>

<?php include_once '../src/includes/footer.php'; ?>
