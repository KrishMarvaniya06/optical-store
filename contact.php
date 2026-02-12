<?php
session_start();
require_once 'db_connect.php';

// Fetch categories for navbar
$categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

$message_sent = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        // Insert message into database
        $stmt = $mysqli->prepare("INSERT INTO contact_messages (user_name, user_email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $message_sent = true;
        } else {
            $error = "Sorry, your message could not be saved. Please try again later.";
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="bootstrap.min.css" rel="stylesheet">
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

/* Hero */
.hero{
  background:#f8f8f8;
  padding:60px 30px;
  text-align:center;
}
.hero h2{font-family:'Playfair Display',serif;font-size:36px;margin-bottom:20px;color:#111;}
.hero p{color:#666;font-size:18px;}

/* Content / Form */
.content{
  max-width:800px;
  margin:50px auto;
  background:var(--card);
  border-radius:8px;
  box-shadow:0 0 20px var(--shadow);
  padding:40px;
}
.content h3{font-family:'Playfair Display',serif;font-size:28px;margin-bottom:25px;color:var(--text);}
form input, form textarea{
  width:100%;
  padding:12px;
  margin-bottom:15px;
  border:1px solid #ccc;
  border-radius:5px;
  font-size:16px;
}
form button{
  background:var(--gold);
  color:#111;
  border:none;
  padding:12px 25px;
  border-radius:25px;
  cursor:pointer;
  font-weight:600;
  font-size:16px;
}
form button:hover{background:#c7993e;}
.success{color:green;margin-bottom:20px;}
.error{color:red;margin-bottom:20px;}

    /*** Footer ***/
    @keyframes footerAnimatedBg {
        0% {
            background-position: 0 0;
        }

        100% {
            background-position: -1000px 0;
        }
    }

    .footer {
        background-image: url(../img/footer-bg.png);
        background-position: 0px 0px;
        background-repeat: repeat-x;
        animation: footerAnimatedBg 50s linear infinite;
    }
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
</nav>

<!-- HERO -->
<section class="hero">
  <h2>Contact Us</h2>
  <p>Have questions or feedback? Reach out to us and we'll get back to you as soon as possible.</p>
</section>

<!-- FORM CONTENT -->
<div class="content">
  <h3>Send a Message</h3>

  <?php if ($message_sent): ?>
    <p class="success">Thank you! Your message has been sent successfully.</p>
  <?php elseif ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="POST" action="">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
      <button type="submit">Send Message</button>
  </form>
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
