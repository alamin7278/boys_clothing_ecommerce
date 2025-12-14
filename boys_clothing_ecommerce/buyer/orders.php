<?php
/*
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    error_log("Unauthorized access to buyer/orders.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$buyerId = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT o.*, p.title, u.username AS seller_username 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON p.seller_id = u.id 
        WHERE o.buyer_id = ? 
        ORDER BY o.created_at DESC");
    $stmt->execute([$buyerId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching orders: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $orders = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Your Orders</h2>
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
                            <th>Seller</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['title']); ?></td>
                                <td><?php echo htmlspecialchars($order['seller_username']); ?></td>
                                <td><?php echo ucfirst($order['status']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>*/

session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    error_log("Unauthorized access to buyer dashboard - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Fetch buyer info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $buyer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$buyer) {
        error_log("Buyer not found for ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error fetching buyer info: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $error = "Database error: Unable to fetch buyer info.";
}

// Fetch buyer's orders
try {
    $stmt = $pdo->prepare("
        SELECT o.*, p.title, u.username AS seller_username, r.id AS return_id, r.status AS return_status
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON p.seller_id = u.id 
        LEFT JOIN returns r ON o.id = r.order_id
        WHERE o.buyer_id = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching orders: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $orders = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Buyer Dashboard</h2>

    <!-- Display Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Section -->
        <div class="col-md-4">
            <div class="card p-4">
                <h4>Profile</h4>
                <p>Username: <?php echo htmlspecialchars($buyer['username']); ?></p>
                <p>Email: <?php echo htmlspecialchars($buyer['email']); ?></p>
                <p><a href="/boys_clothing_ecommerce/buyer/wishlist.php" class="btn btn-primary">View Wishlist</a></p>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="col-md-8">
            <div class="card p-4">
                <h4>Your Orders</h4>
                <?php if (empty($orders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Seller</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['title']); ?></td>
                                    <td><?php echo htmlspecialchars($order['seller_username']); ?></td>
                                    <td><?php echo ucfirst($order['status']); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <?php if ($order['status'] == 'delivered' && !$order['return_id']): ?>
                                            <form action="/boys_clothing_ecommerce/buyer/request_return.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <textarea name="reason" class="form-control" placeholder="Reason for return" required></textarea>
                                                <button type="submit" class="btn btn-sm btn-warning mt-2">Request Return</button>
                                            </form>
                                        <?php elseif ($order['return_id']): ?>
                                            <span class="text-<?php echo $order['return_status'] == 'pending' ? 'warning' : ($order['return_status'] == 'approved' ? 'success' : 'danger'); ?>">
                                                Return: <?php echo ucfirst($order['return_status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>