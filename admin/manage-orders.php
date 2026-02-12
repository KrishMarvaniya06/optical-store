<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $mysqli->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage-orders.php");
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed_statuses = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $mysqli->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage-orders.php");
    exit;
}

// Get filter from GET
$filter_status = $_GET['status'] ?? '';

// Build query with optional status filter
$sql = "SELECT o.*, u.username FROM orders o LEFT JOIN user u ON o.user_id = u.id WHERE 1=1 ";
$params = [];
$types = '';

if ($filter_status && in_array($filter_status, ['pending','processing','shipped','delivered','cancelled'])) {
    $sql .= " AND o.status=?";
    $params[] = $filter_status;
    $types .= 's';
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $mysqli->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Orders</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #046307;
    --secondary: #D4AF37;
    --muted: #f4f6f9;
    --text-dark: #333;
    --text-light: #fff;
}
body { font-family: 'Poppins', sans-serif; display: flex; min-height: 100vh; margin: 0; background: var(--muted); }
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


.main-content { margin-left: 240px; padding: 30px; width: 100%; }
.admin-header { background: gray; padding: 30px; color: black; border-radius: 20px; margin-bottom: 25px; text-align: center; }

.filter-form { margin-bottom: 20px; display:flex; align-items:center; gap:10px; }
.filter-form select { padding: 6px 8px; border-radius:6px; border:1px solid #ccc; }
.filter-form button { padding: 6px 10px; border-radius:6px; border:none; background: var(--secondary); color:#fff; cursor:pointer; }
.filter-form button:hover { background:#b79528; }

table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; }
table th, table td { padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.06); text-align: left; }
table th { background: gray; color: black; }

a.delete { background: #dc3545; color: #fff; padding: 5px 10px; border-radius: 6px; text-decoration: none; }
a.delete:hover { opacity: 0.8; }
a.view { background: var(--secondary); color: #fff; padding: 5px 10px; border-radius: 6px; text-decoration: none; margin-right:5px; }
a.view:hover { opacity: 0.9; }

select.status { padding: 4px 6px; border-radius: 6px; border: 1px solid #ccc; }
button.update-status { padding: 5px 10px; border-radius: 6px; background: #046307; color: #fff; border: none; cursor: pointer; margin-left: 5px; }
button.update-status:hover { background: #034d05; }

@media(max-width:768px){ .sidebar { width:100%; height:auto; position:relative; } .main-content { margin-left:0; padding:15px; } }
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
    <a href="manage-categories.php">🗂 Categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="admin-header">
        <h1>🛒 Orders Management</h1>
        <p>Filter, update status, and manage all orders</p>
    </div>

    <form method="get" class="filter-form">
        <label for="status">Filter by Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <?php
            $statuses = ['pending','processing','shipped','delivered','cancelled'];
            foreach($statuses as $s) {
                $selected = ($filter_status==$s)?'selected':'';
                echo "<option value='$s' $selected>$s</option>";
            }
            ?>
        </select>
        <button type="submit">Filter</button>
        <a href="manage-orders.php" style="padding:6px 10px;background:#ccc;color:#333;border-radius:6px;text-decoration:none;">Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($orders)): ?>
            <?php foreach($orders as $o): ?>
            <tr>
                <td><?= htmlspecialchars($o['id']) ?></td>
                <td><?= htmlspecialchars($o['username'] ?? '-') ?></td>
                <td>₹<?= number_format($o['total_amount'] ?? 0, 2) ?></td>
                <td>
                    <form method="post" style="display:flex;align-items:center;">
                        <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                        <select name="status" class="status">
                            <?php
                            foreach($statuses as $s) {
                                $sel = ($o['status']==$s)?'selected':'';
                                echo "<option value='$s' $sel>$s</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="update-status">Update</button>
                    </form>
                </td>
                <td><?= htmlspecialchars($o['created_at']) ?></td>
                <td>
                    <a class="view" href="view-order.php?id=<?= (int)$o['id'] ?>">View</a>
                    <a class="delete" href="manage-orders.php?delete=<?= (int)$o['id'] ?>" onclick="return confirm('Delete this order?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
