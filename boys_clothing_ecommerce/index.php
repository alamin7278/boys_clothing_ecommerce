<?php
session_start();
require 'includes/config.php';

// Fetch products (only approved/available ones)
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        WHERE p.status IN ('approved', 'available')
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error in index.php: " . $e->getMessage(), 3, __DIR__ . "/errors.log");
    $products = [];
}
?>

<?php require 'includes/header.php'; ?>

<div class="container my-5">
    <!-- Hero Section -->
    <div class="text-center mb-5">
        <h1 class="fw-bold">Second Hand Clothes Marketplace</h1>
        <p class="lead text-muted">Buy & sell stylish second-hand ’ clothes with trust & safety.</p>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">🚫 No products available right now. Check back later!</div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php
                // Get first image
                $images = json_decode($product['images'], true);
                $firstImage = (!empty($images) && file_exists($images[0])) 
                    ? htmlspecialchars($images[0]) 
                    : 'uploads/default.jpg';

                // Truncate description
                $desc = strlen($product['description']) > 80 
                    ? substr($product['description'], 0, 80) . "..." 
                    : $product['description'];
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo $firstImage; ?>" 
                             class="card-img-top img-fluid" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             style="height: 300px; object-fit: contain; background-color: #f8f9fa; padding: 10px;">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($desc); ?></p>
                            <p class="fw-bold mb-1">৳ <?php echo number_format($product['price'], 2); ?></p>
                            <p class="small mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="small mb-1"><strong>Condition:</strong> <?php echo htmlspecialchars($product['item_condition']); ?></p>
                            <p class="small"><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>

                            <div class="mt-auto">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary w-100 mb-2">View Details</a>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'buyer'): ?>
                                    <?php
                                    $wishlistStmt = $pdo->prepare("SELECT id FROM wishlist WHERE buyer_id = ? AND product_id = ?");
                                    $wishlistStmt->execute([$_SESSION['user_id'], $product['id']]);
                                    $inWishlist = $wishlistStmt->fetch();
                                    ?>
                                    <button class="btn btn-outline-<?php echo $inWishlist ? 'danger' : 'secondary'; ?> w-100 wishlist-btn" 
                                            data-product-id="<?php echo $product['id']; ?>">
                                        <?php echo $inWishlist ? '❤️ Remove from Wishlist' : '🤍 Add to Wishlist'; ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
