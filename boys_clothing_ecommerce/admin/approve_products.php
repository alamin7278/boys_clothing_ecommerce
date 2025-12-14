<?php
/*
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    error_log("Unauthorized access to approve_products.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/index.php");
    exit;
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    $action = $_POST['action'];
    try {
        $stmt = $pdo->prepare("SELECT category, laundry_memo, seller_id, title FROM products WHERE id = ? AND status = 'pending'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $isHygiene = $product['category'] == 'hygiene';
            if ($action == 'approve') {
                $status = 'approved';
                $hygieneVerified = $isHygiene ? 'approved' : 'approved';
                $notificationType = 'product_approved';
                $message = "Your product '" . htmlspecialchars($product['title']) . "' has been approved.";
            } elseif ($action == 'reject') {
                $status = 'inactive';
                $hygieneVerified = $isHygiene ? 'rejected' : 'rejected';
                $notificationType = 'product_rejected';
                $message = "Your product '" . htmlspecialchars($product['title']) . "' has been rejected.";
            } else {
                error_log("Invalid action: $action for product ID: $productId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                $errors[] = "Invalid action.";
            }

            if (!isset($errors)) {
                // Update product status
                $stmt = $pdo->prepare("UPDATE products SET status = ?, hygiene_verified = ? WHERE id = ?");
                $stmt->execute([$status, $hygieneVerified, $productId]);

                // Insert notification
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, type, product_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$product['seller_id'], $message, $notificationType, $productId]);

                error_log("Product ID: $productId $action by admin ID: {$_SESSION['user_id']}, Status: $status, Hygiene Verified: $hygieneVerified, Notification sent to seller ID: {$product['seller_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                $success = "Product $action successfully.";
            }
        } else {
            $errors[] = "Product not found or not pending.";
            error_log("Product not found or not pending for ID: $productId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
        error_log("Database error in approve_products.php: " . $e->getMessage() . ", Product ID: $productId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }
}

// Fetch pending products
try {
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM products p JOIN users u ON p.seller_id = u.id WHERE p.status = 'pending'");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching pending products: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $products = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Approve Products</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (empty($products)): ?>
        <p class="text-center">No pending products.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($products as $product): ?>
                <?php
                $images = json_decode($product['images'], true);
                $firstImage = !empty($images) && file_exists("/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/{$images[0]}") 
                    ? "/boys_clothing_ecommerce/{$images[0]}" 
                    : '/boys_clothing_ecommerce/Uploads/default.jpg';
                $laundryMemo = !empty($product['laundry_memo']) && file_exists("/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/{$product['laundry_memo']}") 
                    ? "/boys_clothing_ecommerce/{$product['laundry_memo']}" 
                    : null;
                ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?php echo htmlspecialchars($firstImage); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="card-text"><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="card-text"><strong>Condition:</strong> <?php echo htmlspecialchars($product['item_condition']); ?></p>
                            <p class="card-text"><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <?php if ($laundryMemo): ?>
                                <p class="card-text"><strong>Laundry Memo:</strong> <a href="<?php echo htmlspecialchars($laundryMemo); ?>" target="_blank">View Memo</a></p>
                            <?php else: ?>
                                <p class="card-text"><strong>Laundry Memo:</strong> None</p>
                            <?php endif; ?>
                            <form method="POST" class="mt-2">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success me-2">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>*/


session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /boys_clothing_ecommerce/index.php");
    exit;
}

// Handle approval/rejection with confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    $action = $_POST['action'];

    $stmt = $pdo->prepare("SELECT category, seller_id, title FROM products WHERE id = ? AND status = 'pending'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $status = $action == 'approve' ? 'approved' : 'inactive';
        $hygieneVerified = ($product['category'] == 'hygiene') ? $status : 'approved';
        $notificationType = $action == 'approve' ? 'product_approved' : 'product_rejected';
        $message = "Your product '" . htmlspecialchars($product['title']) . "' has been " . ($action == 'approve' ? 'approved' : 'rejected') . ".";

        $stmt = $pdo->prepare("UPDATE products SET status = ?, hygiene_verified = ? WHERE id = ?");
        $stmt->execute([$status, $hygieneVerified, $productId]);

        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, type, product_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product['seller_id'], $message, $notificationType, $productId]);

        $success = "Product " . ($action == 'approve' ? 'approved' : 'rejected') . " successfully.";
    } else {
        $errors[] = "Product not found or not pending.";
    }
}

// Fetch pending products
$stmt = $pdo->prepare("SELECT p.*, u.username FROM products p JOIN users u ON p.seller_id = u.id WHERE p.status = 'pending' ORDER BY p.created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require '../includes/header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Approve Products</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <p class="text-center">No pending products.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <?php
                // Get first image - handle paths correctly for subdirectory
                $images = json_decode($product['images'], true);
                if (!empty($images) && is_array($images) && !empty($images[0])) {
                    $imagePath = htmlspecialchars($images[0]);
                    // Convert relative path to absolute if needed
                    if (strpos($imagePath, '/boys_clothing_ecommerce/') === false && strpos($imagePath, '/') !== 0) {
                        // Path is relative, make it absolute
                        $imagePath = '/boys_clothing_ecommerce/' . $imagePath;
                    }
                    $firstImage = $imagePath;
                } else {
                    $firstImage = '/boys_clothing_ecommerce/Uploads/default.jpg';
                }
                
                // Handle laundry memo path
                $laundryMemo = null;
                if (!empty($product['laundry_memo'])) {
                    $memoPath = htmlspecialchars($product['laundry_memo']);
                    if (strpos($memoPath, '/boys_clothing_ecommerce/') === false && strpos($memoPath, '/') !== 0) {
                        $memoPath = '/boys_clothing_ecommerce/' . $memoPath;
                    }
                    $laundryMemo = $memoPath;
                }
                
                $badgeClass = $product['category'] == 'hygiene' ? 'bg-info text-dark' : 'bg-secondary';
                ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <img src="<?php echo htmlspecialchars($firstImage); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             style="height: 200px; object-fit: cover; background-color: #f8f9fa;"
                             onerror="this.src='/boys_clothing_ecommerce/Uploads/default.jpg';">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" title="<?php echo htmlspecialchars($product['title']); ?>"><?php echo strlen($product['title']) > 25 ? substr($product['title'],0,25) . '...' : htmlspecialchars($product['title']); ?></h5>
                            <p class="mb-1"><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>
                            <p class="mb-1"><strong>Category:</strong> <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($product['category']); ?></span></p>
                            <p class="mb-1"><strong>Condition:</strong> <?php echo htmlspecialchars($product['item_condition']); ?></p>
                            <p class="mb-1"><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></p>
                            <p class="mb-1"><strong>Price:</strong> $<?php echo number_format($product['price'],2); ?></p>
                            <p class="mb-1" title="<?php echo htmlspecialchars($product['description']); ?>"><strong>Description:</strong> <?php echo strlen($product['description']) > 60 ? substr($product['description'],0,60) . '...' : htmlspecialchars($product['description']); ?></p>
                            <?php if ($laundryMemo): ?>
                                <p class="mb-2"><strong>Laundry Memo:</strong> <a href="<?php echo htmlspecialchars($laundryMemo); ?>" target="_blank" class="link-primary">View Memo</a></p>
                            <?php endif; ?>
                            <div class="mt-auto d-flex gap-2">
                                <button class="btn btn-success btn-sm flex-fill" onclick="confirmAction('approve', <?php echo $product['id']; ?>)">Approve</button>
                                <button class="btn btn-danger btn-sm flex-fill" onclick="confirmAction('reject', <?php echo $product['id']; ?>)">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmAction(action, productId) {
    if (confirm(`Are you sure you want to ${action} this product?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        form.innerHTML = `<input type="hidden" name="product_id" value="${productId}">
                          <input type="hidden" name="action" value="${action}">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.hover-shadow:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease-in-out;
}
</style>

<?php require '../includes/footer.php'; ?>
