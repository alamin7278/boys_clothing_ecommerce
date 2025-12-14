<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    error_log("Unauthorized access to seller/orders.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$sellerId = $_SESSION['user_id'];

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = intval($_POST['order_id']);
    $status = $_POST['status'];
    if (in_array($status, ['shipped'])) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND product_id IN (SELECT id FROM products WHERE seller_id = ?)");
            $stmt->execute([$status, $orderId, $sellerId]);
            error_log("Order ID: $orderId updated to status: $status by seller ID: $sellerId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/seller/orders.php?success=Order updated");
            exit;
        } catch (PDOException $e) {
            error_log("Database error updating order: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $error = "Failed to update order.";
        }
    }
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("SELECT o.*, p.title, u.username AS buyer_username 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON o.buyer_id = u.id 
        WHERE p.seller_id = ? 
        ORDER BY o.created_at DESC");
    $stmt->execute([$sellerId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching orders: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $orders = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Manage Orders</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (empty($orders)): ?>
        <p class="text-center">No orders found.</p>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Buyer</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['title']); ?></td>
                                <td><?php echo htmlspecialchars($order['buyer_username']); ?></td>
                                <td><?php echo ucfirst($order['status']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="status" value="shipped">
                                            <button type="submit" class="btn btn-primary btn-sm">Mark as Shipped</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>