<?php/*
session_start();
require '../includes/config.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /boys_clothing_ecommerce/login.php");
    exit;
}

// Handle verification actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['seller_id'])) {
    $sellerId = $_POST['seller_id'];
    $action = $_POST['action'];
    $validActions = ['approve', 'reject'];

    if (!in_array($action, $validActions)) {
        $error = "Invalid action.";
    } else {
        try {
            $status = $action == 'approve' ? 'approved' : 'rejected';
            $stmt = $pdo->prepare("UPDATE users SET verified = ? WHERE id = ? AND role = 'seller'");
            $stmt->execute([$status, $sellerId]);
            $success = "Seller verification " . ($action == 'approve' ? 'approved' : 'rejected') . " successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch all sellers
try {
    $stmt = $pdo->prepare("SELECT id, username, email, nid, certificate, verified FROM users WHERE role = 'seller' ORDER BY created_at DESC");
    $stmt->execute();
    $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sellers = [];
}

// Count pending products
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE status = 'pending'");
    $stmt->execute();
    $pendingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    $pendingCount = 0;
}

// Fetch payout requests
try {
    $stmt = $pdo->prepare("
        SELECT p.id, u.username, p.amount, p.method, p.phone_number, p.status, p.created_at as requested_at, p.processed_at
        FROM payouts p
        JOIN users u ON p.seller_id = u.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $payoutRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    $payoutRequests = [];
}

// Filter for sales and commission charts
$filter = $_GET['filter'] ?? 'daily';
switch($filter){
    case 'weekly':
        $groupBy = "WEEK(o.created_at)";
        break;
    case 'monthly':
        $groupBy = "MONTH(o.created_at)";
        break;
    case 'yearly':
        $groupBy = "YEAR(o.created_at)";
        break;
    case 'daily':
    default:
        $groupBy = "DATE(o.created_at)";
}

// Sales trend
try {
    $stmt = $pdo->prepare("
        SELECT DATE(o.created_at) as sale_date, SUM(p.price) AS daily_total
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.status='delivered'
        GROUP BY $groupBy
        ORDER BY MIN(o.created_at)
    ");
    $stmt->execute();
    $salesTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Commission trend
    $stmt = $pdo->prepare("
        SELECT DATE(o.created_at) as sale_date, SUM(p.price*0.15) AS commission_total
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.status='delivered'
        GROUP BY $groupBy
        ORDER BY MIN(o.created_at)
    ");
    $stmt->execute();
    $commissionTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    $salesTrend = [];
    $commissionTrend = [];
}
?>

<?php require '../includes/header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Admin Dashboard</h2>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif(isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Sellers Section -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Manage Sellers</h4>
        </div>
        <div class="card-body">
            <?php if(empty($sellers)): ?>
                <p class="text-center text-muted">No sellers found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>NID</th>
                                <th>Certificate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sellers as $seller): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($seller['username']); ?></td>
                                    <td><?php echo htmlspecialchars($seller['email']); ?></td>
                                    <td>
                                        <?php if($seller['nid']): ?>
                                            <a href="/boys_clothing_ecommerce/<?php echo htmlspecialchars($seller['nid']); ?>" target="_blank">View</a>
                                        <?php else: ?>Not uploaded<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($seller['certificate']): ?>
                                            <a href="/boys_clothing_ecommerce/<?php echo htmlspecialchars($seller['certificate']); ?>" target="_blank">View</a>
                                        <?php else: ?>Not uploaded<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = 'text-secondary';
                                        if($seller['verified']=='pending') $statusClass='text-warning';
                                        elseif($seller['verified']=='approved') $statusClass='text-success';
                                        elseif($seller['verified']=='rejected') $statusClass='text-danger';
                                        ?>
                                        <span class="<?php echo $statusClass; ?>"><?php echo ucfirst($seller['verified']); ?></span>
                                    </td>
                                    <td>
                                        <?php if($seller['verified']=='pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        <?php else: ?>No actions<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Products Section -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Manage Products</h4>
        </div>
        <div class="card-body">
            <p>Review pending products: <strong><?php echo $pendingCount; ?></strong></p>
            <a href="/boys_clothing_ecommerce/admin/approve_products.php" class="btn btn-primary">Go to Product Approval</a>
        </div>
    </div>

    <!-- Payout Requests Section -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Seller Payout Requests</h4>
        </div>
        <div class="card-body">
            <?php if(empty($payoutRequests)): ?>
                <p>No payout requests found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Processed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($payoutRequests as $r): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['username']); ?></td>
                                    <td>$<?php echo number_format($r['amount'],2); ?></td>
                                    <td><?php echo ucfirst($r['status']); ?></td>
                                    <td><?php echo $r['requested_at']; ?></td>
                                    <td><?php echo $r['processed_at'] ?? '-'; ?></td>
                                    <td>
                                        <?php if($r['status']=='pending'): ?>
                                            <form method="POST" action="payouts.php" class="d-flex gap-1">
                                                <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                                                <button type="submit" name="action" value="completed" class="btn btn-success btn-sm">Dispatch</button>
                                                <button type="submit" name="action" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        <?php else: ?>-<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mb-4 text-center">
        <a href="?filter=daily" class="btn btn-sm btn-outline-primary <?php echo $filter=='daily'?'active':'' ?>">Daily</a>
        <a href="?filter=weekly" class="btn btn-sm btn-outline-primary <?php echo $filter=='weekly'?'active':'' ?>">Weekly</a>
        <a href="?filter=monthly" class="btn btn-sm btn-outline-primary <?php echo $filter=='monthly'?'active':'' ?>">Monthly</a>
        <a href="?filter=yearly" class="btn btn-sm btn-outline-primary <?php echo $filter=='yearly'?'active':'' ?>">Yearly</a>
    </div>

    <!-- Sales Trend Chart -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Sales Trend</h4>
        </div>
        <div class="card-body">
            <canvas id="salesTrendChart" height="100"></canvas>
        </div>
    </div>

    <!-- Commission Trend Chart -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Commission Earned Trend</h4>
        </div>
        <div class="card-body">
            <canvas id="commissionTrendChart" height="100"></canvas>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
const salesTrendChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: [<?php foreach($salesTrend as $s){ echo "'".$s['sale_date']."',"; } ?>],
        datasets: [{
            label: 'Sales ($)',
            data: [<?php foreach($salesTrend as $s){ echo $s['daily_total'].','; } ?>],
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            tension: 0.1
        }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});

const commissionCtx = document.getElementById('commissionTrendChart').getContext('2d');
const commissionTrendChart = new Chart(commissionCtx, {
    type: 'line',
    data: {
        labels: [<?php foreach($commissionTrend as $c){ echo "'".$c['sale_date']."',"; } ?>],
        datasets: [{
            label: 'Commission ($)',
            data: [<?php foreach($commissionTrend as $c){ echo $c['commission_total'].','; } ?>],
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true,
            tension: 0.1
        }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});
</script>*/
