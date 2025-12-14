<?php
session_start();
require '../includes/config.php';

// Set JSON header for proper AJAX response
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID not found']);
    exit;
}

$productId = intval($_POST['product_id']);
$buyerId = $_SESSION['user_id'];

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Product ID not found']);
    exit;
}

try {
    // First, verify that the product exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Product ID not found']);
        exit;
    }

    // Check if already in wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE buyer_id = ? AND product_id = ?");
    $stmt->execute([$buyerId, $productId]);
    if ($stmt->fetch()) {
        // Remove from wishlist
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE buyer_id = ? AND product_id = ?");
        $stmt->execute([$buyerId, $productId]);
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
    } else {
        // Add to wishlist
        $stmt = $pdo->prepare("INSERT INTO wishlist (buyer_id, product_id) VALUES (?, ?)");
        $stmt->execute([$buyerId, $productId]);
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
    }
} catch (PDOException $e) {
    error_log("Database error in add_to_wishlist.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
