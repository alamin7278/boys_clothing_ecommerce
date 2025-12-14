<?php
session_start();
require 'includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    error_log("Unauthorized access to checkout.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    error_log("Invalid product_id in checkout.php: " . ($_GET['product_id'] ?? 'not set'), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/search.php");
    exit;
}

$productId = intval($_GET['product_id']);
$buyerId = $_SESSION['user_id'];

try {
    // Fetch product details
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ? AND p.status IN ('approved', 'available')");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        error_log("Product not found or not available: ID $productId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/search.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error fetching product: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/search.php");
    exit;
}

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    $fullName = trim($_POST['full_name']);
    $addressLine = trim($_POST['address_line']);
    $city = trim($_POST['city']);
    $postalCode = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);
    
    if (empty($fullName) || empty($addressLine) || empty($city) || empty($postalCode) || empty($phone)) {
        $error = "All fields are required.";
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Save address
            error_log("Attempting to save address for buyer ID: $buyerId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $stmt = $pdo->prepare("INSERT INTO addresses (user_id, full_name, address_line, city, postal_code, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$buyerId, $fullName, $addressLine, $city, $postalCode, $phone]);
            $addressId = $pdo->lastInsertId();
            error_log("Address saved: ID $addressId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            
            // Create order
            error_log("Attempting to create order for product ID: $productId, buyer ID: $buyerId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, product_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$buyerId, $productId]);
            $orderId = $pdo->lastInsertId();
            error_log("Order created: ID $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            
            // Update product status
            error_log("Attempting to update product status to sold for product ID: $productId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $stmt = $pdo->prepare("UPDATE products SET status = 'sold' WHERE id = ?");
            $stmt->execute([$productId]);
            error_log("Product status updated to sold for product ID: $productId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            
            // Notify seller
            error_log("Attempting to notify seller ID: {$product['seller_id']} for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, type, product_id, is_read) VALUES (?, ?, 'order_placed', ?, 0)");
            $stmt->execute([$product['seller_id'], "New order placed for {$product['title']} (Order ID: $orderId)", $productId]);
            error_log("Notification sent to seller ID: {$product['seller_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            
            $pdo->commit();
            error_log("Order placed successfully: ID $orderId, product_id: $productId, buyer_id: $buyerId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/buyer/orders.php?success=Order placed successfully");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Database error placing order: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $error = "Failed to place order: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<?php require 'includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Checkout</h2>
    <div class="card">
        <div class="card-body">
            <h4>Order Details</h4>
            <p><strong>Product:</strong> <?php echo htmlspecialchars($product['title']); ?></p>
            <p><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>
            <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Payment Method:</strong> Cash on Delivery (COD)</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <h4>Shipping Address</h4>
            <form method="POST">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="address_line" class="form-label">Address Line</label>
                    <input type="text" name="address_line" id="address_line" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" name="city" id="city" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" required>
                </div>
                <button type="submit" name="confirm_order" class="btn btn-primary">Confirm Order</button>
            </form>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>