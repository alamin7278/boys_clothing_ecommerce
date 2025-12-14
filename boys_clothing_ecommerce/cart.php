<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    error_log("Unauthorized access to cart.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Handle remove item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND buyer_id = ?");
        $stmt->execute([intval($_POST['remove_id']), $_SESSION['user_id']]);
        error_log("Cart item ID: {$_POST['remove_id']} removed by buyer ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        header("Location: /boys_clothing_ecommerce/cart.php");
        exit;
    } catch (PDOException $e) {
        error_log("Database error removing cart item: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $error = "Failed to remove item.";
    }
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND buyer_id = ?");
            $stmt->execute([$quantity, intval($_POST['cart_id']), $_SESSION['user_id']]);
            error_log("Cart item ID: {$_POST['cart_id']} quantity updated to $quantity by buyer ID: {$_SESSION['user_id']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/cart.php");
            exit;
        } catch (PDOException $e) {
            error_log("Database error updating cart quantity: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            $error = "Failed to update quantity.";
        }
    } else {
        $error = "Quantity must be greater than 0.";
    }
}

// Fetch cart items
try {
    $stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.images FROM cart c JOIN products p ON c.product_id = p.id WHERE c.buyer_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching cart items: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    $cartItems = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Your Cart</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (empty($cartItems)): ?>
        <p class="text-center">Your cart is empty.</p>
        <a href="/boys_clothing_ecommerce/search.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <div class="card p-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php
                        $images = json_decode($item['images'], true);
                        $firstImage = !empty($images) && file_exists("/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/{$images[0]}") 
                            ? "/boys_clothing_ecommerce/{$images[0]}" 
                            : '/boys_clothing_ecommerce/Uploads/default.jpg';
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control d-inline w-auto" style="width: 80px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="remove_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-end">
                <strong>Total: $<?php echo number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $cartItems)), 2); ?></strong>
                <a href="/boys_clothing_ecommerce/checkout.php" class="btn btn-success mt-3">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>