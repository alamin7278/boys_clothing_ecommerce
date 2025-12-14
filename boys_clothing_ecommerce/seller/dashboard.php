<?php
/*
session_start();
require '../includes/config.php';

// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch seller info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$seller_id]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) {
        header("Location: /boys_clothing_ecommerce/login.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle document upload (existing code omitted for brevity)

// Fetch seller's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("
        SELECT o.*, p.title 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE p.seller_id = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

// Calculate total earnings after 15% commission
$totalEarnings = 0;
foreach ($orders as $order) {
    if ($order['status'] == 'delivered') {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$order['product_id']]);
        $price = $stmt->fetchColumn();
        $totalEarnings += $price * 0.85; // 15% commission
    }
}

// Fetch seller payouts
try {
    $stmt = $pdo->prepare("SELECT * FROM payouts WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payouts = [];
}

// Handle payout request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_payout'])) {
    $amount = floatval($_POST['amount']);
    $bkash_number = trim($_POST['bkash_number']);

    if ($amount <= 0 || $amount > $totalEarnings) {
        $error = "Invalid payout amount.";
    } elseif (empty($bkash_number)) {
        $error = "Bkash number is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO payouts (seller_id, amount, status, method, phone_number) VALUES (?, ?, 'pending', 'bkash', ?)");
            $stmt->execute([$seller_id, $amount, $bkash_number]);
            $success = "Payout request submitted successfully.";
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

require '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4 text-center fw-bold">📊 Seller Dashboard</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

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
                <h6 class="text-muted">Total Earnings ($)</h6>
                <h3 class="fw-bold text-warning"><?php echo number_format($totalEarnings, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Payouts Requested</h6>
                <h3 class="fw-bold text-danger"><?php echo count($payouts); ?></h3>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h4 class="fw-bold mb-3">👤 Profile</h4>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($seller['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
        <p><strong>Verification Status:</strong> <?php echo ucfirst($seller['verified']); ?></p>
    </div>

    <!-- Accordion Sections -->
    <div class="accordion" id="dashboardAccordion">
        <!-- Products Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
                    📦 Products
                </button>
            </h2>
            <div id="collapseProducts" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <?php if(empty($products)): ?>
                        <p>No products listed.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price ($)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                                            <td><?php echo number_format($p['price'],2); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrders">
                    📑 Orders
                </button>
            </h2>
            <div id="collapseOrders" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?php if(empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $o): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($o['title']); ?></td>
                                            <td><?php echo ucfirst($o['status']); ?></td>
                                            <td><?php if($o['status'] != 'delivered'): ?>
                                                <form method="POST" action="/boys_clothing_ecommerce/seller/update_order.php">
                                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                                    <select name="status">
                                                        <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option>
                                                        <option value="shipped" <?php echo $o['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                                        <option value="delivered" <?php echo $o['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                </form>
                                                <?php else: ?>-
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

        <!-- Payout Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayouts">
                    💰 Payouts (Bkash)
                </button>
            </h2>
            <div id="collapsePayouts" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <h5>Request Payout</h5>
                    <form method="POST" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Amount ($)</label>
                            <input type="number" name="amount" class="form-control" min="1" max="<?php echo floor($totalEarnings); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Bkash Number</label>
                            <input type="text" name="bkash_number" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="request_payout" class="btn btn-success w-100">Submit Payout Request</button>
                        </div>
                    </form>

                    <h5>Past Payouts</h5>
                    <?php if(empty($payouts)): ?>
                        <p>No payout history.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Amount ($)</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Requested At</th>
                                        <th>Processed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payouts as $p): ?>
                                        <tr>
                                            <td><?php echo number_format($p['amount'],2); ?></td>
                                            <td><?php echo strtoupper($p['method']) . ' - ' . htmlspecialchars($p['phone_number']); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                            <td><?php echo $p['created_at']; ?></td>
                                            <td><?php echo $p['processed_at'] ?? '-'; ?></td>
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


.............


session_start();
require '../includes/config.php';

// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch seller info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$seller_id]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) {
        header("Location: /boys_clothing_ecommerce/login.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_documents'])) {
    $uploadDir = '../Uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $error = "Failed to create upload directory.";
        }
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxFileSize = 5 * 1024 * 1024;

    $nidFile = $_FILES['nid'];
    $certificateFile = $_FILES['certificate'];

    if ($nidFile['error'] === UPLOAD_ERR_NO_FILE || $certificateFile['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Both NID and certificate are required.";
    } elseif ($nidFile['size'] > $maxFileSize || $certificateFile['size'] > $maxFileSize) {
        $error = "Files must be less than 5MB.";
    } elseif (!in_array($nidFile['type'], $allowedTypes) || !in_array($certificateFile['type'], $allowedTypes)) {
        $error = "Files must be JPG, PNG, or PDF.";
    } else {
        // Generate unique filenames
        $nidFileName = uniqid('nid_') . '_' . basename($nidFile['name']);
        $certificateFileName = uniqid('cert_') . '_' . basename($certificateFile['name']);
        
        // Paths for database (relative to root - used in HTML)
        $nidPath = 'Uploads/' . $nidFileName;
        $certificatePath = 'Uploads/' . $certificateFileName;
        
        // Full paths for file system operations (relative to seller/ directory)
        $nidFullPath = $uploadDir . $nidFileName;
        $certificateFullPath = $uploadDir . $certificateFileName;

        if (!move_uploaded_file($nidFile['tmp_name'], $nidFullPath)) {
            $lastError = error_get_last();
            $error = "Failed to upload NID. " . ($lastError ? $lastError['message'] : '');
        } elseif (!move_uploaded_file($certificateFile['tmp_name'], $certificateFullPath)) {
            $lastError = error_get_last();
            $error = "Failed to upload certificate. " . ($lastError ? $lastError['message'] : '');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET nid = ?, certificate = ?, verified = 'pending' WHERE id = ?");
                $stmt->execute([$nidPath, $certificatePath, $seller_id]);
                $success = "Documents uploaded successfully! Awaiting admin approval.";
                header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
                exit;
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Handle payout request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_payout'])) {
    $amount = floatval($_POST['amount']);
    $bkash_number = trim($_POST['bkash_number']);

    if ($amount <= 0) {
        $error = "Invalid payout amount.";
    } elseif (empty($bkash_number)) {
        $error = "Bkash number is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO payouts (seller_id, amount, status, method, phone_number) VALUES (?, ?, 'pending', 'bkash', ?)");
            $stmt->execute([$seller_id, $amount, $bkash_number]);
            $success = "Payout request submitted successfully.";
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch seller's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("
        SELECT o.*, p.title, u.username AS buyer 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON o.buyer_id = u.id
        WHERE p.seller_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

// Calculate total earnings after 15% commission
$totalEarnings = 0;
foreach ($orders as $order) {
    if ($order['status'] == 'delivered') {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$order['product_id']]);
        $price = $stmt->fetchColumn();
        $totalEarnings += $price * 0.85;
    }
}

// Fetch seller payouts
try {
    $stmt = $pdo->prepare("SELECT * FROM payouts WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payouts = [];
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
    $stmt->execute([$seller_id]);
    $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $returns = [];
}

require '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4 text-center fw-bold">📊 Seller Dashboard</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

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
                <h6 class="text-muted">Total Earnings ($)</h6>
                <h3 class="fw-bold text-warning"><?php echo number_format($totalEarnings, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h4 class="fw-bold mb-3">👤 Profile</h4>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($seller['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
        <p><strong>Verification Status:</strong> <?php echo ucfirst($seller['verified']); ?></p>

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

    <!-- Accordion Sections -->
    <div class="accordion" id="dashboardAccordion">

        <!-- Products Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
                    📦 Products
                </button>
            </h2>
            <div id="collapseProducts" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <?php if(empty($products)): ?>
                        <p>No products listed.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price ($)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                                            <td><?php echo number_format($p['price'],2); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrders">
                    📑 Orders
                </button>
            </h2>
            <div id="collapseOrders" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?php if(empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Buyer</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $o): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($o['title']); ?></td>
                                            <td><?php echo htmlspecialchars($o['buyer']); ?></td>
                                            <td><?php echo ucfirst($o['status']); ?></td>
                                            <td>
                                                <?php if($o['status'] != 'delivered'): ?>
                                                <form method="POST" action="/boys_clothing_ecommerce/seller/update_order.php">
                                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                                    <select name="status">
                                                        <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option>
                                                        <option value="shipped" <?php echo $o['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                                        <option value="delivered" <?php echo $o['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                </form>
                                                <?php else: ?>-
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

        <!-- Return Requests Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReturns">
                    ↩️ Return Requests
                </button>
            </h2>
            <div id="collapseReturns" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?php if(empty($returns)): ?>
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
                                            <td>
                                                <span class="badge bg-<?php echo $return['status']=='approved'?'success':($return['status']=='pending'?'warning':'danger'); ?>">
                                                    <?php echo ucfirst($return['status']); ?>
                                                </span>
                                            </td>
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

        <!-- Payouts Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayouts">
                    💰 Payouts (Bkash)
                </button>
            </h2>
            <div id="collapsePayouts" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <h5>Request Payout</h5>
                    <form method="POST" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Amount ($)</label>
                            <input type="number" name="amount" class="form-control" min="1" max="<?php echo floor($totalEarnings); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Bkash Number</label>
                            <input type="text" name="bkash_number" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="request_payout" class="btn btn-success w-100">Submit Payout Request</button>
                        </div>
                    </form>

                    <h5>Past Payouts</h5>
                    <?php if(empty($payouts)): ?>
                        <p>No payout history.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Amount ($)</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Requested At</th>
                                        <th>Processed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payouts as $p): ?>
                                        <tr>
                                            <td><?php echo number_format($p['amount'],2); ?></td>
                                            <td><?php echo strtoupper($p['method']) . ' - ' . htmlspecialchars($p['phone_number']); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                            <td><?php echo $p['created_at']; ?></td>
                                            <td><?php echo $p['processed_at'] ?? '-'; ?></td>
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







session_start();
require '../includes/config.php';

// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch seller info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$seller_id]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) header("Location: /boys_clothing_ecommerce/login.php");
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_documents'])) {
    $uploadDir = '../Uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $error = "Failed to create upload directory.";
        }
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxFileSize = 5 * 1024 * 1024;

    $nidFile = $_FILES['nid'];
    $certificateFile = $_FILES['certificate'];

    if ($nidFile['error'] === UPLOAD_ERR_NO_FILE || $certificateFile['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Both NID and certificate are required.";
    } elseif ($nidFile['size'] > $maxFileSize || $certificateFile['size'] > $maxFileSize) {
        $error = "Files must be less than 5MB.";
    } elseif (!in_array($nidFile['type'], $allowedTypes) || !in_array($certificateFile['type'], $allowedTypes)) {
        $error = "Files must be JPG, PNG, or PDF.";
    } else {
        // Generate unique filenames
        $nidFileName = uniqid('nid_') . '_' . basename($nidFile['name']);
        $certificateFileName = uniqid('cert_') . '_' . basename($certificateFile['name']);
        
        // Paths for database (relative to root - used in HTML)
        $nidPath = 'Uploads/' . $nidFileName;
        $certificatePath = 'Uploads/' . $certificateFileName;
        
        // Full paths for file system operations (relative to seller/ directory)
        $nidFullPath = $uploadDir . $nidFileName;
        $certificateFullPath = $uploadDir . $certificateFileName;

        if (!move_uploaded_file($nidFile['tmp_name'], $nidFullPath)) {
            $lastError = error_get_last();
            $error = "Failed to upload NID. " . ($lastError ? $lastError['message'] : '');
        } elseif (!move_uploaded_file($certificateFile['tmp_name'], $certificateFullPath)) {
            $lastError = error_get_last();
            $error = "Failed to upload certificate. " . ($lastError ? $lastError['message'] : '');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET nid = ?, certificate = ?, verified = 'pending' WHERE id = ?");
                $stmt->execute([$nidPath, $certificatePath, $seller_id]);
                $success = "Documents uploaded successfully! Awaiting admin approval.";
                header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
                exit;
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}


// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../Uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = uniqid('product_') . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $error = "Product image must be JPG or PNG.";
        } elseif (!move_uploaded_file($fileTmp, $filePath)) {
            $error = "Failed to upload product image.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (seller_id, title, category, price, description, image, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$seller_id, $title, $category, $price, $description, 'Uploads/'.$fileName, $status]);
                $success = "Product added successfully.";
                header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
                exit;
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $error = "Product image is required.";
    }
}


// Fetch seller's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("
        SELECT o.*, p.title, u.username AS buyer 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON o.buyer_id = u.id
        WHERE p.seller_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
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
    $stmt->execute([$seller_id]);
    $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $returns = [];
}

// Calculate total sales and earnings after approved returns
$totalSales = 0;
$totalEarnings = 0;
foreach ($orders as $order) {
    if ($order['status'] === 'delivered') {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$order['product_id']]);
        $price = floatval($stmt->fetchColumn());

        $stmtReturn = $pdo->prepare("SELECT COUNT(*) FROM returns WHERE order_id = ? AND status = 'approved'");
        $stmtReturn->execute([$order['id']]);
        $hasApprovedReturn = $stmtReturn->fetchColumn() > 0;

        if (!$hasApprovedReturn) {
            $totalSales += $price;
            $totalEarnings += $price * 0.85; // 15% commission
        }
    }
}

// Fetch seller payouts
try {
    $stmt = $pdo->prepare("SELECT * FROM payouts WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payouts = [];
}

// Calculate collected, pending, and payout left
$collectedPayout = 0;
$pendingPayout = 0;
foreach ($payouts as $p) {
    if ($p['status'] === 'completed') $collectedPayout += $p['amount'];
    elseif ($p['status'] === 'pending') $pendingPayout += $p['amount'];
}
$payoutLeft = max(0, $totalEarnings - ($collectedPayout + $pendingPayout));

// Handle payout request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_payout'])) {
    $amount = floatval($_POST['amount']);
    $bkash_number = trim($_POST['bkash_number']);

    if ($amount <= 0) {
        $error = "Invalid payout amount.";
    } elseif (empty($bkash_number)) {
        $error = "Bkash number is required.";
    } elseif ($amount > $payoutLeft) {
        $error = "Amount exceeds available payout.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO payouts (seller_id, amount, status, method, phone_number) VALUES (?, ?, 'pending', 'bkash', ?)");
            $stmt->execute([$seller_id, $amount, $bkash_number]);
            $success = "Payout request submitted successfully.";
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

require '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4 text-center fw-bold">📊 Seller Dashboard</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Products</h6>
                <h3 class="fw-bold text-primary"><?php echo count($products); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Orders</h6>
                <h3 class="fw-bold text-success"><?php echo count($orders); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Return Requests</h6>
                <h3 class="fw-bold text-danger"><?php echo count($returns); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Total Sales ($)</h6>
                <h3 class="fw-bold text-info"><?php echo number_format($totalSales, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Total Earnings ($)</h6>
                <h3 class="fw-bold text-warning"><?php echo number_format($totalEarnings, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Payout Left ($)</h6>
                <h3 class="fw-bold text-danger"><?php echo number_format($payoutLeft, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h4 class="fw-bold mb-3">👤 Profile</h4>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($seller['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
        <p><strong>Verification Status:</strong> <?php echo ucfirst($seller['verified']); ?></p>

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

    <!-- Accordion Sections for Products, Orders, Returns, Payouts -->
    <div class="accordion" id="dashboardAccordion">

    <!-- Add New Product Form -->
<div class="card shadow-sm border-0 p-4 mb-4">
    <h5 class="fw-bold mb-3">➕ Add New Product</h5>
    <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Product Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Price ($)</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="col-md-8">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Product Image</label>
            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" name="add_product" class="btn btn-primary w-100">Add Product</button>
        </div>
    </form>
</div>


        <!-- Products Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
                    📦 Products
                </button>
            </h2>
            <div id="collapseProducts" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <?php if(empty($products)): ?>
                        <p>No products listed.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price ($)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                                            <td><?php echo number_format($p['price'],2); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrders">
            📑 Orders
        </button>
    </h2>
    <div id="collapseOrders" class="accordion-collapse collapse">
        <div class="accordion-body">
            <?php if(empty($orders)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Buyer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $o): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($o['title']); ?></td>
                                    <td><?php echo htmlspecialchars($o['buyer']); ?></td>
                                    <td><?php echo ucfirst($o['status']); ?></td>
                                    <td>
                                        <?php if($o['status'] != 'delivered'): ?>
                                            <form method="POST" action="/boys_clothing_ecommerce/seller/update_order.php" class="d-flex gap-2">
                                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option>
                                                    <option value="shipped" <?php echo $o['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                                    <option value="delivered" <?php echo $o['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        <?php else: ?>
                                            -
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


        <!-- Return Requests Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReturns">
                    ↩️ Return Requests
                </button>
            </h2>
            <div id="collapseReturns" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?php if(empty($returns)): ?>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returns as $return): ?>
                                        <tr>
                                            <td>#<?php echo $return['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($return['title']); ?></td>
                                            <td><?php echo htmlspecialchars($return['buyer_username']); ?></td>
                                            <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $return['status']=='approved'?'success':($return['status']=='pending'?'warning':'danger'); ?>">
                                                    <?php echo ucfirst($return['status']); ?>
                                                </span>
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

        <!-- Payouts Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayouts">
                    💰 Payouts (Bkash)
                </button>
            </h2>
            <div id="collapsePayouts" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <h5>Request Payout</h5>
                    <form method="POST" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Amount ($)</label>
                            <input type="number" name="amount" class="form-control" min="1" max="<?php echo floor($payoutLeft); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Bkash Number</label>
                            <input type="text" name="bkash_number" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="request_payout" class="btn btn-success w-100">Submit Payout Request</button>
                        </div>
                    </form>

                    <h5>Past Payouts</h5>
                    <?php if(empty($payouts)): ?>
                        <p>No payout history.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Amount ($)</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Requested At</th>
                                        <th>Processed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payouts as $p): ?>
                                        <tr>
                                            <td><?php echo number_format($p['amount'],2); ?></td>
                                            <td><?php echo strtoupper($p['method']) . ' - ' . htmlspecialchars($p['phone_number']); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                            <td><?php echo $p['created_at']; ?></td>
                                            <td><?php echo $p['processed_at'] ?? '-'; ?></td>
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

<?php require '../includes/footer.php'; ?>*/




session_start();
require '../includes/config.php';

// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch seller info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$seller_id]);
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$seller) header("Location: /boys_clothing_ecommerce/login.php");
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_documents'])) {
    $uploadDir = '../Uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $error = "Failed to create upload directory.";
        }
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxFileSize = 5 * 1024 * 1024;

    $nidFile = $_FILES['nid'];
    $certificateFile = $_FILES['certificate'];

    if ($nidFile['error'] === UPLOAD_ERR_NO_FILE || $certificateFile['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Both NID and certificate are required.";
    } elseif ($nidFile['size'] > $maxFileSize || $certificateFile['size'] > $maxFileSize) {
        $error = "Files must be less than 5MB.";
    } elseif (!in_array($nidFile['type'], $allowedTypes) || !in_array($certificateFile['type'], $allowedTypes)) {
        $error = "Files must be JPG, PNG, or PDF.";
    } else {
        // Generate unique filenames
        $nidFileName = uniqid('nid_') . '_' . basename($nidFile['name']);
        $certificateFileName = uniqid('cert_') . '_' . basename($certificateFile['name']);
        
        // Paths for database (relative to root - used in HTML)
        $nidPath = 'Uploads/' . $nidFileName;
        $certificatePath = 'Uploads/' . $certificateFileName;
        
        // Full paths for file system operations (relative to seller/ directory)
        $nidFullPath = $uploadDir . $nidFileName;
        $certificateFullPath = $uploadDir . $certificateFileName;

        if (!move_uploaded_file($nidFile['tmp_name'], $nidFullPath)) {
            $lastError = error_get_last();
            $error = "Failed to upload NID. " . ($lastError ? $lastError['message'] : '');
        } elseif (!move_uploaded_file($certificateFile['tmp_name'], $certificateFullPath)) {
            $lastError = error_get_last();
            $error = "Failed to upload certificate. " . ($lastError ? $lastError['message'] : '');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET nid = ?, certificate = ?, verified = 'pending' WHERE id = ?");
                $stmt->execute([$nidPath, $certificatePath, $seller_id]);
                $success = "Documents uploaded successfully! Awaiting admin approval.";
                header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
                exit;
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch seller's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

// Fetch seller's orders
try {
    $stmt = $pdo->prepare("
        SELECT o.*, p.title, u.username AS buyer 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        JOIN users u ON o.buyer_id = u.id
        WHERE p.seller_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
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
    $stmt->execute([$seller_id]);
    $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $returns = [];
}

// Calculate total sales and earnings after approved returns
$totalSales = 0;
$totalEarnings = 0;
foreach ($orders as $order) {
    if ($order['status'] === 'delivered') {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$order['product_id']]);
        $price = floatval($stmt->fetchColumn());

        $stmtReturn = $pdo->prepare("SELECT COUNT(*) FROM returns WHERE order_id = ? AND status = 'approved'");
        $stmtReturn->execute([$order['id']]);
        $hasApprovedReturn = $stmtReturn->fetchColumn() > 0;

        if (!$hasApprovedReturn) {
            $totalSales += $price;
            $totalEarnings += $price * 0.85; // 15% commission
        }
    }
}

// Fetch seller payouts
try {
    $stmt = $pdo->prepare("SELECT * FROM payouts WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payouts = [];
}

// Calculate collected, pending, and payout left
$collectedPayout = 0;
$pendingPayout = 0;
foreach ($payouts as $p) {
    if ($p['status'] === 'completed') $collectedPayout += $p['amount'];
    elseif ($p['status'] === 'pending') $pendingPayout += $p['amount'];
}
$payoutLeft = max(0, $totalEarnings - ($collectedPayout + $pendingPayout));

// Handle payout request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_payout'])) {
    $amount = floatval($_POST['amount']);
    $bkash_number = trim($_POST['bkash_number']);

    if ($amount <= 0) {
        $error = "Invalid payout amount.";
    } elseif (empty($bkash_number)) {
        $error = "Bkash number is required.";
    } elseif ($amount > $payoutLeft) {
        $error = "Amount exceeds available payout.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO payouts (seller_id, amount, status, method, phone_number) VALUES (?, ?, 'pending', 'bkash', ?)");
            $stmt->execute([$seller_id, $amount, $bkash_number]);
            $success = "Payout request submitted successfully.";
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

require '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4 text-center fw-bold">📊 Seller Dashboard</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Products</h6>
                <h3 class="fw-bold text-primary"><?php echo count($products); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Orders</h6>
                <h3 class="fw-bold text-success"><?php echo count($orders); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Return Requests</h6>
                <h3 class="fw-bold text-danger"><?php echo count($returns); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Total Sales ($)</h6>
                <h3 class="fw-bold text-info"><?php echo number_format($totalSales, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Total Earnings ($)</h6>
                <h3 class="fw-bold text-warning"><?php echo number_format($totalEarnings, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 text-center p-3">
                <h6 class="text-muted">Payout Left ($)</h6>
                <h3 class="fw-bold text-danger"><?php echo number_format($payoutLeft, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h4 class="fw-bold mb-3">👤 Profile</h4>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($seller['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
        <p><strong>Verification Status:</strong> <?php echo ucfirst($seller['verified']); ?></p>

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

    <!-- Accordion Sections for Products, Orders, Returns, Payouts -->
    <div class="accordion" id="dashboardAccordion">

        <!-- Products Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
                        📦 Products
                    </button>
                    <?php if ($seller['verified'] == 'approved'): ?>
                        <a href="/boys_clothing_ecommerce/seller/add_product.php" class="btn btn-sm btn-primary ms-2">➕</a>
                    <?php endif; ?>
                </div>
            </h2>
            <div id="collapseProducts" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <?php if(empty($products)): ?>
                        <p>No products listed.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price ($)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                                            <td><?php echo number_format($p['price'],2); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrders">
                    📑 Orders
                </button>
            </h2>
            <div id="collapseOrders" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?php if(empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Buyer</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $o): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($o['title']); ?></td>
                                            <td><?php echo htmlspecialchars($o['buyer']); ?></td>
                                            <td><?php echo ucfirst($o['status']); ?></td>
                                            <td>
                                                <?php if($o['status'] != 'delivered'): ?>
                                                    <form method="POST" action="/boys_clothing_ecommerce/seller/update_order.php" class="d-flex gap-2">
                                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm">
                                                            <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option>
                                                            <option value="shipped" <?php echo $o['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                                            <option value="delivered" <?php echo $o['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                    </form>
                                                <?php else: ?>
                                                    -
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

        <!-- Return Requests Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReturns">
                    ↩️ Return Requests
                </button>
            </h2>
            <div id="collapseReturns" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?php if(empty($returns)): ?>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returns as $return): ?>
                                        <tr>
                                            <td>#<?php echo $return['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($return['title']); ?></td>
                                            <td><?php echo htmlspecialchars($return['buyer_username']); ?></td>
                                            <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $return['status']=='approved'?'success':($return['status']=='pending'?'warning':'danger'); ?>">
                                                    <?php echo ucfirst($return['status']); ?>
                                                </span>
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

        <!-- Payouts Section -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayouts">
                    💰 Payouts (Bkash)
                </button>
            </h2>
            <div id="collapsePayouts" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <h5>Request Payout</h5>
                    <form method="POST" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Amount ($)</label>
                            <input type="number" name="amount" class="form-control" min="1" max="<?php echo floor($payoutLeft); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Bkash Number</label>
                            <input type="text" name="bkash_number" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="request_payout" class="btn btn-success w-100">Submit Payout Request</button>
                        </div>
                    </form>

                    <h5>Past Payouts</h5>
                    <?php if(empty($payouts)): ?>
                        <p>No payout history.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Amount ($)</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payouts as $p): ?>
                                        <tr>
                                            <td><?php echo number_format($p['amount'], 2); ?></td>
                                            <td><?php echo ucfirst($p['method']); ?></td>
                                            <td><?php echo ucfirst($p['status']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
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
