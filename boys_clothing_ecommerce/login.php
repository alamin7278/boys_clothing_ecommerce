<?php
/*
session_start();
require 'includes/config.php';

// Debug: Log session state before login
error_log("Before login - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) { // Replace with password_verify() in production
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['verified'] = $user['verified']; // For sellers
            error_log("Login successful - User ID: {$user['id']}, Role: {$user['role']}, Verified: {$user['verified']}, Session ID: " . session_id(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            if ($user['role'] == 'seller') {
                header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            } elseif ($user['role'] == 'admin') {
                header("Location: /boys_clothing_ecommerce/admin/dashboard.php");
            } else {
                header("Location: /boys_clothing_ecommerce/index.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
            error_log("Login failed - Email: $email", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        error_log("Login DB error: " . $e->getMessage(), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }
}
?>

<?php require 'includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Login</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="mt-3 text-center">Don't have an account? <a href="/boys_clothing_ecommerce/register.php">Register</a></p>
            </div>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>*/

session_start();
require 'includes/config.php';

// Debug: Log session state before login
error_log("Before login - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, __DIR__ . "/errors.log");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) { // Use password_verify() in production
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['verified'] = $user['verified']; // For sellers

            error_log("Login successful - User ID: {$user['id']}, Role: {$user['role']}, Verified: {$user['verified']}, Session ID: " . session_id(), 3, __DIR__ . "/errors.log");

            // Redirect based on role
            if ($user['role'] == 'seller') {
                header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            } elseif ($user['role'] == 'admin') {
                header("Location: /boys_clothing_ecommerce/admin/dashboard.php");
            } else {
                header("Location: /boys_clothing_ecommerce/index.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
            error_log("Login failed - Email: $email", 3, __DIR__ . "/errors.log");
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        error_log("Login DB error: " . $e->getMessage(), 3, __DIR__ . "/errors.log");
    }
}
?>

<?php require 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm rounded-4 p-4">
                <h2 class="text-center mb-4 fw-bold">Login</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        <div class="invalid-feedback">Password cannot be empty.</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                </form>
                <p class="mt-3 text-center text-muted">Don't have an account? 
                    <a href="/boys_clothing_ecommerce/register.php" class="text-decoration-none fw-medium">Register</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap form validation
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) event.preventDefault(), event.stopPropagation();
            form.classList.add('was-validated');
        }, false)
    })
})();
</script>

<?php require 'includes/footer.php'; ?>
