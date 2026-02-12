<?php
session_start();
require_once 'db_connect.php';

// Fetch all categories for navbar
$catQuery = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC");
$categories = $catQuery->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>About Us — Lumineux Opticals</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="bootstrap.min.css">

  <style>
    :root {
      --bg:#F6F5F3;
      --text:#111;
      --muted:#666;
      --gold:#ff69b4;
    }

    *{box-sizing:border-box;}
    body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}

    /* Navbar */
    .navbar{background:#111;}
    .navbar .navbar-nav .nav-link{margin-right:25px;color:#fff;}
    .navbar .navbar-nav .nav-link.active,
    .navbar .navbar-nav .nav-link:hover{color:var(--gold);}

    /* Page header */
    .page-header{
      padding:100px 20px 50px;
      background:#000;
      color:#fff;
      text-align:center;
    }
    .page-header h1{color:var(--gold); font-size:3rem; font-weight:700; margin-bottom:15px;}
    .page-header p{color:#ccc; font-size:1.1rem; max-width:700px; margin:auto;}

    /* About content */
    .about-section{
      padding:60px 20px;
      max-width:1200px;
      margin:auto;
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
      gap:40px;
      align-items:center;
    }
    .about-section img{width:100%; border-radius:15px; object-fit:cover;}
    .about-section .about-text h2{color:var(--gold); font-weight:700; margin-bottom:20px;}
    .about-section .about-text p{color:#666; font-size:1.05rem; line-height:1.8; margin-bottom:15px;}
    .about-section .about-text a{display:inline-block; margin-top:15px; background:var(--gold); color:#111; font-weight:600; padding:12px 25px; border-radius:30px; text-decoration:none; transition:0.3s;}
    .about-section .about-text a:hover{opacity:0.9;}
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
      <a href="index.php" class="nav-item nav-link">Home</a>
      <a href="about.php" class="nav-item nav-link active">About</a>

      <!-- Categories Dropdown -->
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
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <h1>About Us</h1>
  <p>Discover the story behind Lumineux Opticals and our commitment to premium eyewear crafted with style and comfort.</p>
</div>

<!-- ABOUT CONTENT -->
<section class="about-section">
  <img src="assets/images/luxury-img_550.jpg" alt="About Lumineux Opticals">
  <div class="about-text">
    <h2>Our Story</h2>
    <p>Lumineux Opticals is formed from the collaboration of two established mother showrooms: <strong>Amee Optical</strong> and <strong>Clear Vision</strong>. This union brings together decades of experience in providing premium eyewear with style and comfort.</p>

<p><strong>Amee Optical</strong> operates 3 branches: one in Rajkot and two in Junagadh, serving customers with dedication and expertise.</p>

<p><strong>Clear Vision</strong> has 2 branches, both located in Rajkot, known for offering high-quality eyewear and personalized service.</p>

<p>Together, these two brands combine their legacy and passion to create <strong>Lumineux Opticals</strong>, delivering premium eyewear designed for clarity, elegance, and unmatched quality.</p>

  </div>
</section>

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
