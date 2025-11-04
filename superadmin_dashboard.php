<?php
require 'session.php';
check_role(['superadmin']); // Only superadmin allowed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Super Admin Dashboard - Blend S</title>
    <style>
        body {
            background-color: #f1e0c5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #342a21 !important;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(178, 85, 56, 0.3);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="superadmin_dashboard.php">Super Admin Panel - Blend S</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="superadmin_dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Admin Accounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="pos.php">Point of Sale (POS)</a></li>
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

    <div class="container mt-4">
        <div class="card p-4">
            <h1 class="text-choco">Super Administrator Homepage</h1>
            <p class="lead text-choco">From here, you can manage all aspects of the Blend S system, including creating and suspending cashier accounts.</p>
        </div>
    </div>
</body>
</html>
