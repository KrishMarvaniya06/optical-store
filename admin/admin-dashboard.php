<?php
session_start();

// Database connection
require_once __DIR__ . '/../db_connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Fetch totals
function getCount($mysqli, $table) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM `$table`");
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count;
}

$totalProducts = getCount($mysqli, 'products');
$totalBrands = getCount($mysqli, 'brands');
$totalGenders = getCount($mysqli, 'genders');
$totalPrices = getCount($mysqli, 'price_categories');
$totalOrder = getCount($mysqli, 'orders');
$totalMainCategories = getCount($mysqli, 'main_categories'); // ✅ Added main categories count
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Lumineux Opticals</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    margin: 0;
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
    background: darkgray;
    padding: 40px;
    color: black;
    border-radius: 20px;
    margin-bottom: 30px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
.admin-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
}
.card {
    padding: 20px;
    border-radius: 20px;
    background: pink;
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    text-align: center;
}
.card h3 {
    margin-bottom: 10px;
    color: black;
}
.card .count {
    font-size: 2rem;
    font-weight: 700;
    color: black;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        border-radius: 0;
    }
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    .admin-header h1 {
        font-size: 2rem;
    }
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
    <a href="manage-categories.php">🗂 Categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="admin-header">
        <h1>🛠 Admin Dashboard</h1>
        <p>Manage your products and categories visually</p>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Main Categories</h3>
            <div class="count"><?= $totalMainCategories ?></div>
        </div>
        <div class="card">
            <h3>Brands</h3>
            <div class="count"><?= $totalBrands ?></div>
        </div>
        <div class="card">
            <h3>Genders</h3>
            <div class="count"><?= $totalGenders ?></div>
        </div>
        <div class="card">
            <h3>Price Categories</h3>
            <div class="count"><?= $totalPrices ?></div>
        </div>
        <div class="card">
            <h3>Total Products</h3>
            <div class="count"><?= $totalProducts ?></div>
        </div>
       <div class="card">
            <h3>Orders</h3>
            <div class="count"><?= $totalOrder ?></div>
        </div>
    </div>
</div>

</body>
</html>
