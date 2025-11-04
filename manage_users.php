<?php
require 'session.php';
check_role(['superadmin']);
require 'db.php';

$register_message = '';

// Handle new admin registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, date_added) VALUES (?, ?, 'admin', NOW())");
        $stmt->execute([$username, $password_hash]);
        $register_message = "Admin account '".htmlspecialchars($username)."' created successfully!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            $register_message = "Error: Username '".htmlspecialchars($username)."' already exists.";
        } else {
            $register_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all admin accounts
$stmt = $pdo->prepare("SELECT id, username, status, date_added FROM users WHERE role = 'admin'");
$stmt->execute();
$admins = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Manage Admin Accounts - Blend S</title>
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
        .table {
            background-color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="superadmin_dashboard.php">Super Admin Panel - Blend S</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="superadmin_dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_users.php">Manage Admin Accounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="pos.php">Point of Sale (POS)</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php">View Reports</a></li>
                </ul>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card mb-4 p-4">
            <h1 class="text-choco">Manage Admin (Cashier) Accounts</h1>

            <div class="card mt-4">
                <div class="card-header bg-choco text-main">Register New Admin Account</div>
                <div class="card-body">
                    <?php if ($register_message): ?>
                        <div class="alert <?= strpos($register_message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>">
                            <?= $register_message ?>
                        </div>
                    <?php endif; ?>
                    <form action="manage_users.php" method="POST">
                        <input type="hidden" name="register_admin" value="1">
                        <div class="mb-3">
                            <label for="username" class="form-label text-choco">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-choco">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-highlight text-main">Create Admin Account</button>
                    </form>
                </div>
            </div>

            <h2 class="text-choco mt-4">Existing Admin Accounts</h2>
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Date Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td>
                            <span class="badge <?= $admin['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                <?= ucfirst($admin['status']) ?>
                            </span>
                        </td>
                        <td><?= $admin['date_added'] ?></td>
                        <td>
                            <?php if ($admin['status'] == 'active'): ?>
                                <a href="api/toggle_user_status.php?id=<?= $admin['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Suspend this account?');">Suspend</a>
                            <?php else: ?>
                                <a href="api/toggle_user_status.php?id=<?= $admin['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Activate this account?');">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
