<?php
require '../session.php';
check_role(['admin', 'superadmin']);
require '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data) || !is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'No order data received.']);
    exit;
}

$cashier_id = $_SESSION['user_id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Validate items and compute server-side total
    $product_ids = array_map('intval', array_column($data, 'id'));
    if (count($product_ids) === 0) {
        throw new Exception('Empty order.');
    }

    $placeholders = rtrim(str_repeat('?,', count($product_ids)), ',');
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $db_products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => price]

    // Build order items and compute total
    $items_to_insert = [];
    $server_total = 0.0;

    foreach ($data as $item) {
        $pid = (int)$item['id'];
        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
        if ($qty <= 0) {
            throw new Exception("Invalid quantity for product ID {$pid}.");
        }
        if (!isset($db_products[$pid])) {
            throw new Exception("Product ID {$pid} not found.");
        }
        $price = (float)$db_products[$pid]; // use server price
        $line_total = $price * $qty;
        $server_total += $line_total;
        $items_to_insert[] = ['product_id' => $pid, 'quantity' => $qty, 'price_per_item' => $price];
    }

    // Insert into orders
    $stmt = $pdo->prepare("INSERT INTO orders (cashier_id, total_amount, date_added) VALUES (?, ?, NOW())");
    $stmt->execute([$cashier_id, $server_total]);
    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item, date_added) VALUES (?, ?, ?, ?, NOW())");
    foreach ($items_to_insert as $it) {
        $stmtItem->execute([$order_id, $it['product_id'], $it['quantity'], $it['price_per_item']]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Order recorded.', 'order_id' => $order_id, 'total' => $server_total]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
