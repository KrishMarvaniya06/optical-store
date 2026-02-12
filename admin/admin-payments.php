<?php
session_start();
require_once __DIR__ . '/../db_connect.php'; // adjust path

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Delete payment safely
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $mysqli->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        // Reset AUTO_INCREMENT to start from 1 again
        $mysqli->query("ALTER TABLE payments AUTO_INCREMENT = 1");
    }
    header("Location: admin-payments.php");
    exit;
}

// Filter by Payment Method
$filter_method = trim($_GET['payment_method'] ?? '');
$filter_sql = '';
if ($filter_method !== '') {
    $filter_sql = "WHERE p.payment_method = ?";
}

// Fetch payments
if ($filter_sql) {
    $stmt = $mysqli->prepare("
        SELECT p.id, p.order_id, p.payment_method, p.amount, p.payment_status, p.transaction_id, p.paid_at,
               o.id AS order_num
        FROM payments p
        JOIN orders o ON p.order_id = o.id
        $filter_sql
        ORDER BY p.id ASC
    ");
    $stmt->bind_param('s', $filter_method);
    $stmt->execute();
    $payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $payments = $mysqli->query("
        SELECT p.id, p.order_id, p.payment_method, p.amount, p.payment_status, p.transaction_id, p.paid_at,
               o.id AS order_num
        FROM payments p
        JOIN orders o ON p.order_id = o.id
        ORDER BY p.id ASC
    ")->fetch_all(MYSQLI_ASSOC);
}

// Available payment methods (can be dynamic if needed)
$payment_methods = ['credit_card', 'debit_card', 'netbanking', 'upi', 'cash', 'paypal'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Payments</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #046307;
    --secondary: #D4AF37;
    --muted: #f4f6f9;
    --text-dark: #333;
    --text-light: #fff;
}
body {
    font-family: 'Poppins', sans-serif;
    display: flex;
    min-height: 100vh;
    margin: 0;
    background: var(--muted);
}
/* Sidebar */
.sidebar {
    width: 240px;
    background: darkgray;
    color: black;
    display: flex;
    flex-direction: column;
    padding-top: 25px;
    position: fixed;
    height: 100vh;
    box-shadow: 8px 0 20px rgba(0,0,0,0.08);
}
.sidebar-title {
    font-size: 20px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 35px;
}
.sidebar a {
    display: flex;
    align-items: center;
    padding: 14px 25px;
    color: deeppink;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}
.sidebar a:hover {
    background: rgba(255,255,255,0.15);
    padding-left: 30px;
}


/* Main content */
.main-content {
    margin-left: 240px;
    padding: 30px;
    width: 100%;
}
.admin-header {
    background: gray;
    padding: 30px;
    color: black;
    border-radius: 20px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.admin-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}
.filter-form {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.filter-form select, .filter-form button {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.15);
    font-size: 0.9rem;
}
.filter-form button {
    background: var(--secondary);
    color: #fff;
    border: none;
    cursor: pointer;
}
.filter-form button:hover { background: #b58d2c; }

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
table th, table td {
    padding: 12px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    text-align: left;
}
table th {
    background: gray;
    color: black;
}
a.edit {
    background: #28a745;
    color: #fff;
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
    margin-right: 5px;
}
a.edit:hover { opacity: 0.8; }
a.delete {
    background: #dc3545;
    color: #fff;
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
}
a.delete:hover { opacity: 0.8; }
@media(max-width:768px){
    .sidebar { width:100%; height:auto; position:relative; }
    .main-content { margin-left:0; padding:15px; }
    .filter-form { flex-direction: column; align-items:flex-start; }
}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-title">Admin Panel</div>
    <a href="admin-dashboard.php">🏠 Dashboard</a>
    <a href="manage-product.php">📦 Manage Products</a>
    <a href="manage-orders.php">🛒 Orders</a>
    <a href="admin-payments.php">💳 Payments</a>
    <a href="admin-contact-messages.php">✉️ Contact Messages</a>
    <a href="admin-users.php">👤 Users</a>
    <a href="admin-feedback.php">⭐ Feedback</a>
    <a href="manage-categories.php">📁 Categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="admin-header">
        <h1>💳 Payments Management</h1>
        <p>View and manage all payments</p>
    </div>

    <form class="filter-form" method="get" action="admin-payments.php">
        <select name="payment_method">
            <option value="">All Payment Methods</option>
            <?php foreach($payment_methods as $method): ?>
                <option value="<?= $method ?>" <?= $filter_method === $method ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_', ' ', $method)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Transaction ID</th>
                <th>Paid At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if($payments): ?>
            <?php foreach($payments as $p): ?>
                <tr>
                    <td><?= $p['order_num'] ?></td>
                    <td><?= strtoupper($p['payment_method']) ?></td>
                    <td>₹<?= number_format($p['amount'], 2) ?></td>
                    <td><?= ucfirst($p['payment_status']) ?></td>
                    <td><?= !empty($p['transaction_id']) ? $p['transaction_id'] : '-' ?></td>
                    <td><?= !empty($p['paid_at']) ? $p['paid_at'] : '-' ?></td>
                    <td>
                        <a class="delete" href="admin-payments.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this payment?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">No payments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>