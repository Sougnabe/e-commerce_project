<?php
require_once '../../config/config.php';

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../public/login.php');
    exit;
}

include_once '../includes/header.php';

if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli && isset($_GET['action'], $_GET['user_id'])) {
    $action = $_GET['action'];
    $target_user_id = (int)$_GET['user_id'];
    if ($target_user_id > 0 && $target_user_id !== (int)$_SESSION['user_id']) {
        $new_status = null;
        if ($action === 'suspend') {
            $new_status = 'suspended';
        } elseif ($action === 'activate') {
            $new_status = 'active';
        }

        if ($new_status !== null) {
            $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
            if ($update_stmt) {
                $update_stmt->bind_param('si', $new_status, $target_user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
    }
    header('Location: manage-users.php');
    exit;
}

$users = [];
if (defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $result = $conn->query("SELECT user_id, first_name, last_name, email, user_type, status, phone FROM users ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
}
?>

    <section class="dashboard">
        <div class="dashboard-container">
            <aside class="sidebar">
                <nav class="admin-nav">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="manage-users.php" class="active">Manage Users</a></li>
                        <li><a href="manage-products.php">Manage Products</a></li>
                        <li><a href="manage-categories.php">Manage Categories</a></li>
                        <li><a href="orders.php">View Orders</a></li>
                        <li><a href="reports.php">Reports</a></li>
                        <li><a href="settings.php">Settings</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="dashboard-content">
                <h2>Manage Users</h2>
                
                <div class="action-buttons">
                    <button class="btn btn-primary" id="addUserBtn">Add New User</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
                        <?php if (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                            <tr><td colspan="7">Database unavailable.</td></tr>
                        <?php elseif (empty($users)): ?>
                            <tr><td colspan="7">No users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo (int)$user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($user['user_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <a href="manage-users.php?action=suspend&user_id=<?php echo (int)$user['user_id']; ?>">Suspend</a>
                                        <?php else: ?>
                                            <a href="manage-users.php?action=activate&user_id=<?php echo (int)$user['user_id']; ?>">Activate</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </section>

<?php include_once '../includes/footer.php'; ?>
