<?php
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Calculate total earnings (delivered orders)
try {
    $stmt = $pdo->prepare("
        SELECT SUM(p.price * 0.85) AS total_earning
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE p.seller_id = ? AND o.status = 'delivered'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $totalEarning = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle cashout request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $phone = $_POST['phone_number'];
    $method = $_POST['method'];

    if ($amount <= 0 || $amount > $totalEarning) {
        $error = "Invalid amount. Must be between 0 and your total earnings.";
    } elseif (empty($phone)) {
        $error = "Phone number is required.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO seller_payout_requests (seller_id, amount, method, phone_number)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $amount, $method, $phone]);
            $success = "Cashout request submitted successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="mb-4 text-center">Request Cashout</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <p><strong>Total Available Earnings:</strong> $<?php echo number_format($totalEarning, 2); ?></p>

    <form method="POST" class="row g-3 mt-3">
        <div class="col-md-6">
            <label for="amount" class="form-label">Amount to Withdraw</label>
            <input type="number" step="0.01" max="<?php echo $totalEarning; ?>" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="col-md-6">
            <label for="phone_number" class="form-label">Bkash Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
        </div>
        <div class="col-md-6">
            <label for="method" class="form-label">Payment Method</label>
            <select name="method" id="method" class="form-select">
                <option value="bkash">Bkash</option>
                <option value="rocket">Rocket</option>
                <option value="bank">Bank</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
    </form>
</div>
<?php require '../includes/footer.php'; ?>
