<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$productId = (int) $_GET['id'];

// Fetch product
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$productResult = $stmt->get_result();

if ($productResult->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$product = $productResult->fetch_assoc();

// Fetch categories for navbar
$catQuery = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC");
$categories = $catQuery->fetch_all(MYSQLI_ASSOC);

// Fetch genders, brands, price_categories
$genders = $mysqli->query("SELECT * FROM genders ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$brands  = $mysqli->query("SELECT * FROM brands ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$prices  = $mysqli->query("SELECT * FROM price_categories ORDER BY min_price ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch wishlist
$wishlist_ids = [];
if ($user_id) {
    $wlQuery = $mysqli->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wlQuery->bind_param("i", $user_id);
    $wlQuery->execute();
    $wlResult = $wlQuery->get_result();
    while ($wl = $wlResult->fetch_assoc()) {
        $wishlist_ids[] = $wl['product_id'];
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($product['name']) ?> — Lumineux Opticals</title>
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
}

body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
.navbar{background:#111;}
.navbar .navbar-nav .nav-link{margin-right:25px;color:#fff;}
.navbar .navbar-nav .nav-link.active,
.navbar .navbar-nav .nav-link:hover{color:#ff69b4;}
.page-wrap{max-width:1400px;margin:auto;padding:40px 20px;}

.product-detail-wrapper{
    display:flex;
    gap:30px;
    flex-wrap:wrap;
    background:var(--card);
    padding:20px;
    border-radius:20px;
    box-shadow:0 10px 30px var(--shadow);
}

.product-detail-wrapper img{
    width:300px;
    max-width:100%;
    border-radius:12px;
    object-fit:cover;
}

.product-detail-info{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:flex-start;
}

.product-detail-info h1{
    font-family:'Playfair Display',serif;
    font-size:1.8rem;
    font-weight:600;
    margin-bottom:10px;
}

.product-detail-info p{
    margin:5px 0;
    color:var(--muted);
}

.product-detail-info .price{
    color:var(--gold);
    font-weight:600;
    font-size:1.3rem;
    margin:10px 0 15px 0;
}

.product-detail-info form{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:10px;
}

.add-cart-btn, .add-wishlist-btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 140px;
    padding: 10px 0;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.95rem;
    color: #111;
    border: none;
    background: var(--gold);
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
    text-align: center;
    white-space: nowrap;
}

.add-cart-btn:hover,
.add-wishlist-btn:hover {
    transform: scale(1.05);
    background: #C8A062;
}

.add-wishlist-btn.disabled {
    background: #ccc;
    cursor: not-allowed;
}

@media(max-width:768px){
    .product-detail-wrapper{flex-direction:column;}
    .product-detail-wrapper img{width:100%;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
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
      <a href="index.php" class="nav-item nav-link">HOME</a>
      <a href="about.php" class="nav-item nav-link">ABOUT</a>

      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          CATEGORIES
        </a>
        <ul class="dropdown-menu">
          <?php foreach($categories as $cat): ?>
            <li><a class="dropdown-item" href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <a href="contact.php" class="nav-item nav-link">CONTACT</a>
    </div>

    <div class="d-flex align-items-center ms-auto" style="gap:14px;">

    <a href="cart.php"
       style="width:42px;height:42px;border-radius:50%;
              display:flex;align-items:center;justify-content:center;
              background:rgba(255,255,255,0.12);
              color:#fff;text-decoration:none;
              transition:0.2s;">
        <i class="fa-solid fa-bag-shopping"></i>
    </a>

    <a href="wishlist.php"
       style="width:42px;height:42px;border-radius:50%;
              display:flex;align-items:center;justify-content:center;
              background:rgba(255,255,255,0.12);
              color:#ff69b4;text-decoration:none;
              transition:0.2s;">
        <i class="fa-solid fa-heart"></i>
    </a>

    <a href="category.php" class="btn btn-outline-light">← Back to Home</a>
</div>

  </div>
</nav>

<div class="page-wrap">

  <div class="product-detail-wrapper">

    <!-- Left: Image -->
    <div>
      <img src="<?= file_exists($product['image_path']) ? $product['image_path'] : 'assets/images/default.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <!-- Right: Info -->
    <div class="product-detail-info">
      <h1><?= htmlspecialchars($product['name']) ?></h1>
      <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand_id'] ? $brands[array_search($product['brand_id'], array_column($brands,'id'))]['name'] : 'N/A') ?></p>
      <p><strong>Gender:</strong> <?= htmlspecialchars($product['gender_id'] ? $genders[array_search($product['gender_id'], array_column($genders,'id'))]['name'] : 'N/A') ?></p>
      <p><strong>Price Category:</strong> <?= htmlspecialchars($product['price_category_id'] ? $prices[array_search($product['price_category_id'], array_column($prices,'id'))]['label'] : 'N/A') ?></p>
      <div class="price">₹<?= number_format($product['price']) ?></div>
      <p><?= nl2br(htmlspecialchars($product['description'] ?: 'No description available.')) ?></p>

      <form method="post" action="add_to_cart.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <button type="submit" class="add-cart-btn">Add to Cart</button>
      </form>
      
      <?php if ($user_id): ?>
      <form method="post" action="add_to_wishlist.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <button type="submit" class="add-wishlist-btn">
    Add to Wishlist
</button>

      </form>
      <?php else: ?>
      <a href="login.php" class="add-wishlist-btn" style="margin-top:5px; display:inline-block;">Add to Wishlist</a>
      <?php endif; ?>

    </div>

  </div>

</div>

<!-- FOOTER -->
<footer style="background:#000; padding:80px 0 40px; color:#fff;">
  <div class="container">
      <div class="row">
          <div class="col-lg-3 col-md-6 mb-4">
              <h4 style="color:#ff69b4; font-weight:700;">Lumineux Opticals</h4>
              <p style="color:#ccc;">Discover premium eyewear crafted for clarity, comfort, and unmatched luxury. Elevate your vision with style.</p>
              <div class="footer-social mt-3" style="display:flex; gap:15px;">
                  <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-facebook-f"></i></a>
                  <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-instagram"></i></a>
                  <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-twitter"></i></a>
                  <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-youtube"></i></a>
              </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
              <h5 style="font-weight:700; color:#ff69b4;">About</h5>
              <p style="color:#ccc;">Lumineux Opticals offers high-quality frames and lenses to elevate your style while ensuring clarity and comfort.</p>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
              <h5 style="font-weight:700; color:#ff69b4;">Quick Links</h5>
              <ul style="list-style:none; padding:0;">
                  <li><a href="index.php" style="color:#ccc; text-decoration:none;">Home</a></li>
                  <li><a href="about.php" style="color:#ccc; text-decoration:none;">About</a></li>
                  <li><a href="category.php" style="color:#ccc; text-decoration:none;">Category</a></li>
                  <li><a href="contact.php" style="color:#ccc; text-decoration:none;">Contact</a></li>
              </ul>
          </div>
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
      <p class="text-center text-muted" style="color:#777;">© 2026 Lumineux Opticals — All Rights Reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- SWEETALERT POPUP -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get("added_to_cart")) {
        Swal.fire({
            icon: 'success',
            title: 'Added to Cart!',
            text: 'Item successfully added to your cart 😊',
            timer: 2000,
            showConfirmButton: false
        });
    }

    if (urlParams.get("already_in_cart")) {
        Swal.fire({
            icon: 'info',
            title: 'Already in Cart!',
            text: 'Item is already added to your cart 😊',
            timer: 2000,
            showConfirmButton: false
        });
    }

    if (urlParams.get("added_to_wishlist")) {
        Swal.fire({
            icon: 'success',
            title: 'Added to Wishlist!',
            text: 'You can view it later 💖',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (urlParams.get("already_in_wishlist")) {
    Swal.fire({
        icon: 'info',
        title: 'Already in Wishlist!',
        text: 'This item is already saved 💖',
        timer: 2000,
        showConfirmButton: false
    });
}
});
</script>


</body>
</html>
