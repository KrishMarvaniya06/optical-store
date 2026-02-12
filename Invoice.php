<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    die("Invalid Order ID");
}

/* FETCH ORDER */
$stmt = $mysqli->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) die("Order not found");

/* FETCH ITEMS */
$items = $mysqli->query("
    SELECT oi.*, p.name, p.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
")->fetch_all(MYSQLI_ASSOC);

/* CALCULATIONS */
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$gst_rate = 5;
$gst_amount = round($subtotal * $gst_rate / 100, 2);
$grand_total = round($subtotal + $gst_amount, 2); // keep 0 if you do not have discount column
$shipping = 40;

$grand_total = round($subtotal + $gst_amount + $shipping, 2);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice #<?= $order_id ?></title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    background:#f3f6fb;
    font-family:Segoe UI, Arial, sans-serif;
    font-size:14px;
    color:#222;
}

.invoice-wrap{
    max-width:980px;
    margin:30px auto;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

/* HEADER */
.header{
    background:linear-gradient(90deg,#1f7a1f,#4caf50);
    color:#fff;
    padding:22px 30px;
    text-align:center;
}
.header h1{
    margin:0;
    font-size:28px;
    letter-spacing:.5px;
}
.header small{
    display:block;
    margin-top:5px;
    opacity:.9;
}

/* INFO CARDS */
.info-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
    padding:20px 25px 5px;
}

.info-box{
    background:#f7fafc;
    border:1px solid #e3e8ef;
    border-radius:6px;
    padding:12px 14px;
}
.info-box label{
    font-size:12px;
    color:#666;
}
.info-box div{
    margin-top:4px;
    font-weight:600;
}

/* SECTION TITLE */
.section-title{
    margin:20px 25px 0;
    background:linear-gradient(90deg,#1f7a1f,#4caf50);
    color:#fff;
    padding:8px 12px;
    border-radius:4px;
    font-weight:600;
}

/* BILL TO */
.bill-box{
    margin:10px 25px 0;
    background:#f7fafc;
    border:1px solid #e3e8ef;
    border-radius:6px;
    padding:15px;
    line-height:1.6;
}

/* TABLE */
.table-wrap{
    margin:10px 25px 0;
    overflow-x:auto;
}
table{
    width:100%;
    border-collapse:collapse;
}
thead th{
    background:linear-gradient(90deg,#1f7a1f,#4caf50);
    color:#fff;
    padding:10px 8px;
    font-size:13px;
}
tbody td{
    border:1px solid #e3e8ef;
    padding:9px 8px;
    background:#fff;
}
.right{text-align:right}
.center{text-align:center}

/* TOTAL BOX */
.total-area{
    display:flex;
    justify-content:flex-end;
    padding:15px 25px 0;
}
.total-box{
    width:300px;
}
.total-row{
    display:flex;
    justify-content:space-between;
    padding:6px 0;
}
.total-row.discount span:last-child{color:#d32f2f}
.total-row.shipping span:last-child{color:#2e7d32}

.grand-total{
    margin-top:10px;
    background:linear-gradient(90deg,#1f7a1f,#4caf50);
    color:#fff;
    padding:10px 12px;
    display:flex;
    justify-content:space-between;
    font-weight:700;
    border-radius:4px;
}

/* FOOTER */
.footer{
    text-align:center;
    padding:30px 20px 20px;
    color:#555;
    font-size:13px;
}
.footer strong{
    display:block;
    margin-bottom:6px;
}

.actions{
    text-align:center;
    margin:25px 0 35px;
}
.actions a,
.actions button{
    padding:8px 18px;
    border:1px solid #1f7a1f;
    background:#fff;
    color:#1f7a1f;
    border-radius:4px;
    cursor:pointer;
    font-weight:600;
    text-decoration:none;
    margin:0 6px;
}

@media(max-width:900px){
    .info-grid{grid-template-columns:repeat(2,1fr);}
}

@media print{
    body{background:#fff}
    .actions{display:none}
    .invoice-wrap{box-shadow:none;margin:0;border-radius:0}
}
</style>
</head>
<body>

<div class="invoice-wrap">

    <!-- HEADER -->
    <div class="header">
        <h1>Lumineux Opticals</h1>
        <small>Your Trusted Optical Partner</small>
        <small>Phone: +91 99999 88888 | Email: support@lumineux.com</small>
    </div>

    <!-- INFO BOXES -->
    <div class="info-grid">
        <div class="info-box">
            <label>Invoice Number</label>
            <div>INV<?= $order_id ?></div>
        </div>
        <div class="info-box">
            <label>Invoice Date</label>
            <div><?= date("d F, Y", strtotime($order['created_at'])) ?></div>
        </div>
        <div class="info-box">
            <label>Order Date</label>
            <div><?= date("d F, Y h:i A", strtotime($order['created_at'])) ?></div>
        </div>
        <div class="info-box">
            <label>Order Status</label>
            <div><?= ucfirst($order['status']) ?></div>
        </div>
    </div>

    <!-- BILL TO -->
    <div class="section-title">Bill To</div>
    <div class="bill-box">
        <strong><?= htmlspecialchars($order['name']) ?></strong><br>
        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
        <?php if(!empty($order['phone'])): ?>
            Phone: <?= htmlspecialchars($order['phone']) ?><br>
        <?php endif; ?>
        <?php if(!empty($order['email'])): ?>
            Email: <?= htmlspecialchars($order['email']) ?>
        <?php endif; ?>
    </div>

    <!-- ORDER ITEMS -->
    <div class="section-title">Order Items</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th class="center">Qty</th>
                    <th class="right">Unit Price</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=1; foreach($items as $item): ?>
                <tr>
                    <td class="center"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td class="center"><?= $item['quantity'] ?></td>
                    <td class="right">₹<?= number_format($item['price'],2) ?></td>
                    <td class="right">₹<?= number_format($item['price']*$item['quantity'],2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- TOTALS -->
    <div class="total-area">
        <div class="total-box">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹<?= number_format($subtotal,2) ?></span>
            </div>
            <div class="total-row">
                <span>GST (5%)</span>
                <span>₹<?= number_format($gst_amount,2) ?></span>
            </div>
            <div class="total-row shipping">
                <span>Shipping:</span>
                <span><?= $shipping == 0 ? 'Free' : '₹'.number_format($shipping,2) ?></span>
            </div>

            <div class="grand-total">
                <span>Grand Total:</span>
                <span>₹<?= number_format($grand_total,2) ?></span>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <strong>Thank you for your business!</strong>
        This is a computer-generated invoice. No signature required.<br>
        For any queries, please contact us at support@lumineux.com<br><br>
        <strong>Lumineux Opticals – Clear Vision, Better Life</strong>
    </div>

</div>

<div class="actions">
    <button onclick="window.print()">🖨 Print Invoice</button>
    <a href="index.php">⬅ Back to Home</a>
</div>

</body>
</html>