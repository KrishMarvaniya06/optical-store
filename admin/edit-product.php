<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['admin_id'])) { 
    header('Location: admin-login.php'); 
    exit; 
}

// Get product ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { 
    header('Location: manage-products.php'); 
    exit; 
}

// Fetch product
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: manage-products.php');
    exit;
}

// Fetch dropdown data
$main_categories = $mysqli->query("SELECT id, name FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$brands = $mysqli->query("SELECT id, name FROM brands ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$genders = $mysqli->query("SELECT id, name FROM genders ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$price_categories = $mysqli->query("SELECT id, label FROM price_categories ORDER BY label ASC")->fetch_all(MYSQLI_ASSOC);

$err = [];
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $main_category_id = (int)($_POST['main_category_id'] ?: null);
    $brand_id = (int)($_POST['brand_id'] ?: null);
    $gender_id = (int)($_POST['gender_id'] ?: null);
    $price_category_id = (int)($_POST['price_category_id'] ?: null);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $image_path = $product['image_path'];

    if ($name === '') $err[] = "Product name is required";
    if ($price <= 0) $err[] = "Price must be greater than 0";

    // Image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            $err[] = "Invalid image format";
        } else {
            $new = 'assets/images/products/' . uniqid('prod_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../' . $new)) {
                if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                    unlink(__DIR__ . '/../' . $image_path);
                }
                $image_path = $new;
            }
        }
    }

    if (!$err) {
        $stmt = $mysqli->prepare("
            UPDATE products SET
            name=?, main_category_id=?, brand_id=?, gender_id=?, price_category_id=?,
            price=?, description=?, image_path=?
            WHERE id=?
        ");
        $stmt->bind_param(
            'siiiisdsi',
            $name, $main_category_id, $brand_id, $gender_id,
            $price_category_id, $price, $description, $image_path, $id
        );

        if ($stmt->execute()) {
            $ok = "Product updated successfully!";
            $product = array_merge($product, $_POST, ['image_path'=>$image_path]);
        } else {
            $err[] = "Database error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --primary:#046307;
    --secondary:#D4AF37;
    --bg:#f4f6f9;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Poppins;background:var(--bg);display:flex;min-height:100vh}

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

/* Main */
.main-content{
    margin-left:240px;
    width:100%;
    padding:30px;
}
.card{
    max-width:800px;
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    margin:auto;
}
h1{text-align:center;color:var(--primary);margin-bottom:20px}
input,select,textarea{
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid #ccc;
}
button{
    background:var(--secondary);
    color:#fff;
    padding:10px 20px;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}
.thumb{width:150px;height:100px;object-fit:cover;border-radius:8px;margin-bottom:10px}
.alert{padding:10px;border-radius:8px;margin-bottom:15px}
.alert-danger{background:#ffdddd;color:#900}
.alert-success{background:#ddffdd;color:#046307}
.back-btn { display:inline-block; padding:8px 12px; background:#ccc; color:#333; border-radius:6px; text-decoration:none; margin-bottom:20px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
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

<!-- MAIN CONTENT -->
<div class="main-content">
<div class="card">
<a class="back-btn" href="admin-dashboard.php">← Back to Dashboard</a>

<h1>Edit Product</h1>

<?php if($err): ?><div class="alert alert-danger"><?=implode('<br>', $err)?></div><?php endif; ?>
<?php if($ok): ?><div class="alert alert-success"><?=$ok?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Product Name</label>
<input type="text" name="name" value="<?=htmlspecialchars($product['name'])?>" required>

<label>Main Category</label>
<select name="main_category_id">
<option value="">-- Select --</option>
<?php foreach($main_categories as $m): ?>
<option value="<?=$m['id']?>" <?=$m['id']==$product['main_category_id']?'selected':''?>><?=$m['name']?></option>
<?php endforeach; ?>
</select>

<label>Brand</label>
<select name="brand_id">
<option value="">-- Select --</option>
<?php foreach($brands as $b): ?>
<option value="<?=$b['id']?>" <?=$b['id']==$product['brand_id']?'selected':''?>><?=$b['name']?></option>
<?php endforeach; ?>
</select>

<label>Gender</label>
<select name="gender_id">
<option value="">-- Select --</option>
<?php foreach($genders as $g): ?>
<option value="<?=$g['id']?>" <?=$g['id']==$product['gender_id']?'selected':''?>><?=$g['name']?></option>
<?php endforeach; ?>
</select>

<label>Price Category</label>
<select name="price_category_id">
<option value="">-- Select --</option>
<?php foreach($price_categories as $pc): ?>
<option value="<?=$pc['id']?>" <?=$pc['id']==$product['price_category_id']?'selected':''?>><?=$pc['label']?></option>
<?php endforeach; ?>
</select>

<label>Price (₹)</label>
<input type="number" step="0.01" name="price" value="<?=$product['price']?>" required>

<label>Description</label>
<textarea name="description"><?=$product['description']?></textarea>

<?php if($product['image_path']): ?>
<img src="../<?=$product['image_path']?>" class="thumb">
<?php endif; ?>

<label>Change Image</label>
<input type="file" name="image">

<button type="submit">Update Product</button>
</form>

</div>
</div>

</body>
</html>
