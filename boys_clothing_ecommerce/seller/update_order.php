<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    error_log("Unauthorized access to update_order - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = $_POST['status'];
    error_log("Received POST data - Order ID: $orderId, Status: $newStatus", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

    // Validate status
    $validStatuses = ['pending', 'shipped', 'delivered'];
    if (!in_array($newStatus, $validStatuses)) {
        error_log("Invalid order status provided: $newStatus for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Invalid%20order%20status");
        exit;
    }

    try {
        // Verify order belongs to seller
        $stmt = $pdo->prepare("
            SELECT o.id, o.product_id 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.id = ? AND p.seller_id = ?
        ");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            error_log("Order ID: $orderId not found or does not belong to seller ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Order%20not%20found%20or%20unauthorized");
            exit;
        }

        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        error_log("Order ID: $orderId updated to status: $newStatus", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

        // If status is 'delivered', update product status to 'sold'
        if ($newStatus == 'delivered') {
            $stmt = $pdo->prepare("UPDATE products SET status = 'sold' WHERE id = ?");
            $stmt->execute([$order['product_id']]);
            error_log("Product ID: {$order['product_id']} status updated to 'sold' for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }

        // Create notification for buyer
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message, type, product_id, created_at)
            SELECT o.buyer_id, CONCAT('Your order for product ', p.title, ' has been updated to ', ?), 'order_placed', o.product_id, NOW()
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.id = ?
        ");
        $stmt->execute([$newStatus, $orderId]);
        error_log("Notification created for buyer for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

        header("Location: /boys_clothing_ecommerce/seller/dashboard.php?success=Order%20status%20updated%20successfully");
        exit;

    } catch (PDOException $e) {
        error_log("Database error updating order status: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Database%20error:%20" . urlencode($e->getMessage()));
        exit;
    }
} else {
    error_log("Invalid request to update_order - POST data: " . print_r($_POST, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Invalid%20request");
    exit;
}
?>