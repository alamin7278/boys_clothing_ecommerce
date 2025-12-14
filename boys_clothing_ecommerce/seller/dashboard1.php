<?php
/*
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    error_log("Unauthorized access to seller dashboard - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Fetch seller info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) {
        error_log("Seller not found for ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error fetching seller info: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $error = "Database error: Unable to fetch seller info.";
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_documents'])) {
    $uploadDir = '/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/Uploads/';
    $relativeUploadDir = '../Uploads/';
    $usedDir = $uploadDir;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        error_log("Created absolute Uploads directory: $uploadDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }
    if (!is_writable($uploadDir)) {
        error_log("Absolute Uploads directory not writable: $uploadDir, Owner: " . posix_getpwuid(fileowner($uploadDir))['name'] . ", Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $usedDir = $relativeUploadDir;
        if (!is_dir($relativeUploadDir)) {
            mkdir($relativeUploadDir, 0777, true);
            error_log("Created relative Uploads directory: $relativeUploadDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
        if (!is_writable($relativeUploadDir)) {
            $error = "Uploads directory is not writable. Check permissions for $relativeUploadDir.";
            error_log("Relative Uploads directory not writable: $relativeUploadDir, Owner: " . posix_getpwuid(fileowner($relativeUploadDir))['name'] . ", Permissions: " . substr(sprintf('%o', fileperms($relativeUploadDir)), -4), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    }

    if (!isset($error)) {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $nidFile = $_FILES['nid'];
        $certificateFile = $_FILES['certificate'];

        if ($nidFile['error'] === UPLOAD_ERR_NO_FILE || $certificateFile['error'] === UPLOAD_ERR_NO_FILE) {
            $error = "Both NID and certificate are required.";
        } elseif ($nidFile['error'] !== UPLOAD_ERR_OK || $certificateFile['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload error: NID (" . $nidFile['error'] . "), Certificate (" . $certificateFile['error'] . ").";
            error_log("Upload error - NID: {$nidFile['error']}, Certificate: {$certificateFile['error']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        } elseif ($nidFile['size'] > $maxFileSize || $certificateFile['size'] > $maxFileSize) {
            $error = "Files must be less than 5MB.";
        } elseif (!in_array($nidFile['type'], $allowedTypes) || !in_array($certificateFile['type'], $allowedTypes)) {
            $error = "Files must be JPG, PNG, or PDF.";
        } else {
            $nidPath = 'Uploads/' . uniqid('nid_') . '_' . basename($nidFile['name']);
            $certificatePath = 'Uploads/' . uniqid('cert_') . '_' . basename($certificateFile['name']);

            if (!move_uploaded_file($nidFile['tmp_name'], $usedDir . basename($nidPath))) {
                $error = "Failed to upload NID.";
                error_log("Failed to move NID file: {$nidFile['name']} to $usedDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            } elseif (!move_uploaded_file($certificateFile['tmp_name'], $usedDir . basename($certificatePath))) {
                $error = "Failed to upload certificate.";
                error_log("Failed to move certificate file: {$certificateFile['name']} to $usedDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET nid = ?, certificate = ?, verified = 'pending' WHERE id = ?");
                    $stmt->execute([$nidPath, $certificatePath, $_SESSION['user_id']]);
                    $success = "Documents uploaded successfully! Awaiting admin approval.";
                    header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
                    exit;
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                    error_log("Document upload DB error: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                }
            }
        }
    }
}

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_POST['notification_id']), $_SESSION['user_id']]);
        error_log("Notification ID: {$_POST['notification_id']} marked as read by seller ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
        exit;
    } catch (PDOException $e) {
        error_log("Database error marking notification as read: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $error = "Database error: Unable to mark notification as read.";
    }
}

// Fetch seller's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching products: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $products = [];
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("SELECT o.*, p.title, u.username AS buyer FROM orders o JOIN products p ON o.product_id = p.id JOIN users u ON o.buyer_id = u.id WHERE p.seller_id = ? ORDER BY o.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching orders: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $orders = [];
}

// Fetch notifications
try {
    $stmt = $pdo->prepare("SELECT n.*, p.title FROM notifications n JOIN products p ON n.product_id = p.id WHERE n.user_id = ? ORDER BY n.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching notifications: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $notifications = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Seller Dashboard</h2>
    
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
        <!-- Profile and Document Upload -->
        <div class="col-md-4">
            <div class="card p-4">
                <h4>Profile</h4>
                <p>Username: <?php echo htmlspecialchars($seller['username']); ?></p>
                <p>Email: <?php echo htmlspecialchars($seller['email']); ?></p>
                <p>Verification Status: 
                    <?php echo ucfirst($seller['verified']); ?>
                    <?php if ($seller['verified'] == 'pending'): ?>
                        <span class="text-warning">(Awaiting Approval)</span>
                    <?php elseif ($seller['verified'] == 'rejected'): ?>
                        <span class="text-danger">(Rejected - Please reupload)</span>
                    <?php elseif ($seller['verified'] == 'approved'): ?>
                        <span class="text-success">(Approved)</span>
                    <?php endif; ?>
                </p>
                <?php if ($seller['nid']): ?>
                    <p><a href="/boys_clothing_ecommerce/<?php echo htmlspecialchars($seller['nid']); ?>" target="_blank">View NID</a></p>
                <?php endif; ?>
                <?php if ($seller['certificate']): ?>
                    <p><a href="/boys_clothing_ecommerce/<?php echo htmlspecialchars($seller['certificate']); ?>" target="_blank">View Certificate</a></p>
                <?php endif; ?>
                <?php if ($seller['verified'] != 'approved'): ?>
                    <h4>Upload Verification Documents</h4>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php elseif (isset($success)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data" id="documentUploadForm">
                        <div class="mb-3">
                            <label for="nid" class="form-label">NID (PDF, JPG, PNG)</label>
                            <input type="file" class="form-control" id="nid" name="nid" accept=".pdf,.jpg,.png" required>
                        </div>
                        <div class="mb-3">
                            <label for="certificate" class="form-label">Chairman Certificate (PDF, JPG, PNG)</label>
                            <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.jpg,.png" required>
                        </div>
                        <button type="submit" name="upload_documents" class="btn btn-primary w-100">Upload Documents</button>
                    </form>
                <?php else: ?>
                    <p class="text-success">You are approved to sell products!</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Products and Orders -->
        <div class="col-md-8">
            <div class="card p-4 mb-4">
                <h4>Manage Products</h4>
                <?php if ($seller['verified'] == 'approved'): ?>
                    <a href="/boys_clothing_ecommerce/seller/add_product.php" class="btn btn-primary mb-3">Add New Product</a>
                <?php else: ?>
                    <p class="text-warning">You must be verified to add products.</p>
                <?php endif; ?>
                <?php if (empty($products)): ?>
                    <p>No products listed.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Hygiene</th>
                                <th>Laundry Memo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <?php
                                $laundryMemo = !empty($product['laundry_memo']) && file_exists("/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/{$product['laundry_memo']}") 
                                    ? "/boys_clothing_ecommerce/{$product['laundry_memo']}" 
                                    : null;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td><?php echo ucwords(str_replace('_', ' ', $product['category'])); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo ucfirst($product['status']); ?></td>
                                    <td>
                                        <?php 
                                        echo $product['category'] == 'hygiene' ? ucfirst($product['hygiene_verified']) : 'N/A';
                                        if ($product['category'] == 'hygiene' && $product['hygiene_verified'] == 'pending') {
                                            echo ' <span class="text-warning">(Awaiting Approval)</span>';
                                        } elseif ($product['category'] == 'hygiene' && $product['hygiene_verified'] == 'rejected') {
                                            echo ' <span class="text-danger">(Rejected)</span>';
                                        } elseif ($product['category'] == 'hygiene' && $product['hygiene_verified'] == 'approved') {
                                            echo ' <span class="text-success">(Approved)</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($laundryMemo): ?>
                                            <a href="<?php echo htmlspecialchars($laundryMemo); ?>" target="_blank">View Memo</a>
                                        <?php else: ?>
                                            None
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="card p-4">
                <h4>Manage Orders</h4>
                <?php if (empty($orders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Buyer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['title']); ?></td>
                                    <td><?php echo htmlspecialchars($order['buyer']); ?></td>
                                    <td><?php echo ucfirst($order['status']); ?></td>
                                    <td>
                                        <?php if ($order['status'] != 'delivered'): ?>
                                            <form action="/boys_clothing_ecommerce/seller/update_order.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="form-control d-inline w-auto">
                                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            </form>
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

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    error_log("Unauthorized access to seller dashboard - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Fetch seller info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) {
        error_log("Seller not found for ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error fetching seller info: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $error = "Database error: Unable to fetch seller info.";
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_documents'])) {
    $uploadDir = '/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/Uploads/';
    $relativeUploadDir = '../Uploads/';
    $usedDir = $uploadDir;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        error_log("Created absolute Uploads directory: $uploadDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }
    if (!is_writable($uploadDir)) {
        error_log("Absolute Uploads directory not writable: $uploadDir, Owner: " . posix_getpwuid(fileowner($uploadDir))['name'] . ", Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $usedDir = $relativeUploadDir;
        if (!is_dir($relativeUploadDir)) {
            mkdir($relativeUploadDir, 0777, true);
            error_log("Created relative Uploads directory: $relativeUploadDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
        if (!is_writable($relativeUploadDir)) {
            $error = "Uploads directory is not writable. Check permissions for $relativeUploadDir.";
            error_log("Relative Uploads directory not writable: $relativeUploadDir, Owner: " . posix_getpwuid(fileowner($relativeUploadDir))['name'] . ", Permissions: " . substr(sprintf('%o', fileperms($relativeUploadDir)), -4), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    }

    if (!isset($error)) {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $nidFile = $_FILES['nid'];
        $certificateFile = $_FILES['certificate'];

        if ($nidFile['error'] === UPLOAD_ERR_NO_FILE || $certificateFile['error'] === UPLOAD_ERR_NO_FILE) {
            $error = "Both NID and certificate are required.";
        } elseif ($nidFile['error'] !== UPLOAD_ERR_OK || $certificateFile['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload error: NID (" . $nidFile['error'] . "), Certificate (" . $certificateFile['error'] . ").";
            error_log("Upload error - NID: {$nidFile['error']}, Certificate: {$certificateFile['error']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        } elseif ($nidFile['size'] > $maxFileSize || $certificateFile['size'] > $maxFileSize) {
            $error = "Files must be less than 5MB.";
        } elseif (!in_array($nidFile['type'], $allowedTypes) || !in_array($certificateFile['type'], $allowedTypes)) {
            $error = "Files must be JPG, PNG, or PDF.";
        } else {
            $nidPath = 'Uploads/' . uniqid('nid_') . '_' . basename($nidFile['name']);
            $certificatePath = 'Uploads/' . uniqid('cert_') . '_' . basename($certificateFile['name']);

            if (!move_uploaded_file($nidFile['tmp_name'], $usedDir . basename($nidPath))) {
                $error = "Failed to upload NID.";
                error_log("Failed to move NID file: {$nidFile['name']} to $usedDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            } elseif (!move_uploaded_file($certificateFile['tmp_name'], $usedDir . basename($certificatePath))) {
                $error = "Failed to upload certificate.";
                error_log("Failed to move certificate file: {$certificateFile['name']} to $usedDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET nid = ?, certificate = ?, verified = 'pending' WHERE id = ?");
                    $stmt->execute([$nidPath, $certificatePath, $_SESSION['user_id']]);
                    $success = "Documents uploaded successfully! Awaiting admin approval.";
                    header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
                    exit;
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                    error_log("Document upload DB error: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                }
            }
        }
    }
}

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_POST['notification_id']), $_SESSION['user_id']]);
        error_log("Notification ID: {$_POST['notification_id']} marked as read by seller ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
        exit;
    } catch (PDOException $e) {
        error_log("Database error marking notification as read: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $error = "Database error: Unable to mark notification as read.";
    }
}

// Fetch seller's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching products: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $products = [];
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("SELECT o.*, p.title, u.username AS buyer FROM orders o JOIN products p ON o.product_id = p.id JOIN users u ON o.buyer_id = u.id WHERE p.seller_id = ? ORDER BY o.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching orders: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $orders = [];
}

// Fetch notifications
try {
    $stmt = $pdo->prepare("SELECT n.*, p.title FROM notifications n JOIN products p ON n.product_id = p.id WHERE n.user_id = ? ORDER BY n.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching notifications: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $notifications = [];
}

// Fetch return requests
try {
    $stmt = $pdo->prepare("
        SELECT r.*, o.id AS order_id, p.title, u.username AS buyer_username
        FROM returns r
        JOIN orders o ON r.order_id = o.id
        JOIN products p ON o.product_id = p.id
        JOIN users u ON o.buyer_id = u.id
        WHERE p.seller_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching return requests: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $returns = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">

    <h2 class="mb-4 text-center fw-bold">📊 Seller Dashboard</h2>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Products</h6>
                <h3 class="fw-bold text-primary"><?php echo count($products); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Orders</h6>
                <h3 class="fw-bold text-success"><?php echo count($orders); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Return Requests</h6>
                <h3 class="fw-bold text-danger"><?php echo count($returns); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Notifications</h6>
                <h3 class="fw-bold text-warning"><?php echo count($notifications); ?></h3>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h4 class="fw-bold mb-3">👤 Profile</h4>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($seller['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
        <p><strong>Verification Status:</strong> 
            <?php if ($seller['verified'] == 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
            <?php elseif ($seller['verified'] == 'rejected'): ?>
                <span class="badge bg-danger">Rejected</span>
            <?php elseif ($seller['verified'] == 'approved'): ?>
                <span class="badge bg-success">Approved</span>
            <?php endif; ?>
        </p>
        <?php if ($seller['nid']): ?>
            <p><a href="/boys_clothing_ecommerce/<?php echo htmlspecialchars($seller['nid']); ?>" target="_blank">📄 View NID</a></p>
        <?php endif; ?>
        <?php if ($seller['certificate']): ?>
            <p><a href="/boys_clothing_ecommerce/<?php echo htmlspecialchars($seller['certificate']); ?>" target="_blank">📄 View Certificate</a></p>
        <?php endif; ?>

        <?php if ($seller['verified'] != 'approved'): ?>
            <h5 class="mt-3">Upload Verification Documents</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3 mt-2">
                <div class="col-12">
                    <label for="nid" class="form-label">NID (PDF, JPG, PNG)</label>
                    <input type="file" class="form-control" id="nid" name="nid" accept=".pdf,.jpg,.png" required>
                </div>
                <div class="col-12">
                    <label for="certificate" class="form-label">Chairman Certificate (PDF, JPG, PNG)</label>
                    <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.jpg,.png" required>
                </div>
                <div class="col-12">
                    <button type="submit" name="upload_documents" class="btn btn-primary w-100">Upload Documents</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Notifications -->
    <div class="accordion mb-4" id="dashboardAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingNotifications">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotifications">
                    🔔 Notifications
                </button>
            </h2>
            <div id="collapseNotifications" class="accordion-collapse collapse show" data-bs-parent="#dashboardAccordion">
                <div class="accordion-body">
                    <?php if (empty($notifications)): ?>
                        <p>No notifications.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($notifications as $notification): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $notification['is_read'] ? '' : 'list-group-item-warning'; ?>">
                                    <span><?php echo htmlspecialchars($notification['message']); ?></span>
                                    <?php if (!$notification['is_read']): ?>
                                        <form method="POST">
                                            <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Mark Read</button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Returns -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingReturns">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReturns">
                    ↩️ Return Requests
                </button>
            </h2>
            <div id="collapseReturns" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                <div class="accordion-body">
                    <?php if (empty($returns)): ?>
                        <p>No return requests.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Product</th>
                                        <th>Buyer</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returns as $return): ?>
                                        <tr>
                                            <td>#<?php echo $return['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($return['title']); ?></td>
                                            <td><?php echo htmlspecialchars($return['buyer_username']); ?></td>
                                            <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                            <td><span class="badge bg-<?php echo $return['status'] == 'approved' ? 'success' : ($return['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($return['status']); ?></span></td>
                                            <td>
                                                <?php if ($return['status'] == 'pending'): ?>
                                                    <form action="/boys_clothing_ecommerce/seller/handle_return.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="return_id" value="<?php echo $return['id']; ?>">
                                                        <input type="hidden" name="order_id" value="<?php echo $return['order_id']; ?>">
                                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                                    </form>
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
        </div>

        <!-- Products -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingProducts">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
                    📦 Products
                </button>
            </h2>
            <div id="collapseProducts" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                <div class="accordion-body">
                    <?php if ($seller['verified'] == 'approved'): ?>
                        <a href="/boys_clothing_ecommerce/seller/add_product.php" class="btn btn-primary mb-3">➕ Add Product</a>
                    <?php else: ?>
                        <p class="text-warning">Verification required to add products.</p>
                    <?php endif; ?>

                    <?php if (empty($products)): ?>
                        <p>No products listed.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Hygiene</th>
                                        <th>Laundry Memo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <?php
                                        $laundryMemo = !empty($product['laundry_memo']) && file_exists("/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/{$product['laundry_memo']}") 
                                            ? "/boys_clothing_ecommerce/{$product['laundry_memo']}" 
                                            : null;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['title']); ?></td>
                                            <td><?php echo ucwords(str_replace('_', ' ', $product['category'])); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo ucfirst($product['status']); ?></span></td>
                                            <td>
                                                <?php if ($product['category'] == 'hygiene'): ?>
                                                    <span class="badge bg-<?php echo $product['hygiene_verified'] == 'approved' ? 'success' : ($product['hygiene_verified'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst($product['hygiene_verified']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($laundryMemo): ?>
                                                    <a href="<?php echo htmlspecialchars($laundryMemo); ?>" target="_blank">📄 View Memo</a>
                                                <?php else: ?>
                                                    None
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
        </div>

        <!-- Orders -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOrders">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrders">
                    📑 Orders
                </button>
            </h2>
            <div id="collapseOrders" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                <div class="accordion-body">
                    <?php if (empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Buyer</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['title']); ?></td>
                                            <td><?php echo htmlspecialchars($order['buyer']); ?></td>
                                            <td><span class="badge bg-info"><?php echo ucfirst($order['status']); ?></span></td>
                                            <td>
                                                <?php if ($order['status'] != 'delivered'): ?>
                                                    <form action="/boys_clothing_ecommerce/seller/update_order.php" method="POST" class="d-flex align-items-center gap-2">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm w-auto">
                                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                    </form>
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
        </div>

    </div>
</div>
<?php require '../includes/footer.php'; ?>


