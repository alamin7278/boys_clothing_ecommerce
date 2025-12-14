<?php
session_start();
require 'includes/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log("Invalid product ID in product.php: " . ($_GET['id'] ?? 'not set'), 3, __DIR__ . "/errors.log");
    header("Location: search.php");
    exit;
}

$productId = intval($_GET['id']);
try {
    $stmt = $pdo->prepare("SELECT p.*, u.username, u.verified FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ? AND p.status IN ('approved', 'available')");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        error_log("Product not found or not available: ID $productId", 3, __DIR__ . "/errors.log");
        header("Location: search.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error fetching product: " . $e->getMessage(), 3, __DIR__ . "/errors.log");
    header("Location: search.php");
    exit;
}
?>

<?php require 'includes/header.php'; ?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-6">
            <?php 
            $images = json_decode($product['images'], true);
            $images = $images ?: ['Uploads/default.jpg'];
            ?>
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($images as $index => $image): ?>
                        <?php
                        // Use image path directly (stored as relative path like "Uploads/img_xxx.jpg")
                        $imagePath = !empty($image) ? htmlspecialchars($image) : 'Uploads/default.jpg';
                        ?>
                        <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $imagePath; ?>" class="d-block w-100" alt="Product Image" onerror="this.src='Uploads/default.jpg';">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['title']); ?></h2>
            <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
            <p>Condition: <?php echo ucwords(str_replace('_', ' ', $product['item_condition'])); ?></p>
            <p>Size: <?php echo htmlspecialchars($product['size']); ?></p>
            <p>Category: <?php echo ucwords(str_replace('_', ' ', $product['category'])); ?></p>
            <p>Seller: <?php echo htmlspecialchars($product['username']); ?>
                <?php if ($product['verified'] == 'approved'): ?>
                    <span class="bi bi-check-circle text-success" title="Verified Seller"></span>
                <?php endif; ?>
            </p>
            <?php if ($product['hygiene_verified'] == 'approved'): ?>
                <p>Hygiene: <span class="bi bi-droplet text-primary" title="Hygiene Verified"></span> Verified</p>
                <?php if ($product['laundry_memo']): ?>
                    <?php
                    // Use laundry memo path directly
                    $laundryMemo = !empty($product['laundry_memo']) ? $product['laundry_memo'] : null;
                    ?>
                    <p><a href="<?php echo htmlspecialchars($laundryMemo ?: '#'); ?>" target="_blank"><?php echo $laundryMemo ? 'View Laundry Memo' : 'No Laundry Memo'; ?></a></p>
                <?php endif; ?>
            <?php endif; ?>
            <p>Description: <?php echo htmlspecialchars($product['description'] ?: 'No description provided.'); ?></p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'buyer'): ?>
                <a href="checkout.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary mb-2">Buy Now (COD)</a>
                <button class="btn btn-outline-secondary wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE buyer_id = ? AND product_id = ?");
                    $stmt->execute([$_SESSION['user_id'], $product['id']]);
                    echo $stmt->fetch() ? 'Remove from Wishlist' : 'Add to Wishlist';
                    ?>
                </button>
            <?php else: ?>
                <p><a href="login.php">Log in</a> to buy this product.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
