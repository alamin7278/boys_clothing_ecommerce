<?php
/*
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT p.*, u.username, u.verified FROM wishlist w JOIN products p ON w.product_id = p.id JOIN users u ON p.seller_id = u.id WHERE w.buyer_id = ? AND p.status = 'available'");
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Wishlist error: " . $e->getMessage(), 3, "errors.log");
    $wishlist = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">My Wishlist</h2>
    <?php if (empty($wishlist)): ?>
        <p class="text-center">Your wishlist is empty.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($wishlist as $product): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <?php $images = json_decode($product['images'], true); ?>
                        <img src="<?php echo $images ? htmlspecialchars($images[0]) : '/boys_clothing_ecommerce/images/placeholder.jpg'; ?>" class="card-img-top" alt="Product">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="card-text">
                                Price: $<?php echo number_format($product['price'], 2); ?><br>
                                Condition: <?php echo ucwords(str_replace('_', ' ', $product['item_condition'])); ?><br>
                                Seller: <?php echo htmlspecialchars($product['username']); ?>
                                <?php if ($product['verified'] == 'approved'): ?>
                                    <span class="bi bi-check-circle text-success" title="Verified Seller"></span>
                                <?php endif; ?>
                                <?php if ($product['hygiene_verified'] == 'approved'): ?>
                                    <span class="bi bi-droplet text-primary" title="Hygiene Verified"></span>
                                <?php endif; ?>
                            </p>
                            <a href="/boys_clothing_ecommerce/product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            <button class="btn btn-outline-secondary wishlist-btn" data-product-id="<?php echo $product['id']; ?>">Remove from Wishlist</button>
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

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    error_log("Unauthorized access to wishlist.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, __DIR__ . "/../errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

$buyerId = $_SESSION['user_id'];

// Handle remove from wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    $productId = intval($_POST['remove_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE buyer_id = ? AND product_id = ?");
        $stmt->execute([$buyerId, $productId]);
        error_log("Removed product ID: $productId from wishlist for buyer ID: $buyerId", 3, __DIR__ . "/../errors.log");
        header("Location: /boys_clothing_ecommerce/buyer/wishlist.php");
        exit;
    } catch (PDOException $e) {
        error_log("Database error removing from wishlist: " . $e->getMessage(), 3, __DIR__ . "/../errors.log");
        $error = "Failed to remove from wishlist.";
    }
}

try {
    $stmt = $pdo->prepare("SELECT w.*, p.title, p.description, p.price, p.images, p.status FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.buyer_id = ? AND p.status IN ('approved', 'available') ORDER BY w.created_at DESC");
    $stmt->execute([$buyerId]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching wishlist: " . $e->getMessage(), 3, __DIR__ . "/../errors.log");
    $wishlistItems = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Your Wishlist</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (empty($wishlistItems)): ?>
        <p class="text-center">Your wishlist is empty.</p>
        <a href="/boys_clothing_ecommerce/search.php" class="btn btn-primary d-block mx-auto">Browse Products</a>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($wishlistItems as $item): ?>
                <?php
                // Get first image - handle paths for subdirectory
                $images = json_decode($item['images'], true);
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
                ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo $firstImage; ?>" 
                             class="card-img-top img-fluid" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>"
                             style="height: 300px; object-fit: contain; background-color: #f8f9fa; padding: 10px;"
                             onerror="this.src='/boys_clothing_ecommerce/Uploads/default.jpg';">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                            <a href="/boys_clothing_ecommerce/product.php?id=<?php echo $item['product_id']; ?>" class="btn btn-primary">View Details</a>
                            <form method="POST" class="d-inline mt-2">
                                <input type="hidden" name="remove_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>
