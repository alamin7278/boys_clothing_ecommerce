<?php
/*
session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id']) && isset($_POST['action'])) {
    $requestId = intval($_POST['request_id']);
    $action = $_POST['action'];

    if (!in_array($action, ['completed', 'rejected'])) {
        $error = "Invalid action.";
    } else {
        try {
            $processed_at = $action === 'completed' ? date('Y-m-d H:i:s') : null;
            $stmt = $pdo->prepare("UPDATE seller_payout_requests SET status=?, processed_at=? WHERE id=?");
            $stmt->execute([$action, $processed_at, $requestId]);
            $success = "Request updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch all payout requests
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.username 
        FROM seller_payout_requests r
        JOIN users u ON r.seller_id = u.id
        ORDER BY r.requested_at DESC
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $requests = [];
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-5">
    <h2 class="text-center mb-4">Seller Payout Requests</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Processed At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['username']); ?></td>
                        <td>$<?php echo number_format($r['amount'],2); ?></td>
                        <td><?php echo ucfirst($r['method']); ?></td>
                        <td><?php echo htmlspecialchars($r['phone_number']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $r['status']=='completed'?'success':($r['status']=='pending'?'warning':'danger'); ?>">
                                <?php echo ucfirst($r['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $r['requested_at']; ?></td>
                        <td><?php echo $r['processed_at'] ?? '-'; ?></td>
                        <td>
                            <?php if ($r['status']=='pending'): ?>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                                    <button type="submit" name="action" value="completed" class="btn btn-sm btn-success">Dispatch</button>
                                    <button type="submit" name="action" value="rejected" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require '../includes/footer.php'; ?>*/




session_start();
require '../includes/config.php';

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Handle dispatch or reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payout_id']) && isset($_POST['action'])) {
    $payoutId = intval($_POST['payout_id']);
    $action = $_POST['action'];

    if (!in_array($action, ['completed', 'rejected'])) {
        $error = "Invalid action.";
    } else {
        if ($action === 'completed') {
            $transaction_number = trim($_POST['transaction_number'] ?? '');
            if (empty($transaction_number)) {
                $error = "Transaction number is required to dispatch payout.";
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE payouts SET status=?, transaction_number=?, created_at=NOW() WHERE id=?");
                    $stmt->execute([$action, $transaction_number, $payoutId]);
                    $success = "Payout dispatched successfully.";
                    header("Refresh: 1"); // Refresh to show updated status
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        } else { // rejected
            try {
                $stmt = $pdo->prepare("UPDATE payouts SET status=? WHERE id=?");
                $stmt->execute([$action, $payoutId]);
                $success = "Payout request rejected.";
                header("Refresh: 1");
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch all payout requests
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM payouts p
        JOIN users u ON p.seller_id = u.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $payouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $payouts = [];
}
?>

<?php require '../includes/header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Seller Payout Requests</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Amount ($)</th>
                    <th>Status</th>
                    <th>Transaction Number</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($payouts as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['username']); ?></td>
                        <td><?php echo number_format($p['amount'],2); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $p['status']=='completed'?'success':($p['status']=='pending'?'warning':'danger'); ?>">
                                <?php echo ucfirst($p['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($p['transaction_number'] ?? '-'); ?></td>
                        <td><?php echo $p['created_at']; ?></td>
                        <td>
                            <?php if($p['status']=='pending'): ?>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="payout_id" value="<?php echo $p['id']; ?>">
                                    <input type="text" name="transaction_number" placeholder="Transaction #" class="form-control form-control-sm" required>
                                    <button type="submit" name="action" value="completed" class="btn btn-success btn-sm">Dispatch</button>
                                    <button type="submit" name="action" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>





