<?php
/*
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Fetch buyer info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$buyer = $stmt->fetch();
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Buyer Dashboard</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h4>Profile</h4>
                <p>Username: <?php echo htmlspecialchars($buyer['username']); ?></p>
                <p>Email: <?php echo htmlspecialchars($buyer['email']); ?></p>
                <p><a href="/boys_clothing_ecommerce/buyer/wishlist.php" class="btn btn-primary">View Wishlist</a></p>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>
----------------------
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

// Fetch notifications
try {
    $stmt = $pdo->prepare("
        SELECT n.*, p.title 
        FROM notifications n 
        JOIN products p ON n.product_id = p.id 
        WHERE n.user_id = ? 
        ORDER BY n.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching notifications: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $notifications = [];
}

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_POST['notification_id']), $_SESSION['user_id']]);
        error_log("Notification ID: {$_POST['notification_id']} marked as read by buyer ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/buyer/dashboard.php");
        exit;
    } catch (PDOException $e) {
        error_log("Database error marking notification as read: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $error = "Database error: Unable to mark notification as read.";
    }
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

    <!-- Notifications Section -->
    <div class="card p-4 mb-4">
        <h4>Notifications</h4>
        <?php if (empty($notifications)): ?>
            <p>No notifications.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-warning'; ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                                <small class="text-muted d-block"><?php echo $notification['created_at']; ?></small>
                            </div>
                            <?php if (!$notification['is_read']): ?>
                                <form method="POST">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Mark as Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

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

// Fetch notifications
try {
    $stmt = $pdo->prepare("
        SELECT n.*, p.title 
        FROM notifications n 
        JOIN products p ON n.product_id = p.id 
        WHERE n.user_id = ? 
        ORDER BY n.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching notifications: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $notifications = [];
}

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_POST['notification_id']), $_SESSION['user_id']]);
        error_log("Notification ID: {$_POST['notification_id']} marked as read by buyer ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/buyer/dashboard.php");
        exit;
    } catch (PDOException $e) {
        error_log("Database error marking notification as read: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $error = "Database error: Unable to mark notification as read.";
    }
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

    <!-- Notifications Section -->
    <div class="card p-4 mb-4">
        <h4>Notifications</h4>
        <?php if (empty($notifications)): ?>
            <p>No notifications.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-warning'; ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                                <small class="text-muted d-block"><?php echo $notification['created_at']; ?></small>
                            </div>
                            <?php if (!$notification['is_read']): ?>
                                <form method="POST">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Mark as Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Profile Section -->
        <div class="col-md-4">
            <div class="card p-4">
                <h4>Profile</h4>
                <p>Username: <?php echo htmlspecialchars($buyer['username']); ?></p>
                <p>Email: <?php echo htmlspecialchars($buyer['email']); ?></p>
                <!-- Wishlist link removed -->
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
<?php require '../includes/footer.php'; ?>*/

session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Fetch buyer info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$buyer = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch buyer's orders
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

// Fetch notifications
$stmt = $pdo->prepare("
    SELECT n.*, p.title 
    FROM notifications n 
    JOIN products p ON n.product_id = p.id 
    WHERE n.user_id = ? 
    ORDER BY n.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_POST['notification_id']), $_SESSION['user_id']]);
    header("Location: /boys_clothing_ecommerce/buyer/dashboard.php");
    exit;
}
?>

<?php require '../includes/header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Buyer Dashboard</h2>

    <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">Notifications</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Orders</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile</button>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- Notifications Tab -->
        <div class="tab-pane fade show active" id="notifications" role="tabpanel">
            <div class="card p-4">
                <?php if (empty($notifications)): ?>
                    <p class="text-center">No notifications.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $notification['is_read'] ? '' : 'list-group-item-warning'; ?>">
                                <div>
                                    <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                                    <small class="d-block text-muted"><?php echo $notification['created_at']; ?></small>
                                </div>
                                <?php if (!$notification['is_read']): ?>
                                    <form method="POST">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Mark as Read</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Orders Tab -->
        <div class="tab-pane fade" id="orders" role="tabpanel">
            <div class="card p-4">
                <?php if (empty($orders)): ?>
                    <p class="text-center">No orders found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                        <td>
                                            <span class="badge <?php echo $order['status']=='delivered' ? 'bg-success' : 'bg-info'; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <?php if ($order['status']=='delivered' && !$order['return_id']): ?>
                                                <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#returnForm<?php echo $order['id']; ?>">Request Return</button>
                                                <div class="collapse mt-2" id="returnForm<?php echo $order['id']; ?>">
                                                    <form action="/boys_clothing_ecommerce/buyer/request_return.php" method="POST">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <textarea name="reason" class="form-control mb-2" placeholder="Reason for return" required></textarea>
                                                        <button type="submit" class="btn btn-sm btn-warning w-100">Submit Return</button>
                                                    </form>
                                                </div>
                                            <?php elseif ($order['return_id']): ?>
                                                <span class="badge <?php echo $order['return_status']=='pending' ? 'bg-warning text-dark' : ($order['return_status']=='approved' ? 'bg-success' : 'bg-danger'); ?>">
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
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile Tab -->
        <div class="tab-pane fade" id="profile" role="tabpanel">
            <div class="card p-4 text-center">
                <h4><?php echo htmlspecialchars($buyer['username']); ?></h4>
                <p>Email: <?php echo htmlspecialchars($buyer['email']); ?></p>
                <!--<a href="/boys_clothing_ecommerce/buyer/wishlist.php" class="btn btn-primary">View Wishlist</a> -->
            </div>
        </div>
    </div>
</div>

<script>
var triggerTabList = [].slice.call(document.querySelectorAll('#dashboardTabs button'))
triggerTabList.forEach(function (triggerEl) {
  var tabTrigger = new bootstrap.Tab(triggerEl)
  triggerEl.addEventListener('click', function (event) {
    event.preventDefault()
    tabTrigger.show()
  })
})
</script>

<?php require '../includes/footer.php'; ?>

