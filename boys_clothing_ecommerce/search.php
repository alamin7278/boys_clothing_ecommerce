<?php
session_start();
require 'includes/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$condition = isset($_GET['item_condition']) ? $_GET['item_condition'] : '';

try {
    $query = "SELECT p.*, u.username FROM products p JOIN users u ON p.seller_id = u.id WHERE p.status IN ('approved', 'available')";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (p.title LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($category)) {
        $query .= " AND p.category = ?";
        $params[] = $category;
    }
    
    if (!empty($condition)) {
        $query .= " AND p.item_condition = ?";
        $params[] = $condition;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error in search.php: " . $e->getMessage(), 3, __DIR__ . "/errors.log");
    $products = [];
}
?>

<?php require 'includes/header.php'; ?>

<div class="container my-5">
    <!-- Hero Section -->
    <div class="text-center mb-4">
        <h1 class="fw-bold">Search Second Hand Clothes</h1>
        <p class="text-muted">Find your desired style quickly and easily</p>
    </div>

    <!-- Search & Filters -->
    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search by title or description" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="category">
                    <option value="">All Categories</option>
                    <?php 
                    $categories = ['polo','casual_shirt','formal_shirt','tshirt','jeans','shorts','jacket','sweater','hoodie','trousers','shoes','hygiene'];
                    foreach ($categories as $cat) {
                        $selected = $category == $cat ? 'selected' : '';
                        echo "<option value='$cat' $selected>".ucwords(str_replace('_', ' ', $cat))."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" name="item_condition">
                    <option value="">All Conditions</option>
                    <?php 
                    $conditions = ['new','like_new','excellent','good','used','fair','worn'];
                    foreach ($conditions as $cond) {
                        $selected = $condition == $cond ? 'selected' : '';
                        echo "<option value='$cond' $selected>".ucwords(str_replace('_',' ',$cond))."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">No products found. Try adjusting your search criteria.</div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php
                $images = json_decode($product['images'], true);
                $firstImage = (!empty($images) && file_exists($images[0])) ? htmlspecialchars($images[0]) : 'uploads/default.jpg';
                $desc = strlen($product['description']) > 80 ? substr($product['description'],0,80).'...' : $product['description'];
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        <img src="<?php echo $firstImage; ?>" 
                             class="card-img-top img-fluid" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             style="height:300px; object-fit:contain; background-color:#f8f9fa; padding:10px;">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($desc); ?></p>
                            <p class="fw-bold mb-1">৳ <?php echo number_format($product['price'],2); ?></p>
                            <p class="small mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="small mb-1"><strong>Condition:</strong> <?php echo htmlspecialchars($product['item_condition']); ?></p>
                            <p class="small mb-2"><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>

                            <div class="mt-auto">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary w-100 mb-2">View Details</a>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role']==='buyer'): ?>
                                    <?php
                                    $wishlistStmt = $pdo->prepare("SELECT id FROM wishlist WHERE buyer_id = ? AND product_id = ?");
                                    $wishlistStmt->execute([$_SESSION['user_id'],$product['id']]);
                                    $inWishlist = $wishlistStmt->fetch();
                                    ?>
                                    <button class="btn btn-outline-<?php echo $inWishlist?'danger':'secondary'; ?> w-100 wishlist-btn">
                                        <?php echo $inWishlist?'❤️ Remove from Wishlist':'🤍 Add to Wishlist'; ?>
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
