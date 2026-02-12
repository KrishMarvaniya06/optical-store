<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Fetch categories for select dropdowns
$main_categories = $mysqli->query("SELECT id, name FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$brands = $mysqli->query("SELECT id, name FROM brands ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$genders = $mysqli->query("SELECT id, name FROM genders ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$prices = $mysqli->query("SELECT id, label FROM price_categories ORDER BY label ASC")->fetch_all(MYSQLI_ASSOC);

$err = [];
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $main_category_id = (int)($_POST['main_category_id'] ?? 0);
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $gender_id = (int)($_POST['gender_id'] ?? 0);
    $price_category_id = (int)($_POST['price_category_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image_path = '';

    // Validation
    if (!$name) $err[] = "Product name is required.";
    if ($main_category_id <= 0) $err[] = "Please select a main category.";
    if ($price <= 0) $err[] = "Price must be greater than 0.";

    // Image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $err[] = "Only JPG, PNG, and WEBP images are allowed.";
        } else {
            $newName = 'product_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/products/' . $newName;
            if (!move_uploaded_file($fileTmp, $dest)) {
                $err[] = "Failed to upload image.";
            } else {
                $image_path = 'assets/images/products/' . $newName;
            }
        }
    }

    // Insert into database
    if (empty($err)) {
        $stmt = $mysqli->prepare("INSERT INTO products 
            (name, main_category_id, brand_id, gender_id, price_category_id, price, description, image_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('siiiidss', $name, $main_category_id, $brand_id, $gender_id, $price_category_id, $price, $description, $image_path);
        if ($stmt->execute()) {
            $ok = "Product added successfully!";
            $name = $description = '';
            $main_category_id = $brand_id = $gender_id = $price_category_id = 0;
        } else {
            $err[] = "Database error: " . $mysqli->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Add Product - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
/* same styles as your original UI */
:root {
    --primary: #046307;
    --secondary: #D4AF37;
    --muted: #f4f6f9;
    --text-dark: #333;
    --text-light: #fff;
}
body { font-family: 'Poppins', sans-serif; display: flex; min-height: 100vh; margin: 0; background: var(--muted); }
.sidebar { width: 240px; background: gray; color: black; display:flex; flex-direction: column; padding-top:25px; position:fixed; height:100vh; box-shadow:8px 0 20px rgba(0,0,0,0.08); }
.sidebar-title { font-size:20px; font-weight:700; text-align:center; margin-bottom:35px; }
.sidebar a { display:flex; align-items:center; padding:14px 25px; color:deeppink; text-decoration:none; font-weight:500; transition: all 0.3s ease; }
.sidebar a:hover { background: rgba(255,255,255,0.15); padding-left:30px; }
.main-content { margin-left:240px; padding:30px; width:100%; }
.admin-header { background: linear-gradient(-45deg, var(--primary), var(--secondary)); padding:30px; color: var(--text-light); border-radius:20px; margin-bottom:25px; text-align:center; box-shadow:0 8px 24px rgba(0,0,0,0.1); }
.admin-header h1 { font-size:2rem; font-weight:700; margin-bottom:5px; }
.container-form { max-width:700px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1); }
input, select, textarea { width:100%; padding:10px; margin-bottom:15px; border-radius:8px; border:1px solid #ccc; }
button { padding:10px 20px; border:none; background:var(--secondary); color:#fff; font-weight:600; border-radius:8px; cursor:pointer; }
.error { background:#ffdddd; color:#900; padding:10px; margin-bottom:15px; border-radius:8px; }
.success { background:#ddffdd; color:#046307; padding:10px; margin-bottom:15px; border-radius:8px; }
@media(max-width:768px){ .sidebar { width:100%; height:auto; position:relative; } .main-content { margin-left:0; padding:15px; } }
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-title">Admin Panel</div>
    <a href="admin-dashboard.php">🏠 Dashboard</a>
    <a href="manage-product.php">📦 Manage Products</a>
    <a href="manage-categories.php">🗂 Categories</a>
    <a href="manage-orders.php">🛒 Orders</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="admin-header" style="background: darkgray;">
        <h1 style="color:black">➕ Add Product</h1>
        <p style="color:black">Fill in the details to add a new product</p>
    </div>

    <div class="container-form">
        <?php foreach($err as $e): ?>
            <div class="error"><?=htmlspecialchars($e)?></div>
        <?php endforeach; ?>
        <?php if($ok): ?>
            <div class="success"><?=htmlspecialchars($ok)?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="name" value="<?=htmlspecialchars($name ?? '')?>" required>

            <label>Main Category</label>
            <select name="main_category_id" required>
                <option value="0">-- Select Category --</option>
                <?php foreach($main_categories as $mc): ?>
                    <option value="<?=$mc['id']?>" <?=($main_category_id ?? 0)==$mc['id']?'selected':''?>><?=htmlspecialchars($mc['name'])?></option>
                <?php endforeach; ?>
            </select>

            <label>Brand</label>
            <select name="brand_id">
                <option value="0">-- Select Brand --</option>
                <?php foreach($brands as $b): ?>
                    <option value="<?=$b['id']?>" <?=($brand_id ?? 0)==$b['id']?'selected':''?>><?=htmlspecialchars($b['name'])?></option>
                <?php endforeach; ?>
            </select>

            <label>Gender</label>
            <select name="gender_id">
                <option value="0">-- Select Gender --</option>
                <?php foreach($genders as $g): ?>
                    <option value="<?=$g['id']?>" <?=($gender_id ?? 0)==$g['id']?'selected':''?>><?=htmlspecialchars($g['name'])?></option>
                <?php endforeach; ?>
            </select>

            <label>Price Category</label>
            <select name="price_category_id">
                <option value="0">-- Select Category --</option>
                <?php foreach($prices as $p): ?>
                    <option value="<?=$p['id']?>" <?=($price_category_id ?? 0)==$p['id']?'selected':''?>><?=htmlspecialchars($p['label'])?></option>
                <?php endforeach; ?>
            </select>

            <label>Price (₹)</label>
            <input type="number" step="0.01" name="price" value="<?=htmlspecialchars($price ?? '')?>" required>

            <label>Description</label>
            <textarea name="description" rows="4"><?=htmlspecialchars($description ?? '')?></textarea>

            <label>Product Image</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Add Product</button>
        </form>
    </div>
</div>

</body>
</html>
