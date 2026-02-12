<?php
session_start();
require_once 'db_connect.php'; // Database connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';
$pass_success = '';
$pass_error = '';

// Fetch current user data
$stmt = $mysqli->prepare("SELECT username, email, password FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit;
}

// Handle profile update (username/email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$username || !$email) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt_check = $mysqli->prepare("SELECT id FROM user WHERE (username = ? OR email = ?) AND id != ?");
        $stmt_check->bind_param("ssi", $username, $email, $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username or email already taken by another user.";
        } else {
            $stmt_update = $mysqli->prepare("UPDATE user SET username = ?, email = ? WHERE id = ?");
            $stmt_update->bind_param("ssi", $username, $email, $user_id);
            if ($stmt_update->execute()) {
                $success = "Profile updated successfully!";
                $user['username'] = $username;
                $user['email'] = $email;
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
        $stmt_check->close();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if (!$current_pass || !$new_pass || !$confirm_pass) {
        $pass_error = "All password fields are required.";
    } elseif (!password_verify($current_pass, $user['password'])) {
        $pass_error = "Current password is incorrect.";
    } elseif ($new_pass !== $confirm_pass) {
        $pass_error = "New password and confirmation do not match.";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt_update = $mysqli->prepare("UPDATE user SET password = ? WHERE id = ?");
        $stmt_update->bind_param("si", $hashed_pass, $user_id);
        if ($stmt_update->execute()) {
            $pass_success = "Password changed successfully!";
        } else {
            $pass_error = "Failed to update password. Please try again.";
        }
    }
}

// Fetch categories for navbar
$categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<style>
:root {
    --bg:#F6F5F3;
    --card:#FFFFFF;
    --text:#111111;
    --muted:#666666;
    --gold:#C8A062;
    --shadow:rgba(0,0,0,0.12);
}

/* General */
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
.hero p{color:var(--muted);font-size:18px;}

/* Profile container */
.profile-container{
  max-width:600px;
  margin:50px auto;
  background:var(--card);
  border-radius:12px;
  box-shadow:0 0 20px var(--shadow);
  padding:40px;
}
.profile-container h2{text-align:center;font-family:'Playfair Display',serif;font-size:28px;margin-bottom:30px;color:var(--text);}
.profile-item{margin-bottom:20px;}
.profile-item label{display:block;font-weight:600;margin-bottom:5px;color:var(--text);}
.profile-item input{width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;font-size:16px;}
.btn{
  display:block;
  width:100%;
  padding:12px;
  margin-top:20px;
  background:var(--gold);
  color:#111;
  border:none;
  border-radius:25px;
  font-weight:600;
  cursor:pointer;
  font-size:16px;
}
.btn:hover{background:#c7993e;}
.success{color:green;margin-bottom:15px;text-align:center;}
.error{color:red;margin-bottom:15px;text-align:center;}

/* Footer */
footer{
  background:#111;
  color:#fff;
  text-align:center;
  padding:40px 20px;
  margin-top:50px;
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
    <div class="d-flex ms-auto">
      <a href="index.php" class="btn btn-outline-light">← Back to Home</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <h2>Edit Profile</h2>
  <p>Manage your account details and update your password securely.</p>
</section>

<!-- PROFILE FORM -->
<div class="profile-container">
    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <input type="hidden" name="update_profile" value="1">
        <div class="profile-item">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="profile-item">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>

    <hr>

    <?php if($pass_success) echo "<p class='success'>$pass_success</p>"; ?>
    <?php if($pass_error) echo "<p class='error'>$pass_error</p>"; ?>

    <form method="POST" action="">
        <input type="hidden" name="change_password" value="1">
        <div class="profile-item">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="profile-item">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="profile-item">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Change Password</button>
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
