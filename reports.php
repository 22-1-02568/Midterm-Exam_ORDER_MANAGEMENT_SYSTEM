<?php
require 'session.php';
check_role(['admin', 'superadmin']);
require 'db.php';

// Set default dates
$date_start = $_GET['date_start'] ?? date('Y-m-d');
$date_end = $_GET['date_end'] ?? date('Y-m-d');

// Adjust date_end to include the whole day
$date_end_for_query = $date_end . ' 23:59:59';

$params = [$date_start, $date_end_for_query];
$sql = "SELECT
            o.id as order_id,
            o.total_amount,
            o.date_added as transaction_date,
            u.username as cashier_name
        FROM orders o
        JOIN users u ON o.cashier_id = u.id
        WHERE o.date_added BETWEEN ? AND ?
        ORDER BY o.date_added DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Calculate total sum
$total_sum = 0;
foreach ($transactions as $t) {
    $total_sum += $t['total_amount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Transaction Reports - Blend S</title>
    <style>
        body {
            background-color: #f1e0c5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: <?= $_SESSION['role'] == 'superadmin' ? '#342a21' : '#b25538' ?> !important;
        }
        .filter-form {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 15px rgba(178, 85, 56, 0.3);
        }
        .table {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            overflow: hidden;
        }
        tfoot tr {
            font-weight: bold;
            font-size: 1.2em;
            background: rgba(178, 85, 56, 0.1);
        }
        .print-btn {
            text-decoration: none;
            background: #b25538;
            color: #f1e0c5;
            padding: 10px 15px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .print-btn:hover {
            background: #d46a38;
            color: #fff;
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
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="reports.php">View Reports</a></li>
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
                    <li class="nav-item"><a class="nav-link" href="pos.php">Point of Sale (POS)</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="reports.php">View Reports</a></li>
                </ul>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>
<?php endif; ?>

    <div class="container mt-4">
        <h1 class="text-choco">Transaction History</h1>

        <form action="reports.php" method="GET" class="filter-form">
            <label for="date_start" class="text-choco">Start Date:</label>
            <input type="date" id="date_start" name="date_start" value="<?= htmlspecialchars($date_start) ?>" class="form-control d-inline-block w-auto mx-2">
            
            <label for="date_end" class="text-choco">End Date:</label>
            <input type="date" id="date_end" name="date_end" value="<?= htmlspecialchars($date_end) ?>" class="form-control d-inline-block w-auto mx-2">
            
            <button type="submit" class="btn btn-highlight text-main mx-2">Filter</button>
            
            <a href="generate_pdf.php?date_start=<?= htmlspecialchars($date_start) ?>&date_end=<?= htmlspecialchars($date_end) ?>" 
               class="print-btn" 
               target="_blank">Print to PDF</a>
        </form>
        
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Cashier</th>
                    <th>Transaction Date</th>
                    <th>Total Amount (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= $t['order_id'] ?></td>
                    <td><?= htmlspecialchars($t['cashier_name']) ?></td>
                    <td><?= $t['transaction_date'] ?></td>
                    <td><?= number_format($t['total_amount'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No transactions found for this date range.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end text-choco">Total Sum:</td>
                    <td class="text-choco"><?= number_format($total_sum, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
