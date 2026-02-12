<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
if (!isset($_SESSION['admin_id'])) { header('Location: admin-login.php'); exit; }

$err = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name === '') $err = 'Brand name required.';
    else {
        $stmt = $mysqli->prepare("INSERT INTO brands (name) VALUES (?)");
        $stmt->bind_param('s', $name);
        if ($stmt->execute()) $ok = 'Brand added.';
        else $err = 'Error: ' . $stmt->error;
        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $mysqli->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage-brands.php');
    exit;
}

$res = $mysqli->query("SELECT * FROM brands ORDER BY name ASC");
$brands = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Manage Brands - Admin</title>
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
    background: var(--muted);
    color: var(--text-dark);
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 240px;
    background: linear-gradient(145deg, var(--primary), var(--secondary));
    color: #fff;
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
    color: #fff;
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
    background: linear-gradient(-45deg, var(--primary), var(--secondary));
    padding: 30px;
    color: var(--text-light);
    border-radius: 20px;
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
.admin-header h1 {
    font-size: 2rem;
    font-weight: 700;
}
.form, .list {
    background:#fff;
    border-radius:12px;
    padding:16px;
    margin-bottom:16px;
    box-shadow:0 8px 24px rgba(4,99,7,0.04);
}
input {
    padding:10px;
    border-radius:10px;
    border:1px solid rgba(0,0,0,0.10);
    width:100%;
    margin-top:6px;
}
button {
    margin-top:10px;
    padding:10px 12px;
    border-radius:10px;
    border:none;
    background:var(--secondary);
    color:#fff;
    font-weight:700;
    cursor:pointer;
}
.table {
    width:100%;
    border-collapse:collapse;
    margin-top:12px;
}
.table td, .table th {
    padding:10px;
    border-bottom:1px solid rgba(0,0,0,0.06);
    text-align:left;
}
.del {
    background:#ffdddd;
    color:#900;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
}
.msg {margin-top:8px;}
@media(max-width:768px){
    .sidebar { width:100%; height:auto; position:relative; }
    .main-content { margin-left:0; padding:15px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-title">Admin Panel</div>
    <a href="admin-dashboard.php">🏠 Dashboard</a>
    <a href="manage-product.php">📦 Manage Products</a>
    <a href="manage-orders.php">🛒 Orders</a>
    <a href="admin-payments.php">💳 Payments</a>
    <a href="admin-contact-messages.php">✉️ Contact Messages</a>
    <a href="admin-users.php">👤 Users</a>
    <a href="admin-feedback.php">⭐ Feedback</a>
    <a href="manage-categories.php">categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="admin-header">
        <h1>🏷 Manage Brands</h1>
        <p>Add and remove product brands</p>
    </div>

    <div class="form">
      <form method="post">
        <label>Add Brand</label>
        <input name="name" placeholder="Brand name" value="<?=htmlspecialchars($_POST['name'] ?? '')?>">
        <button type="submit">Add</button>
      </form>
      <?php if ($err): ?><div class="msg" style="color:#c00"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <?php if ($ok): ?><div class="msg" style="color:green"><?=htmlspecialchars($ok)?></div><?php endif; ?>
    </div>

    <div class="list">
      <h3>Existing Brands</h3>
      <table class="table">
        <thead><tr><th>#</th><th>Name</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($brands as $b): ?>
          <tr>
            <td><?= (int)$b['id'] ?></td>
            <td><?= htmlspecialchars($b['name']) ?></td>
            <td><a class="del" href="manage-brands.php?delete=<?= (int)$b['id'] ?>" onclick="return confirm('Delete brand?')">Delete</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>

</body>
</html>
