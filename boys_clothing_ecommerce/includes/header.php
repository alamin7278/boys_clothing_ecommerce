<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
//error_log("Header - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Second Hand Clothes Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/boys_clothing_ecommerce/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Gradient Navbar */
        .navbar-custom {
            background: linear-gradient(90deg, #007bff, #00c6ff);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: 1px;
            color: #fff !important;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: 8px;
            position: relative;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffc107 !important;
        }

        .navbar-nav .nav-link .badge-trust {
            font-size: 0.8rem;
            margin-left: 4px;
            vertical-align: middle;
            box-shadow: 0 0 5px #0dcaf0;
            border-radius: 50%;
        }

        .navbar-nav .btn-login,
        .navbar-nav .btn-register {
            border-radius: 25px;
            margin-left: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar-nav .btn-login:hover {
            background-color: #ffc107;
            color: #000 !important;
        }

        .navbar-nav .btn-register:hover {
            background-color: #fff;
            color: #007bff !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
        <div class="container-fluid">
            <?php
            $logoUrl = '/boys_clothing_ecommerce/index.php';
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {
                if ($_SESSION['role'] == 'seller')
                    $logoUrl = '/boys_clothing_ecommerce/seller/dashboard.php';
                elseif ($_SESSION['role'] == 'admin')
                    $logoUrl = '/boys_clothing_ecommerce/admin/dashboard.php';
            }
            ?>
            <a class="navbar-brand" href="<?php echo htmlspecialchars($logoUrl); ?>">Second Hand Clothes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <?php if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])): ?>
                        <?php if ($_SESSION['role'] == 'buyer'): ?>
                            <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/search.php">Search</a></li>
                            <li class="nav-item"><a class="nav-link"
                                    href="/boys_clothing_ecommerce/buyer/dashboard.php">Dashboard</a></li>
                            <li class="nav-item">
                                <a class="nav-link" href="/boys_clothing_ecommerce/buyer/wishlist.php">
                                    <i class="bi bi-heart"></i> Wishlist
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/logout.php">Logout</a></li>
                        <?php elseif ($_SESSION['role'] == 'seller'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/boys_clothing_ecommerce/seller/dashboard.php">
                                    Dashboard
                                    <?php if (!empty($_SESSION['verified']) && $_SESSION['verified'] === 'approved'): ?>
                                        <i class="bi bi-patch-check-fill text-info badge-trust" title="Verified Seller"></i>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/logout.php">Logout</a></li>
                        <?php elseif ($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/admin/dashboard.php">Admin
                                    Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link"
                                    href="/boys_clothing_ecommerce/admin/approve_products.php">Approve Products</a></li>
                            <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/logout.php">Logout</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="/boys_clothing_ecommerce/search.php">Search</a></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-login"
                                href="/boys_clothing_ecommerce/login.php">Login</a></li>
                        <li class="nav-item"><a class="btn btn-warning btn-register"
                                href="/boys_clothing_ecommerce/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
