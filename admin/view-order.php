<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Get order ID from GET
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id <= 0) {
    header('Location: manage-orders.php');
    exit;
}

// Fetch order details
$stmt = $mysqli->prepare("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    LEFT JOIN user u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: manage-orders.php');
    exit;
}

// Fetch order items with product image
$stmt = $mysqli->prepare("
    SELECT oi.*, p.name AS product_name, p.price AS product_price, p.image_path 
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$order_items = $items_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Order #<?= $order_id ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background:#f4f6f9; padding:20px; }
.container { max-width:900px; margin:0 auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
h1 { margin-bottom:10px; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
table th, table td { padding:12px; border-bottom:1px solid #ddd; text-align:left; vertical-align: middle; }
table th { background:gray; color:black; }
.status { font-weight:bold; }
.back-btn { display:inline-block; padding:8px 12px; background:#ccc; color:#333; border-radius:6px; text-decoration:none; margin-bottom:20px; }
.product-img { width:80px; height:60px; object-fit:cover; border-radius:6px; margin-right:10px; vertical-align:middle; }
.product-name-wrapper { display:flex; align-items:center; }
</style>
</head>
<body>

<div class="container">
    <a class="back-btn" href="manage-orders.php">← Back to Orders</a>
    <h1>Order #<?= htmlspecialchars($order['id']) ?></h1>
    <p><strong>User:</strong> <?= htmlspecialchars($order['username'] ?? '-') ?> (<?= htmlspecialchars($order['email'] ?? '-') ?>)</p>
    <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($order['status']) ?></span></p>
    <p><strong>Created At:</strong> <?= htmlspecialchars($order['created_at']) ?></p>

    <h2>Ordered Items</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price (₹)</th>
                <th>Quantity</th>
                <th>Subtotal (₹)</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $total = 0;
        foreach($order_items as $item): 
            $subtotal = $item['product_price'] * $item['quantity'];
            $total += $subtotal;
        ?>
            <tr>
                <td>
                    <div class="product-name-wrapper">
                        <?php if ($item['image_path'] && file_exists(__DIR__ . '/../' . $item['image_path'])): ?>
                            <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt="Product Image" class="product-img">
                        <?php else: ?>
                            <div class="product-img" style="background:#eee;display:flex;align-items:center;justify-content:center;color:#aaa;">No image</div>
                        <?php endif; ?>
                        <?= htmlspecialchars($item['product_name'] ?? '-') ?>
                    </div>
                </td>
                <td><?= number_format($item['product_price'],2) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td><?= number_format($subtotal,2) ?></td>
            </tr>
        <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                <td><strong>₹<?= number_format($total,2) ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
