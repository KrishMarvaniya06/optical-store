<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$catId = (int) $_GET['id'];

$catStmt = $mysqli->prepare("SELECT name FROM main_categories WHERE id = ?");
$catStmt->bind_param("i", $catId);
$catStmt->execute();
$catResult = $catStmt->get_result();

if ($catResult->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$category = $catResult->fetch_assoc();

$catQuery = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC");
$categories = $catQuery->fetch_all(MYSQLI_ASSOC);

$genders = $mysqli->query("SELECT * FROM genders ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$brands  = $mysqli->query("SELECT * FROM brands ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$prices  = $mysqli->query("SELECT * FROM price_categories ORDER BY min_price ASC")->fetch_all(MYSQLI_ASSOC);

$where = "main_category_id = ?";
$types = "i";
$params = [$catId];

if (!empty($_GET['gender'])) {
    $where .= " AND gender_id = ?";
    $types .= "i";
    $params[] = (int)$_GET['gender'];
}
if (!empty($_GET['brand'])) {
    $where .= " AND brand_id = ?";
    $types .= "i";
    $params[] = (int)$_GET['brand'];
}
if (!empty($_GET['price'])) {
    $priceStmt = $mysqli->prepare("SELECT min_price, max_price FROM price_categories WHERE id=?");
    $priceStmt->bind_param("i", $_GET['price']);
    $priceStmt->execute();
    $priceRes = $priceStmt->get_result()->fetch_assoc();
    $priceStmt->close();
    if ($priceRes) {
        $where .= " AND price >= ? AND price <= ?";
        $types .= "dd";
        $params[] = $priceRes['min_price'];
        $params[] = $priceRes['max_price'];
    }
}

$sql = "SELECT * FROM products WHERE $where";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($category['name']) ?> — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="bootstrap.min.css">
<style>
:root {
--bg:#F6F5F3;
--card:#FFFFFF;
--text:#111111;
--muted:#666666;
--gold:#C8A062;
--shadow:rgba(0,0,0,0.12);
--overlay-dark:rgba(0,0,0,0.5);
}
*{box-sizing:border-box;}
body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
.navbar{background:#111;}
.navbar .navbar-nav .nav-link{margin-right:25px;color:#fff;}
.navbar .navbar-nav .nav-link.active,
.navbar .navbar-nav .nav-link:hover{color:#ff69b4;}
.page-wrap{max-width:1400px;margin:auto;padding:40px 20px;}
.category-header{text-align:center;margin-bottom:30px;}
.category-header h1{font-family:'Playfair Display',serif;font-size:2.6rem;font-weight:600;margin-bottom:12px;}
.category-header p{color:var(--muted);font-size:1rem;}
.filters{display:flex;gap:15px;margin-bottom:30px;flex-wrap:wrap;}
.filters select{padding:10px;border-radius:8px;border:1px solid #ddd;font-weight:600;background:#fff;cursor:pointer;}
.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:30px;}
.product-card{position:relative;background:var(--card);border-radius:22px;overflow:hidden;box-shadow:0 18px 40px var(--shadow);transition:transform 0.3s ease, box-shadow 0.3s ease;}
.product-card:hover{transform:translateY(-6px);box-shadow:0 24px 50px var(--shadow);}
.product-card img{width:100%;height:260px;object-fit:cover;}
.image-overlay{position:absolute;inset:0;background:var(--overlay-dark);display:flex;justify-content:center;align-items:center;opacity:0;transition:.3s;gap:10px;}
.product-card:hover .image-overlay{opacity:1;}
.add-btn{padding:10px 18px;border-radius:25px;font-weight:600;text-decoration:none;}
.product-info{padding:16px;text-align:center;}
.product-title{font-weight:600;font-size:1.15rem;}
.product-price{font-weight:600;color:var(--gold);}
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark px-lg-5 position-relative">
<a href="index.php" class="navbar-brand d-flex align-items-center">
<img src="assets/images/FullLogo1.png" alt="Lumineux Logo" style="height:42px; margin-right:10px;">
<div class="brand-small">Lumineux Opticals</div>
</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarCollapse">
<div class="navbar-nav mx-auto p-4 p-lg-0">
<a href="index.php" class="nav-item nav-link active">HOME</a>
<a href="about.php" class="nav-item nav-link">ABOUT</a>

<div class="nav-item dropdown">
<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">CATEGORIES</a>
<ul class="dropdown-menu">
<?php foreach($categories as $cat): ?>
<li><a class="dropdown-item" href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
<?php endforeach; ?>
</ul>
</div>

<a href="contact.php" class="nav-item nav-link">CONTACT</a>
</div>

<div class="d-flex align-items-center ms-auto" style="gap:14px;">
<a href="cart.php" style="width:42px;height:42px;border-radius:50%;
display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.12);color:#fff;text-decoration:none;">
<i class="fa-solid fa-bag-shopping"></i></a>

<a href="wishlist.php" style="width:42px;height:42px;border-radius:50%;
display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.12);color:#ff69b4;text-decoration:none;">
<i class="fa-solid fa-heart"></i></a>

<a href="index.php" class="btn btn-outline-light">← Back to Home</a>
</div>
</div>
</nav>

<div class="page-wrap">
<div class="category-header">
<h1 style="color: black;"><?= htmlspecialchars($category['name']) ?></h1>
<p style="color: black; font-size:20px;">Explore premium frames crafted for your unique style.</p>
</div>

<div class="filters">
<form method="GET" id="filterForm" style="display:flex;gap:20px;flex-wrap:wrap;width:100%;">
<input type="hidden" name="id" value="<?= $catId ?>">

<select name="gender" onchange="this.form.submit()">
<option value="">All Genders</option>
<?php foreach($genders as $g): ?>
<option value="<?= $g['id'] ?>" <?= ($_GET['gender']??'')==$g['id']?'selected':'' ?>><?= $g['name'] ?></option>
<?php endforeach; ?>
</select>

<select name="brand" onchange="this.form.submit()">
<option value="">All Brands</option>
<?php foreach($brands as $b): ?>
<option value="<?= $b['id'] ?>" <?= ($_GET['brand']??'')==$b['id']?'selected':'' ?>><?= $b['name'] ?></option>
<?php endforeach; ?>
</select>

<select name="price" onchange="this.form.submit()">
<option value="">All Prices</option>
<?php foreach($prices as $p): ?>
<option value="<?= $p['id'] ?>" <?= ($_GET['price']??'')==$p['id']?'selected':'' ?>><?= $p['label'] ?></option>
<?php endforeach; ?>
</select>

</form>
</div>

<div class="product-grid">
<?php while($row = $products->fetch_assoc()): ?>
<?php $imageFile = (!empty($row['image_path']) && file_exists($row['image_path'])) ? $row['image_path'] : "assets/images/default.jpg"; ?>
<div class="product-card">
<img src="<?= $imageFile ?>">

<div class="image-overlay">
    <a href="product-detail.php?id=<?= $row['id'] ?>" 
       class="add-btn"
       style="background:#ff69b4;color:#fff;">
       View Product
    </a>

    <a href="buy-now.php?id=<?= $row['id'] ?>&buynow=1" 
       class="add-btn"
       style="background:#28a745;color:#fff;">
       Buy Now
    </a>
</div>

<div class="product-info">
<div class="product-title"><?= htmlspecialchars($row['name']) ?></div>
<div class="product-price">₹<?= number_format($row['price']) ?></div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>

<!-- FOOTER -->
<footer style="background:#000; padding:80px 0 40px; color:#fff;">
    <div class="container">
        <div class="row">

            <!-- Column 1: Logo + Brand + Info -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h4 style="color:#ff69b4; font-weight:700;">Lumineux Opticals</h4>
                <p style="color:#ccc;">
                    Discover premium eyewear crafted for clarity, comfort, and unmatched luxury. 
                    Elevate your vision with style.
                </p>
                <div class="footer-social mt-3" style="display:flex; gap:15px;">
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Column 2: About -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">About</h5>
                <p style="color:#ccc;">
                    Lumineux Opticals offers high-quality frames and lenses to elevate your style while ensuring clarity and comfort.
                </p>
            </div>

            <!-- Column 4: Quick Links -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">Quick Links</h5>
                <ul style="list-style:none; padding:0;">
                    <li><a href="index.php" style="color:#ccc; text-decoration:none;">Home</a></li>
                    <li><a href="about.php" style="color:#ccc; text-decoration:none;">About</a></li>
                    <li><a href="category.php" style="color:#ccc; text-decoration:none;">Category</a></li>
                    <li><a href="contact.php" style="color:#ccc; text-decoration:none;">Contact</a></li>
                </ul>
            </div>


            <!-- Column 3: Contact -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">Contact Us</h5>
                <p style="color:#ccc;">
                    <i class="fas fa-map-marker-alt" style="margin-right:8px;"></i> Ahmedabad, Gujarat, India<br>
                    <i class="fas fa-phone" style="margin-right:8px;"></i> +91 98765 43210<br>
                    <i class="fas fa-envelope" style="margin-right:8px;"></i> support@lumineux.com
                </p>
            </div>

            
        </div>

        <hr style="border-color:#333; margin:30px auto; width:80%;">

        <p class="text-center text-muted" style="color:#777;">
            © 2026 Lumineux Opticals — All Rights Reserved.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>