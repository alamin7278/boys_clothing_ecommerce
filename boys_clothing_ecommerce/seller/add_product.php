<?php

session_start();
require '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller' || $_SESSION['verified'] != 'approved') {
    error_log("Unauthorized access to add_product.php - Session ID: " . session_id() . ", Session: " . print_r($_SESSION, true), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $size = $_POST['size'];
    $item_condition = $_POST['item_condition'];
    $price = floatval($_POST['price']);
    $uploadDir = '../Uploads/';
    $absoluteDir = '/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/Uploads/';
    $usedDir = $uploadDir;
    $errors = [];
    $imagePaths = [];
    $laundryMemoPath = null;

    // Validate inputs
    if (empty($title) || empty($description) || empty($category) || empty($size) || empty($item_condition) || $price <= 0) {
        $errors[] = "All fields are required, and price must be greater than 0.";
    }

    // Validate category
    $validCategories = ['polo', 'casual_shirt', 'formal_shirt', 'tshirt', 'jeans', 'shorts', 'jacket', 'sweater', 'hoodie', 'trousers', 'shoes', 'hygiene'];
    if (!in_array($category, $validCategories)) {
        $errors[] = "Invalid category selected: $category";
        error_log("Invalid category: $category", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }

    // Validate condition
    $validConditions = ['new', 'like_new', 'excellent', 'good', 'used', 'fair', 'worn'];
    if (!in_array($item_condition, $validConditions)) {
        $errors[] = "Invalid condition selected: $item_condition";
        error_log("Invalid condition: $item_condition", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }

    // Check directory
    if (!is_dir($usedDir)) {
        if (!mkdir($usedDir, 0777, true)) {
            $errors[] = "Failed to create Uploads directory.";
            error_log("Failed to create directory: $usedDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        } else {
            error_log("Created directory: $usedDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    }
    if (!is_writable($usedDir)) {
        error_log("Directory not writable: $usedDir, Owner: " . posix_getpwuid(fileowner($usedDir))['name'] . ", Permissions: " . substr(sprintf('%o', fileperms($usedDir)), -4), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        $usedDir = $absoluteDir;
        if (!is_dir($absoluteDir)) {
            if (!mkdir($absoluteDir, 0777, true)) {
                $errors[] = "Failed to create absolute Uploads directory.";
                error_log("Failed to create absolute directory: $absoluteDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            } else {
                error_log("Created absolute directory: $absoluteDir", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            }
        }
        if (!is_writable($absoluteDir)) {
            $errors[] = "Uploads directory is not writable. Contact administrator.";
            error_log("Absolute directory not writable: $absoluteDir, Owner: " . posix_getpwuid(fileowner($absoluteDir))['name'] . ", Permissions: " . substr(sprintf('%o', fileperms($absoluteDir)), -4), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    }

    // Validate images
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if (empty($_FILES['images']['name'][0])) {
        $errors[] = "At least one image is required.";
    } else {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $fileType = $_FILES['images']['type'][$key];
                $fileSize = $_FILES['images']['size'][$key];
                $fileTmp = $_FILES['images']['tmp_name'][$key];
                // Sanitize filename
                $fileName = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($name));
                // Verify MIME type with fileinfo
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $actualMimeType = finfo_file($finfo, $fileTmp);
                finfo_close($finfo);

                error_log("Image: $name, Reported MIME: $fileType, Actual MIME: $actualMimeType, Size: $fileSize, Temp: $fileTmp", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

                if (!in_array($actualMimeType, $allowedTypes)) {
                    $errors[] = "Image '$name' must be JPG or PNG (detected: $actualMimeType).";
                    error_log("Invalid MIME type for $name: $actualMimeType", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                } elseif ($fileSize > $maxFileSize) {
                    $errors[] = "Image '$name' exceeds 5MB.";
                    error_log("File too large: $name, Size: $fileSize", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                } elseif (!is_uploaded_file($fileTmp)) {
                    $errors[] = "Image '$name' is not a valid uploaded file.";
                    error_log("Not a valid uploaded file: $name, Temp: $fileTmp", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                } else {
                    $uniqueFileName = 'Uploads/' . uniqid('img_') . '_' . $fileName;
                    $destination = $usedDir . basename($uniqueFileName);
                    if (!move_uploaded_file($fileTmp, $destination)) {
                        $errors[] = "Failed to upload image: $name";
                        error_log("Failed to move image: $name from $fileTmp to $destination, Directory writable: " . (is_writable($usedDir) ? 'Yes' : 'No'), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                    } else {
                        $imagePaths[] = $uniqueFileName;
                        error_log("Successfully uploaded image: $name to $destination", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
                    }
                }
            } else {
                $errorCodes = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini upload_max_filesize',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
                    UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
                    UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
                ];
                $errorCode = $_FILES['images']['error'][$key];
                $errors[] = "Upload error for image '$name': " . ($errorCodes[$errorCode] ?? 'Unknown error');
                error_log("Image upload error for $name: Code $errorCode - " . ($errorCodes[$errorCode] ?? 'Unknown error'), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            }
        }
    }

    // Validate laundry memo (optional, image)
    if (!empty($_FILES['laundry_memo']['name']) && $_FILES['laundry_memo']['error'] === UPLOAD_ERR_OK) {
        $memoFile = $_FILES['laundry_memo'];
        $memoFileName = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($memoFile['name']));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $actualMemoMimeType = finfo_file($finfo, $memoFile['tmp_name']);
        finfo_close($finfo);

        error_log("Laundry memo: {$memoFile['name']}, Reported MIME: {$memoFile['type']}, Actual MIME: $actualMemoMimeType, Size: {$memoFile['size']}, Temp: {$memoFile['tmp_name']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");

        if (!in_array($actualMemoMimeType, $allowedTypes)) {
            $errors[] = "Laundry memo must be JPG or PNG (detected: $actualMemoMimeType).";
            error_log("Invalid laundry memo type: $actualMemoMimeType", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        } elseif ($memoFile['size'] > $maxFileSize) {
            $errors[] = "Laundry memo exceeds 5MB.";
            error_log("Laundry memo too large: {$memoFile['name']}, Size: {$memoFile['size']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        } elseif (!is_uploaded_file($memoFile['tmp_name'])) {
            $errors[] = "Laundry memo is not a valid uploaded file.";
            error_log("Not a valid uploaded file: {$memoFile['name']}, Temp: {$memoFile['tmp_name']}", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        } else {
            $laundryMemoPath = 'Uploads/' . uniqid('memo_') . '_' . $memoFileName;
            $destination = $usedDir . basename($laundryMemoPath);
            if (!move_uploaded_file($memoFile['tmp_name'], $destination)) {
                $errors[] = "Failed to upload laundry memo: {$memoFile['name']}";
                error_log("Failed to move laundry memo: {$memoFile['name']} from {$memoFile['tmp_name']} to $destination, Directory writable: " . (is_writable($usedDir) ? 'Yes' : 'No'), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            } else {
                error_log("Successfully uploaded laundry memo: {$memoFile['name']} to $destination", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            }
        }
    } elseif (!empty($_FILES['laundry_memo']['name'])) {
        $errorCodes = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
            UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
        ];
        $errorCode = $_FILES['laundry_memo']['error'];
        $errors[] = "Upload error for laundry memo: " . ($errorCodes[$errorCode] ?? 'Unknown error');
        error_log("Laundry memo upload error: Code $errorCode - " . ($errorCodes[$errorCode] ?? 'Unknown error'), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
    }

    // Save product if no errors
    if (empty($errors)) {
        try {
            $status = $laundryMemoPath ? 'pending' : 'approved';
            $hygieneVerified = ($category == 'hygiene' && $laundryMemoPath) ? 'pending' : 'approved';
            $stmt = $pdo->prepare("INSERT INTO products (seller_id, title, description, category, size, item_condition, price, images, laundry_memo, hygiene_verified, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $title,
                $description,
                $category,
                $size,
                $item_condition,
                $price,
                json_encode($imagePaths),
                $laundryMemoPath,
                $hygieneVerified,
                $status
            ]);
            $success = "Product added successfully! " . ($status == 'pending' ? 'Awaiting admin approval.' : 'Posted directly.');
            error_log("Product added by seller ID: {$_SESSION['user_id']}, Title: $title, Category: $category, Status: $status, Hygiene Verified: $hygieneVerified, Images: " . json_encode($imagePaths), 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
            header("Location: /boys_clothing_ecommerce/seller/dashboard.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
            error_log("Product add DB error: " . $e->getMessage() . ", Category attempted: $category, Condition: $item_condition", 3, "/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/errors.log");
        }
    }
}
?>

<?php require '../includes/header.php'; ?>
<div class="container my-4">
    <h2 class="text-center">Add New Product</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" id="addProductForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">Product Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="polo">Polo</option>
                            <option value="casual_shirt">Casual Shirt</option>
                            <option value="formal_shirt">Formal Shirt</option>
                            <option value="tshirt">T-Shirt</option>
                            <option value="jeans">Jeans</option>
                            <option value="shorts">Shorts</option>
                            <option value="jacket">Jacket</option>
                            <option value="sweater">Sweater</option>
                            <option value="hoodie">Hoodie</option>
                            <option value="trousers">Trousers</option>
                            <option value="shoes">Shoes</option>
                            <option value="hygiene">Hygiene</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="size" class="form-label">Size</label>
                        <select class="form-control" id="size" name="size" required>
                            <option value="">Select Size</option>
                            <option value="S">Small</option>
                            <option value="M">Medium</option>
                            <option value="L">Large</option>
                            <option value="XL">Extra Large</option>
                            <option value="XXL">Extra Extra Large</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="item_condition" class="form-label">Condition</label>
                        <select class="form-control" id="item_condition" name="item_condition" required>
                            <option value="">Select Condition</option>
                            <option value="new">New</option>
                            <option value="like_new">Like New</option>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="used">Used</option>
                            <option value="fair">Fair</option>
                            <option value="worn">Worn</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (TK)</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="images" class="form-label">Product Images (JPG, PNG, max 5MB each)</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept=".jpg,.jpeg,.png" required>
                    </div>
                    <div class="mb-3">
                        <label for="laundry_memo" class="form-label">Laundry Memo Image (JPG, PNG, max 5MB, optional)</label>
                        <input type="file" class="form-control" id="laundry_memo" name="laundry_memo" accept=".jpg,.jpeg,.png">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>
