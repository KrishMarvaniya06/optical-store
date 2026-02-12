<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
include 'manage-user.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Block or Unblock user
if(isset($_GET['action']) && isset($_GET['id'])){

    $id = intval($_GET['id']);

    if($_GET['action'] == 'block'){
        blockUser($mysqli, $id);
    }

    if($_GET['action'] == 'unblock'){
        unblockUser($mysqli, $id);
    }

    header("Location: admin-users.php");
    exit;
}

// Fetch users
$result = $mysqli->query("SELECT id, username, email, status, created_at FROM user ORDER BY created_at DESC");
$users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Users</title>
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

.main-content { margin-left: 240px; padding: 30px; width: 100%; }
.admin-header { background: gray; padding: 30px; color: black; border-radius: 20px; margin-bottom: 25px; text-align: center; }
table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; }
table th, table td { padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.06); text-align: left; }
table th { background: gray; color: black; }
a.delete { background: #dc3545; color: #fff; padding: 5px 10px; border-radius: 6px; text-decoration: none; }
a.delete:hover { opacity: 0.8; }
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
        <h1>👤 Users Management</h1>
        <p>View and manage registered users</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($users)): ?>
            <?php foreach($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['created_at']) ?></td>
                    <td>
                         <?php if($u['status'] == 'active'){ ?>
        <a class="delete" href="admin-users.php?action=block&id=<?= $u['id'] ?>" 
        onclick="return confirm('Block this user?')">Block</a>
    <?php } else { ?>
        <a style="background:green;color:white;padding:5px 10px;border-radius:6px;text-decoration:none;"
        href="admin-users.php?action=unblock&id=<?= $u['id'] ?>"
        onclick="return confirm('Unblock this user?')">Unblock</a>
    <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
