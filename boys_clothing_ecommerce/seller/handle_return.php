<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    error_log("Unauthorized access to handle_return - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_id']) && isset($_POST['action']) && isset($_POST['order_id'])) {
    $returnId = intval($_POST['return_id']);
    $action = $_POST['action'];
    $orderId = intval($_POST['order_id']);
    $validActions = ['approve', 'reject'];

    if (!in_array($action, $validActions)) {
        error_log("Invalid action provided: $action for return ID: $returnId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Invalid%20action");
        exit;
    }

    try {
        // Verify that the return request belongs to the seller's product
        $stmt = $pdo->prepare("
            SELECT r.id 
            FROM returns r
            JOIN orders o ON r.order_id = o.id
            JOIN products p ON o.product_id = p.id
            WHERE r.id = ? AND p.seller_id = ? AND r.status = 'pending'
        ");
        $stmt->execute([$returnId, $_SESSION['user_id']]);
        $return = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$return) {
            error_log("Return ID: $returnId not found, not pending, or does not belong to seller ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Return%20request%20not%20found%20or%20unauthorized");
            exit;
        }

        // Update return request status
        $newStatus = $action == 'approve' ? 'approved' : 'rejected';
        $stmt = $pdo->prepare("UPDATE returns SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $returnId]);
        error_log("Return ID: $returnId updated to status: $newStatus by seller ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

        // If approved, update product status to 'available' (optional, assuming return makes product available again)
        if ($action == 'approve') {
            $stmt = $pdo->prepare("UPDATE products p SET status = 'available' WHERE p.id = (SELECT product_id FROM orders WHERE id = ?)");
            $stmt->execute([$orderId]);
            error_log("Product status updated to 'available' for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }

        // Notify buyer about the return decision
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message, type, product_id, created_at)
            SELECT o.buyer_id, CONCAT('Your return request for order #', o.id, ' - Product: ', p.title, ' has been ', ?), 'order_placed', o.product_id, NOW()
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.id = ?
        ");
        $stmt->execute([$newStatus, $orderId]);
        error_log("Notification created for buyer for return ID: $returnId, order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

        header("Location: /boys_clothing_ecommerce/seller/dashboard.php?success=Return%20request%20" . $newStatus . "%20successfully");
        exit;

    } catch (PDOException $e) {
        error_log("Database error handling return request: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Database%20error:%20" . urlencode($e->getMessage()));
        exit;
    }
} else {
    error_log("Invalid request to handle_return - POST data: " . print_r($_POST, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/seller/dashboard.php?error=Invalid%20request");
    exit;
}
?>