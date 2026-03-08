<?php
require_once '../config/config.php';
include_once '../src/includes/header.php';

$pending_orders = [];
$subtotal = 0.0;

if (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'customer' && defined('DB_CONNECTED') && DB_CONNECTED && $conn instanceof mysqli) {
    $customer_id = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT o.order_id, p.product_name, o.total_price, o.quantity
            FROM orders o
            INNER JOIN products p ON p.product_id = o.product_id
            WHERE o.customer_id = ? AND o.order_status = 'pending'
            ORDER BY o.order_date DESC");
    if ($stmt) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $pending_orders[] = $row;
            $subtotal += (float)$row['total_price'];
        }
        $stmt->close();
    }
}

$shipping = $subtotal > 0 ? 5.00 : 0.00;
$total = $subtotal + $shipping;
?>

    <section class="cart-section">
        <h2>Shopping Cart</h2>
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartItems">
                    <?php if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'customer'): ?>
                        <tr><td colspan="5">Please <a href="login.php">login as customer</a> to view your cart.</td></tr>
                    <?php elseif (!defined('DB_CONNECTED') || !DB_CONNECTED): ?>
                        <tr><td colspan="5">Database unavailable.</td></tr>
                    <?php elseif (empty($pending_orders)): ?>
                        <tr><td colspan="5">No pending orders in your cart.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pending_orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo number_format(((float)$order['total_price'] / max(1, (int)$order['quantity'])), 2); ?></td>
                                <td><?php echo (int)$order['quantity']; ?></td>
                                <td>$<?php echo number_format((float)$order['total_price'], 2); ?></td>
                                <td>Pending</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span id="shipping">$<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="total">$<?php echo number_format($total, 2); ?></span>
                </div>
                <a class="btn btn-primary" id="checkoutBtn" href="<?php echo APP_URL; ?>src/customer/orders.php">Proceed to Orders</a>
            </div>
        </div>
    </section>

<?php include_once '../src/includes/footer.php'; ?>
