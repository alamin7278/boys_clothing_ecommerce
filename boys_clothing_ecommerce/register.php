<?php
/*
session_start();
require 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Server-side password validation
    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 6 characters, include one uppercase letter and one number.";
    } else {
        // Check if username or email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or email already taken.";
        } else {
            try {
                $verified_status = ($role == 'seller') ? 'pending' : 'approved';
                $stmt = $pdo->prepare("INSERT INTO users (role, username, password, email, verified) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$role, $username, $password, $email, $verified_status]);
                $success = $role == 'seller' ? "Registration successful! Awaiting admin approval." : "Registration successful! You can now log in.";
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
                error_log("Registration error: " . $e->getMessage(), 3, "errors.log");
            }
        }
    }
}
?>

<?php require 'includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Register</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <form id="registerForm" method="POST">
                    <div class="mb-3">
                        <label for="role" class="form-label">Register as</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="buyer">Buyer</option>
                            <option value="seller">Seller</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">Password must be at least 6 characters, include one uppercase letter and one number.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>*/


session_start();
require 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Server-side password validation
    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 6 characters, include one uppercase letter and one number.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or email already taken.";
        } else {
            try {
                $verified_status = ($role == 'seller') ? 'pending' : 'approved';
                $stmt = $pdo->prepare("INSERT INTO users (role, username, password, email, verified) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$role, $username, $password, $email, $verified_status]);
                $success = $role == 'seller' ? "Registration successful! Awaiting admin approval." : "Registration successful! You can now log in.";
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
                error_log("Registration error: " . $e->getMessage(), 3, "errors.log");
            }
        }
    }
}
?>

<?php require 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm rounded-4 p-4">
                <h2 class="text-center mb-4 fw-bold">Register</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form id="registerForm" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Register as</label>
                        <select class="form-control form-control-lg" id="role" name="role" required>
                            <option value="buyer">Buyer</option>
                            <option value="seller">Seller</option>
                        </select>
                        <div class="invalid-feedback">Please select a role.</div>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control form-control-lg" id="username" name="username" required>
                        <div class="invalid-feedback">Please enter a username.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        <small class="form-text text-muted">At least 6 characters, one uppercase letter, and one number.</small>
                        <div class="invalid-feedback">Please provide a valid password.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">Register</button>
                </form>

                <p class="mt-3 text-center text-muted">Already have an account? 
                    <a href="login.php" class="text-decoration-none fw-medium">Login</a>
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
