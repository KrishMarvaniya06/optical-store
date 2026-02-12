<?php
session_start();
require_once 'db_connect.php';

// Fetch categories for navbar
$categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Get cart items from session
$cart_ids = $_SESSION['cart'] ?? [];
$cart_items = [];

if (!empty($cart_ids)) {
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
    $stmt = $mysqli->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($cart_ids));
    $stmt->bind_param($types, ...array_keys($cart_ids));
    $stmt->execute();
    $cart_items = $stmt->get_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
    --bg:#F6F5F3;
    --card:#FFFFFF;
    --text:#111111;
    --muted:#666666;
    --gold:#C8A062;
    --shadow:rgba(0,0,0,0.12);
}
*{box-sizing:border-box;}
body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}

/* Navbar */
.navbar{background:#111;}
.navbar .navbar-nav .nav-link{margin-right:25px;color:#fff;}
.navbar .navbar-nav .nav-link.active,
.navbar .navbar-nav .nav-link:hover{color:#ff69b4;}
.navbar .dropdown-menu{background:#111;}
.navbar .dropdown-item{color:#fff;}
.navbar .dropdown-item:hover{background:#ff69b4;color:#111;}

/* Page wrap */
.page-wrap{max-width:1400px;margin:auto;padding:40px 20px;}

/* Cart header */
.cart-header{text-align:center;margin-bottom:50px;}
.cart-header h1{font-family:'Playfair Display',serif;font-size:2.6rem;font-weight:600;margin-bottom:12px;}
.cart-header p{color:var(--muted);font-size:1rem;}

/* Product grid */
.product-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:30px;
}

.product-card{
    position:relative;
    background:var(--card);
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 18px 40px var(--shadow);
    transition:transform 0.3s ease, box-shadow 0.3s ease;
}
.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 24px 50px var(--shadow);
}
.product-card img{
    width:100%;
    height:260px;
    object-fit:cover;
    display:block;
    transition:transform 0.3s ease;
}
.product-card:hover img{transform:scale(1.05);}

.image-overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.5);
    display:flex;
    justify-content:center;
    align-items:center;
    opacity:0;
    transition:opacity 0.3s ease;
}
.product-card:hover .image-overlay{opacity:1;}

.remove-btn, .checkout-btn{
    background:var(--gold);
    color:#111;
    border:none;
    padding:10px 18px;
    border-radius:25px;
    font-weight:600;
    cursor:pointer;
    font-size:0.95rem;
    transition:transform 0.2s ease;
    margin:0 5px;
}
.remove-btn:hover, .checkout-btn:hover{transform:scale(1.05);}

.product-info{padding:16px;text-align:center;}
.product-title{font-family:'Playfair Display',serif;font-weight:600;font-size:1.15rem;margin-bottom:6px;color:var(--text);}
.product-price{font-weight:600;color:var(--gold);font-size:1.1rem;}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-lg-5">
  <a href="index.php" class="navbar-brand d-flex align-items-center">
    <img src="assets/images/FullLogo1.png" alt="Lumineux Logo" style="height:42px; margin-right:10px;">
    <div class="brand-small">Lumineux Opticals</div>
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav mx-auto p-4 p-lg-0">
      <a href="index.php" class="nav-item nav-link active">Home</a>
      <a href="about.php" class="nav-item nav-link">About</a>

      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Categories
        </a>
        <ul class="dropdown-menu">
          <?php if(!empty($categories)): ?>
            <?php foreach($categories as $cat): ?>
              <li><a class="dropdown-item" href="category.php?id=<?= intval($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span class="dropdown-item">No categories</span></li>
          <?php endif; ?>
        </ul>
      </div>

      <a href="contact.php" class="nav-item nav-link">Contact</a>
    </div>
  </div>
  <!-- Right side: Back to Home button -->
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
</nav>

<!-- PAGE CONTENT -->
<div class="page-wrap">
  <div class="cart-header">
    <h1 style="color:black;">My Cart</h1>
    <p style="color:black; font-size: 20px;">Review your selected products and proceed to checkout</p>
  </div>

  <div class="product-grid">
    <?php if (!empty($cart_items) && $cart_items->num_rows > 0): ?>
      <?php while($row = $cart_items->fetch_assoc()): ?>
        <?php
          $imageFile = (!empty($row['image_path']) && file_exists($row['image_path']))
                       ? $row['image_path']
                       : "assets/images/default.jpg";
        ?>
        <div class="product-card">
          <img src="<?= htmlspecialchars($imageFile) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="image-overlay">
            <!-- Remove from cart -->
            <form method="post" action="remove_cart.php" style="display:inline-block;">
              <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
              <button type="submit" class="remove-btn">Remove</button>
            </form>
            <!-- Proceed to checkout -->
            <form method="post" action="checkout.php" style="display:inline-block;">
              <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="quantity" value="<?= $row['quantity'] ?? 1 ?>">
              <button type="submit" class="checkout-btn">Proceed to Checkout</button>
            </form>
          </div>
          <div class="product-info">
            <div class="product-title"><?= htmlspecialchars($row['name']) ?></div>
            <div class="product-price">₹<?= number_format($row['price']) ?></div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="grid-column:1/-1;text-align:center;color:black;font-size: 20px;">Your cart is empty 😊</p>
    <?php endif; ?>
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
