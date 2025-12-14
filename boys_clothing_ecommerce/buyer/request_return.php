<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    error_log("Unauthorized access to request_return - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['reason'])) {
    $orderId = intval($_POST['order_id']);
    $reason = trim($_POST['reason']);
    $buyerId = $_SESSION['user_id'];

    if (empty($reason)) {
        error_log("Return request failed - Empty reason for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/buyer/dashboard.php?error=Return%20reason%20is%20required");
        exit;
    }

    try {
        // Verify that the order belongs to the buyer and is delivered
        $stmt = $pdo->prepare("
            SELECT o.id 
            FROM orders o 
            WHERE o.id = ? AND o.buyer_id = ? AND o.status = 'delivered'
        ");
        $stmt->execute([$orderId, $buyerId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            error_log("Return request failed - Order ID: $orderId not found, not delivered, or does not belong to buyer ID: $buyerId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/buyer/dashboard.php?error=Order%20not%20found%20or%20not%20eligible%20for%20return");
            exit;
        }

        // Check if a return request already exists
        $stmt = $pdo->prepare("SELECT id FROM returns WHERE order_id = ?");
        $stmt->execute([$orderId]);
        if ($stmt->fetch()) {
            error_log("Return request failed - Return already exists for order ID: $orderId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/buyer/dashboard.php?error=Return%20request%20already%20submitted");
            exit;
        }

        // Insert return request
        $stmt = $pdo->prepare("
            INSERT INTO returns (order_id, reason, status, admin_decision, created_at)
            VALUES (?, ?, 'pending', 'pending', NOW())
        ");
        $stmt->execute([$orderId, $reason]);

        // Notify seller about the return request
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message, type, product_id, created_at)
            SELECT p.seller_id, CONCAT('Return requested for order #', o.id, ' - Product: ', p.title), 'order_placed', o.product_id, NOW()
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);

        error_log("Return request submitted for order ID: $orderId by buyer ID: $buyerId", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/buyer/dashboard.php?success=Return%20request%20submitted%20successfully");
        exit;

    } catch (PDOException $e) {
        error_log("Database error submitting return request: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/buyer/dashboard.php?error=Database%20error:%20" . urlencode($e->getMessage()));
        exit;
    }
} else {
    error_log("Invalid request to request_return - POST data: " . print_r($_POST, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/buyer/dashboard.php?error=Invalid%20request");
    exit;
}
?>