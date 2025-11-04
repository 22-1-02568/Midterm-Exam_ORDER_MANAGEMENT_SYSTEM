<?php
require 'session.php';
check_role(['admin', 'superadmin']);
require 'db.php';

// Fetch all products to display
$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Point of Sale (POS) - Blend S</title>
    <style>
        body {
            background-color: #f1e0c5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: <?= $_SESSION['role'] == 'superadmin' ? '#342a21' : '#b25538' ?> !important;
        }
        .product-card { cursor: pointer; }
        .product-card:hover { border-color: #b25538; }
        .product-card img { height: 120px; object-fit: cover; }
        .order-summary { position: sticky; top: 20px; }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(178, 85, 56, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation based on role -->
    <?php if ($_SESSION['role'] == 'superadmin'): ?>
        <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="superadmin_dashboard.php">Super Admin Panel - Blend S</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="superadmin_dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Admin Accounts</a></li>
                    <li class="nav-item"><a class="nav-link active" href="pos.php">Point of Sale (POS)</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php">View Reports</a></li>
                </ul>
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
                </span>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>
    <?php else: ?>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="admin_dashboard.php">Cashier Panel - Blend S</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="pos.php">Point of Sale (POS)</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php">View Reports</a></li>
                    </ul>
                    <a href="logout.php" class="btn btn-light">Logout</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Products Grid -->
            <div class="col-md-7">
                <h1 class="text-choco">Menu Items</h1>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="<?= htmlspecialchars($product['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body text-center pb-2">
                                    <h6 class="card-title text-choco"><?= htmlspecialchars($product['name']) ?></h6>
                                    <p class="card-text fw-bold text-choco">₱<?= number_format($product['price'], 2) ?></p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-sm" value="1" min="1" id="qty-<?= $product['id'] ?>" aria-label="Quantity">
                                        <button class="btn btn-highlight btn-sm text-main" onclick="addProductToOrder(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($product['name'])) ?>', <?= $product['price'] ?>)">Add to order</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-5">
                <div class="order-summary card">
                    <div class="card-body">
                        <h2 class="card-title text-choco">Ordered Items</h2>
                        <table class="table" id="order-items-table">
                            <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Total</th><th></th></tr></thead>
                            <tbody id="order-items-body"></tbody>
                        </table>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center fs-4 fw-bold mb-3">
                            <span class="text-choco">Total Price:</span>
                            <span class="text-choco">₱<span id="order-total">0.00</span></span>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text text-choco">Payment Amount ₱</span>
                            <input type="number" class="form-control" id="payment-amount" placeholder="e.g., 200">
                        </div>
                        <button id="pay-btn" class="btn btn-highlight text-main btn-lg w-100">Pay!</button>
                        <p id="order-status" class="text-center mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="uploads/js/pos.js"></script>
</body>
</html>
