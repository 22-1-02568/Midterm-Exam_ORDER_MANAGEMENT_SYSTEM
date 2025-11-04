<?php
require 'session.php';
check_role(['admin', 'superadmin']); // Both roles can access
require 'db.php';

$message = '';

// Handle new product form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $added_by = $_SESSION['user_id'];
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        // Check if file type is valid (basic check)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $message = "Error: Only JPG, JPEG, & PNG files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $message = "Error: There was an error uploading your file.";
            }
        }
    } else {
        $message = "Error: Product image is required.";
    }

    // Insert into database if upload was successful
    if (!empty($image_path)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, image_path, added_by, date_added) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $price, $image_path, $added_by]);
            $message = "Product '".htmlspecialchars($name)."' added successfully!";
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
        }
    }
}

// Fetch all products
$products = $pdo->query("SELECT p.*, u.username as added_by_user FROM products p JOIN users u ON p.added_by = u.id ORDER BY p.date_added DESC")->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Manage Products - Blend S</title>
    <style>
        body {
            background-color: #f1e0c5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: <?= $_SESSION['role'] == 'superadmin' ? '#342a21' : '#b25538' ?> !important;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(178, 85, 56, 0.3);
        }
        .table {
            background-color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <?php if ($_SESSION['role'] == 'superadmin'): ?>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="superadmin_dashboard.php">Super Admin Panel - Blend S</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="superadmin_dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Admin Accounts</a></li>
                        <li class="nav-item"><a class="nav-link" href="pos.php">Point of Sale (POS)</a></li>
                        <li class="nav-item"><a class="nav-link active" href="manage_products.php">Manage Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php">View Reports</a></li>
                    </ul>
                    <a href="logout.php" class="btn btn-light">Logout</a>
                </div>
            </div>
        </nav>
    <?php else: // admin ?>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="admin_dashboard.php">Cashier Panel - Blend S</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="pos.php">Point of Sale (POS)</a></li>
                        <li class="nav-item"><a class="nav-link active" href="manage_products.php">Manage Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php">View Reports</a></li>
                    </ul>
                    <a href="logout.php" class="btn btn-light">Logout</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <div class="card p-4">
            <h1 class="text-choco">Manage Products</h1>

            <div class="card mt-4">
                <div class="card-header bg-choco text-main">Add New Product</div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>
                    <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="add_product" value="1">
                        <div class="mb-3">
                            <label for="name" class="form-label text-choco">Product Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label text-choco">Price (PHP):</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label text-choco">Product Image:</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpeg, image/jpg" required>
                        </div>
                        <button type="submit" class="btn btn-highlight text-main">Add Product</button>
                    </form>
                </div>
            </div>

            <h2 class="text-choco mt-4">Existing Products</h2>
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price (PHP)</th>
                        <th>Added By</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;"></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= number_format($product['price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['added_by_user']) ?></td>
                        <td><?= $product['date_added'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
