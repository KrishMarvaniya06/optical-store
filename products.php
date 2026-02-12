<?php
session_start();
require_once 'db_connect.php';

// Fetch filter lists
$brands = $mysqli->query("SELECT * FROM brands ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$genders = $mysqli->query("SELECT * FROM genders ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$priceCats = $mysqli->query("SELECT * FROM price_categories ORDER BY min_price ASC")->fetch_all(MYSQLI_ASSOC);

// Get filter values from GET
$brand_id = isset($_GET['brand']) && $_GET['brand'] !== '' ? (int)$_GET['brand'] : null;
$gender_id = isset($_GET['gender']) && $_GET['gender'] !== '' ? (int)$_GET['gender'] : null;
$price_cat_id = isset($_GET['price']) && $_GET['price'] !== '' ? (int)$_GET['price'] : null;
$order = isset($_GET['order']) && $_GET['order'] === 'low' ? 'ASC' : (isset($_GET['order']) && $_GET['order'] === 'high' ? 'DESC' : null);

// Build SQL
$sql = "SELECT p.*, b.name AS brand, g.name AS gender, pc.label AS price_label 
        FROM products p
        LEFT JOIN brands b ON p.brand_id=b.id
        LEFT JOIN genders g ON p.gender_id=g.id
        LEFT JOIN price_categories pc ON p.price_category_id=pc.id
        WHERE 1=1";
$params = [];
$types = '';

if ($brand_id) {
    $sql .= " AND p.brand_id = ?";
    $params[] = $brand_id;
    $types .= 'i';
}
if ($gender_id) {
    $sql .= " AND p.gender_id = ?";
    $params[] = $gender_id;
    $types .= 'i';
}
if ($price_cat_id) {
    $sql .= " AND ((pc.min_price IS NULL OR p.price >= pc.min_price) 
                  AND (pc.max_price IS NULL OR p.price <= pc.max_price) 
                  AND pc.id = ?)";
    $params[] = $price_cat_id;
    $types .= 'i';
}

$sql .= $order ? " ORDER BY p.price $order" : " ORDER BY p.created_at DESC";

$stmt = $mysqli->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$products = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Products — Lumineux</title>
<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --emerald: #046307;
    --gold: #D4AF37;
    --light-gray: #f9f9f9;
}

/* Reset */
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }

body { background: var(--light-gray); }
.navbar{background:#111;}
.navbar .nav-link{color:#fff;margin-right:25px;}
.navbar .nav-link:hover,.navbar .nav-link.active{color:#ff69b4;}
/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px;
    background: #fff;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
}

.header strong { font-size: 1.3rem; color: var(--emerald); }

.home-btn-gold {
    padding: 8px 18px;
    background: var(--gold);
    color: #000;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: .25s;
}
.home-btn-gold:hover { background: #b9952f; }

/* Container */
.container { max-width: 1100px; margin: 24px auto; padding: 0 16px; }

/* Filters */
.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
}

select, button {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.1);
    outline: none;
    font-size: 0.95rem;
    transition: all 0.2s;
}

select:focus, button:hover { border-color: var(--emerald); }

button[type="submit"] { background: var(--emerald); color: #fff; cursor: pointer; border: none; }
button[type="submit"]:hover { background: #034d05; }

button[type="button"] { background: #ccc; color: #333; }
button[type="button"]:hover { background: #999; color: #fff; }

/* Product Grid */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.1); }

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
}

.card h3 { margin: 12px; font-size: 1.1rem; color: #333; }
.card .details { margin: 0 12px; font-size: 0.9rem; color: #666; }
.card .price { font-weight: 700; color: var(--emerald); margin: 8px 12px; font-size: 1.1rem; }

/* Add to Cart Form */
.add-cart-form {
    display: flex;
    gap: 8px;
    margin: 12px;
}
.add-cart-form input[type=number] {
    width: 50px;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.add-cart-form button {
    flex: 1;
    background: var(--emerald);
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s;
}
.add-cart-form button:hover { background: #034d05; }

/* Empty State */
.empty {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 1.1rem;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark px-lg-5">
  <a href="index.php" class="navbar-brand d-flex align-items-center">
    <img src="assets/images/FullLogo1.png" style="height:42px;margin-right:10px;">
    Lumineux Opticals
  </a>

  <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="nav">
    <div class="navbar-nav mx-auto">
      <a href="index.php" class="nav-link">Home</a>
      <a href="about.php" class="nav-link">About</a>

      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Categories</a>
        <ul class="dropdown-menu">
          <?php foreach($categories as $c): ?>
            <li>
              <a class="dropdown-item" href="category.php?id=<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <a href="contact.php" class="nav-link">Contact</a>
    </div>
  </div>

  <div class="d-flex align-items-center ms-auto" style="gap:14px;">
    <a href="cart.php" style="width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.12);color:#fff;">
      <i class="fa-solid fa-bag-shopping"></i>
    </a>

    <a href="wishlist.php" style="width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.12);color:#ff69b4;">
      <i class="fa-solid fa-heart"></i>
    </a>

    <a href="category.php" class="btn btn-outline-light">← Back to Home</a>
  </div>
</nav>


<div class="container">
    <h2 style="color:black";>Shop</h2>

    <!-- Filters -->
    <form method="get" class="form-inline">
        <div class="filters">
            <select name="brand">
                <option value="">All Brands</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= (int)$b['id'] ?>" <?= $brand_id === (int)$b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="gender">
                <option value="">All Genders</option>
                <?php foreach ($genders as $g): ?>
                    <option value="<?= (int)$g['id'] ?>" <?= $gender_id === (int)$g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="price">
                <option value="">All Prices</option>
                <?php foreach ($priceCats as $pc): ?>
                    <option value="<?= (int)$pc['id'] ?>" <?= $price_cat_id === (int)$pc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pc['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="order">
                <option value="">Sort</option>
                <option value="low" <?= isset($_GET['order']) && $_GET['order']=='low' ? 'selected' : '' ?>>Price: Low → High</option>
                <option value="high" <?= isset($_GET['order']) && $_GET['order']=='high' ? 'selected' : '' ?>>Price: High → Low</option>
            </select>

            <button type="submit">Apply</button>
            <a href="products.php"><button type="button">Reset</button></a>
        </div>
    </form>

    <!-- Products -->
    <?php if (!$products): ?>
        <div class="empty">No products found for selected filters.</div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($products as $p): ?>
    <div class="card">
        <a href="product-detail.php?id=<?= $p['id'] ?>" style="text-decoration:none; color:inherit;">
            <?php if ($p['image_path'] && file_exists(__DIR__ . '/' . $p['image_path'])): ?>
                <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php else: ?>
                <div style="height:180px;background:#f2f2f2;display:flex;align-items:center;justify-content:center;color:#999;">
                    No image
                </div>
            <?php endif; ?>
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <div class="details"><?= htmlspecialchars($p['brand'] ?? '') ?> • <?= htmlspecialchars($p['gender'] ?? '') ?></div>
            <div class="price">₹<?= number_format($p['price'],2) ?></div>
        </a>

        <!-- Add to Cart Form -->
        <form class="add-cart-form" method="post" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <button type="submit">Add to Cart</button>
        </form>
    </div>
<?php endforeach; ?>

        </div>
    <?php endif; ?>
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
