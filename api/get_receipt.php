<?php
require '../session.php';
check_role(['admin', 'superadmin']);
require '../db.php';

if (!isset($_GET['order_id'])) {
    die('Order ID is required.');
}

$order_id = intval($_GET['order_id']);

// Get order details
$stmt = $pdo->prepare("SELECT o.id, o.total_amount, o.date_added, u.username as cashier_name
                       FROM orders o
                       JOIN users u ON o.cashier_id = u.id
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die('Order not found.');
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.quantity, oi.price_per_item, p.name as product_name
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.id
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<div class="receipt-container" style="font-family: 'Courier New', monospace; max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; background: #fff;">
    <div class="text-center mb-3">
        <h4 style="margin: 0; color: #b25538;">Blend S Coffee</h4>
        <p style="margin: 5px 0; font-size: 12px;">123 Coffee Street, Manila, Philippines</p>
        <p style="margin: 5px 0; font-size: 12px;">Tel: (02) 123-4567</p>
        <p style="margin: 5px 0; font-size: 12px;">Order Receipt</p>
    </div>

    <div class="mb-3">
        <p style="margin: 2px 0; font-size: 12px;"><strong>Order ID:</strong> #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></p>
        <p style="margin: 2px 0; font-size: 12px;"><strong>Cashier:</strong> <?= htmlspecialchars($order['cashier_name']) ?></p>
        <p style="margin: 2px 0; font-size: 12px;"><strong>Date:</strong> <?= date('M d, Y H:i:s', strtotime($order['date_added'])) ?></p>
    </div>

    <hr style="border-top: 1px dashed #000; margin: 10px 0;">

    <div class="items-header d-flex justify-content-between" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">
        <span>Item</span>
        <span>Qty</span>
        <span>Price</span>
        <span>Total</span>
    </div>

    <hr style="border-top: 1px dashed #000; margin: 5px 0;">

    <div class="items mb-3">
        <?php foreach ($items as $item): ?>
        <div class="d-flex justify-content-between" style="font-size: 11px; margin: 3px 0;">
            <span style="flex: 2;"><?= htmlspecialchars($item['product_name']) ?></span>
            <span style="flex: 0.5; text-align: center;"><?= $item['quantity'] ?></span>
            <span style="flex: 1; text-align: right;">₱<?= number_format($item['price_per_item'], 2) ?></span>
            <span style="flex: 1; text-align: right;">₱<?= number_format($item['price_per_item'] * $item['quantity'], 2) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <hr style="border-top: 1px dashed #000; margin: 10px 0;">

    <div class="total text-end" style="font-weight: bold; font-size: 14px;">
        <p style="margin: 5px 0;">TOTAL: ₱<?= number_format($order['total_amount'], 2) ?></p>
    </div>

    <div class="text-center mt-3" style="font-size: 10px; color: #666;">
        <p>Thank you for your business!</p>
        <p>Blend S Coffee - Serving the best coffee</p>
        <p>Visit us again soon!</p>
    </div>
</div>
