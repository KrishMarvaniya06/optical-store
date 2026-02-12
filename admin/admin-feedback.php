<?php
session_start();
require_once __DIR__ . '/../db_connect.php'; // adjust path

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Delete feedback
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $mysqli->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin-feedback.php");
    exit;
}

// Fetch feedback with user and product info
$query = "
    SELECT 
        f.*, 
        u.username AS user_name, 
        p.name AS product_name
    FROM feedback f
    JOIN user u ON f.user_id = u.id
    JOIN products p ON f.product_id = p.id
    ORDER BY f.created_at DESC
";


$feedbacks = $mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Feedback</title>
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
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.admin-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}
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
a.delete {
    background: #dc3545;
    color: #fff;
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
}
a.delete:hover {
    opacity: 0.8;
}
@media(max-width:768px){
    .sidebar { width:100%; height:auto; position:relative; }
    .main-content { margin-left:0; padding:15px; }
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
    <a href="manage-categories.php">categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="admin-header">
        <h1>⭐ Product Feedback</h1>
        <p>View and manage user feedback</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if($feedbacks): ?>
            <?php foreach($feedbacks as $f): ?>
                <tr>
                    <td><?= $f['id'] ?></td>
                    <td><?= htmlspecialchars($f['user_name']) ?></td>
                    <td><?= htmlspecialchars($f['product_name']) ?></td>
                    <td><?= $f['rating'] ?></td>
                    <td><?= nl2br(htmlspecialchars($f['review'])) ?></td>
                    <td><?= $f['created_at'] ?></td>
                    <td>
                        <a class="delete" href="admin-feedback.php?delete=<?= $f['id'] ?>" onclick="return confirm('Delete this feedback?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">No feedback found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
