<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Delete product
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Fetch image path to delete file
    $stmt = $mysqli->prepare("SELECT image_path FROM products WHERE id=?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        if ($product['image_path'] && file_exists(__DIR__ . '/../' . $product['image_path'])) {
            unlink(__DIR__ . '/../' . $product['image_path']);
        }

        // Delete from DB
        $stmt = $mysqli->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param('i', $delete_id);
        $stmt->execute();
        $stmt->close();
        $ok = "Product deleted successfully!";
    } else {
        $err = "Product not found.";
    }
}

// Fetch all products with joins to show category names
$query = "
SELECT p.id, p.name, p.price, p.description, p.image_path, 
       b.name AS brand_name, g.name AS gender_name, pc.label AS price_label, m.name AS main_category
FROM products p
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN genders g ON p.gender_id = g.id
LEFT JOIN price_categories pc ON p.price_category_id = pc.id
LEFT JOIN main_categories m ON p.main_category_id = m.id
ORDER BY p.id DESC
";

$products = $mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Products - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #046307;
    --secondary: #D4AF37;
    --muted: #f4f6f9;
    --text-dark: #333;
    --text-light: #fff;
}
* { box-sizing: border-box; margin:0; padding:0; }
body { font-family:'Poppins',sans-serif; display:flex; min-height:100vh; background: var(--muted); color: var(--text-dark); }

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
.main-content { margin-left:240px; padding:30px; width:100%; }
h1 { margin-bottom:20px; }
table { width:100%; border-collapse: collapse; border-radius:10px; overflow:hidden; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.05); }
th,td { padding:12px; border-bottom:1px solid rgba(0,0,0,0.1); text-align:left; }
th { background: gray;; color:black; }
td a { padding:5px 10px; border-radius:6px; color:#fff; text-decoration:none; font-size:0.85rem; margin-right:5px; display:inline-block; }
.btn-edit { background: var(--primary); }
.btn-edit:hover { background: #034d05; }
.btn-delete { background: #dc3545; }
.btn-delete:hover { background:#a71d2a; }
.success { color:green; margin-bottom:15px; }
.error { color:red; margin-bottom:15px; }

@media(max-width:768px){
    .sidebar{width:100%;position:relative;height:auto;}
    .main-content{margin-left:0;padding:15px;}
    table, th, td { font-size:12px; }
}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-title">Admin Panel</div>
    <a href="admin-dashboard.php">🏠 Dashboard</a>
    <a href="add-product.php">➕ Add Product</a>
    <a href="manage-product.php">📦 Manage Products</a>
    <a href="manage-categories.php">🗂 Categories</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <h1>Manage Products</h1>

    <?php if(isset($ok)) echo "<div class='success'>$ok</div>"; ?>
    <?php if(isset($err)) echo "<div class='error'>$err</div>"; ?>

    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Main Category</th>
            <th>Brand</th>
            <th>Gender</th>
            <th>Price Cat</th>
            <th>Price (₹)</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php $i=1; foreach($products as $p): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['main_category'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['brand_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['gender_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['price_label'] ?? '-') ?></td>
            <td><?= number_format($p['price'],2) ?></td>
            <td>
                <?php if($p['image_path'] && file_exists(__DIR__ . '/../'.$p['image_path'])): ?>
                    <img src="../<?= $p['image_path'] ?>" width="50">
                <?php endif; ?>
            </td>
            <td>
                <a class="btn-edit" href="edit-product.php?id=<?= $p['id'] ?>">Edit</a>
                <a class="btn-delete" href="?delete_id=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
