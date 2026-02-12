<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db_connect.php';

/* FORCE LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* FETCH LOGGED IN USER (for autofill) */
$user = [
    'first_name' => '',
    'last_name'  => '',
    'email'      => ''
];

$uid = (int)$_SESSION['user_id'];

$stmtUser = $mysqli->prepare("SELECT username, email FROM user WHERE id=?");
$stmtUser->bind_param("i", $uid);
$stmtUser->execute();
$resUser = $stmtUser->get_result();

if($resUser && $resUser->num_rows){
    $u = $resUser->fetch_assoc();
    $user['first_name'] = $u['username'];
    $user['last_name']  = '';
    $user['email']      = $u['email'];
}

/* FETCH CATEGORIES FOR NAVBAR */
$categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")
    ->fetch_all(MYSQLI_ASSOC);

/* CART CHECK */
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    header("Location: cart.php");
    exit;
}

/* UPDATE QTY (+ / -) */
if(isset($_GET['update_qty'], $_GET['pid'], $_GET['qty'])){

    $pid = (int)$_GET['pid'];
    $qty = max(1,(int)$_GET['qty']);

    if(isset($_SESSION['cart'][$pid])){
        $_SESSION['cart'][$pid] = $qty;
    }

    header("Location: checkout.php");
    exit;
}

/* REMOVE ITEM */
if(isset($_GET['remove_item'], $_GET['pid'])){

    $pid = (int)$_GET['pid'];

    if(isset($_SESSION['cart'][$pid])){
        unset($_SESSION['cart'][$pid]);
    }

    if(empty($_SESSION['cart'])){
        header("Location: cart.php");
        exit;
    }

    header("Location: checkout.php");
    exit;
}

/* FETCH CART PRODUCTS */
$cart = $_SESSION['cart'];

$ids = implode(',', array_map('intval', array_keys($cart)));
$res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");

$items = [];
$subtotal = 0;

while ($row = $res->fetch_assoc()) {
    $row['qty']  = $cart[$row['id']];
    $row['line'] = $row['price'] * $row['qty'];
    $subtotal   += $row['line'];
    $items[]     = $row;
}

$gst   = round($subtotal * 0.05, 2);
$grand = round($subtotal + $gst, 2);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required = ['first_name','last_name','email','phone','address','city','state','zip'];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $error = "Please fill all required fields.";
            break;
        }
    }

    if (!$error) {

        $_SESSION['checkout'] = [
            'name'    => $_POST['first_name'].' '.$_POST['last_name'],
            'email'   => $_POST['email'],
            'phone'   => $_POST['phone'],
            'address' => $_POST['address'].', '.$_POST['city'].' - '.$_POST['zip'],
            'total'   => $grand
        ];

        header("Location: payment.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Checkout — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --bg:#F6F5F3;
  --card:#fff;
  --text:#111;
  --muted:#666;
  --gold:#C8A062;
  --shadow:rgba(0,0,0,.12);
}
body{background:var(--bg);font-family:'Poppins',sans-serif;color:var(--text);}
.navbar{background:#111;}
.navbar .nav-link{color:#fff;margin-right:25px;}
.navbar .nav-link:hover,.navbar .nav-link.active{color:#ff69b4;}
.dropdown-menu{background:#111;}
.dropdown-item{color:#fff;}
.dropdown-item:hover{background:#ff69b4;color:#111;}
.page-wrap{max-width:1400px;margin:auto;padding:40px 20px;}
.checkout-title{text-align:center;margin-bottom:40px;}
.checkout-title h1{font-family:'Playfair Display',serif;font-size:2.6rem;color: black;}
.checkout-title p{color:var(--muted);}
.checkout-grid{display:grid;grid-template-columns:2fr 1fr;gap:30px;}
.card-box{background:var(--card);border-radius:22px;padding:25px;box-shadow:0 18px 40px var(--shadow);}
input{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;margin-bottom:15px;}
.row-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;}
.product{display:flex;gap:15px;margin-bottom:15px;align-items:flex-start;}
.product img{width:80px;height:80px;object-fit:cover;border-radius:12px;}
.product strong{display:block;}
.price{font-weight:600;color:var(--gold);white-space:nowrap;}
.qty-box{display:flex;align-items:center;gap:6px;margin-top:6px;}
.qty-btn{background:#eee;padding:2px 10px;border-radius:6px;text-decoration:none;color:#000;font-weight:600;}
.qty-input{width:40px;text-align:center;border-radius:6px;border:1px solid #ddd;padding:4px;background:#f6f6f6;}
.remove-btn{margin-top:6px;display:inline-block;background:#ff4d4d;color:#fff;padding:3px 10px;border-radius:6px;font-size:12px;text-decoration:none;}
.edit-btn{margin-top:6px;display:inline-block;background:green;color:#fff;padding:3px 10px;border-radius:6px;font-size:12px;text-decoration:none;}
.summary{display:flex;justify-content:space-between;margin-bottom:10px;}
.total{font-size:18px;font-weight:700;}
.btn-main{background:var(--gold);color:#111;border:none;width:100%;padding:14px;border-radius:30px;font-weight:600;}
.btn-main:hover{transform:scale(1.03);}
.error{background:#f8d7da;color:#842029;padding:12px;border-radius:10px;margin-bottom:20px;text-align:center;}
@media(max-width:992px){.checkout-grid{grid-template-columns:1fr;}}
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

<div class="page-wrap">

<div class="checkout-title">
  <h1>Checkout</h1>
  <p style="font-size:20px;">Complete your purchase securely</p>
</div>

<form method="post">
<div class="checkout-grid">

<!-- BILLING -->
<div class="card-box">
<h4>Billing Details</h4>

<div class="row-2">
  <input name="first_name" placeholder="First Name *" required
         value="<?= htmlspecialchars($user['first_name']) ?>">

  <input name="last_name" placeholder="Last Name *" required
         value="<?= htmlspecialchars($user['last_name']) ?>">
</div>

<input name="email" type="email" placeholder="Email *" required
       value="<?= htmlspecialchars($user['email']) ?>">

<!-- ✅ VALIDATED FIELDS -->

<input name="phone"
       placeholder="Phone *"
       required
       pattern="[6-9][0-9]{9}"
       inputmode="numeric"
       title="Enter valid 10 digit mobile number">

<input name="address"
       placeholder="Address *"
       required
       minlength="5"
       title="Address must be at least 5 characters">

<input name="city"
       placeholder="City *"
       required
       pattern="[A-Za-z\s]{2,}"
       title="Enter valid city name">

<div class="row-2">
  <input name="state"
         placeholder="State *"
         required
         pattern="[A-Za-z\s]{2,}"
         title="Enter valid state name">

  <input name="zip"
         placeholder="Zip *"
         required
         pattern="[1-9][0-9]{5}"
         inputmode="numeric"
         title="Enter valid 6 digit PIN code">
</div>

</div>

<!-- SUMMARY -->
<div class="card-box">
<h4 style="color:black;text-align: center;">Order Summary</h4>

<?php foreach($items as $i): ?>
<?php
$image = (!empty($i['image_path']) && file_exists($i['image_path']))
        ? $i['image_path']
        : "assets/images/default.jpg";
?>

<div class="product">

  <img src="<?= $image ?>">

  <div style="flex:1;">
    <strong><?= htmlspecialchars($i['name']) ?></strong>

    <div class="qty-box">
      <a class="qty-btn"
         href="checkout.php?update_qty=1&pid=<?= $i['id'] ?>&qty=<?= $i['qty']-1 ?>">−</a>

      <input class="qty-input" value="<?= $i['qty'] ?>" readonly>

      <a class="qty-btn"
         href="checkout.php?update_qty=1&pid=<?= $i['id'] ?>&qty=<?= $i['qty']+1 ?>">+</a>
    </div>

    <a class="remove-btn"
       href="checkout.php?remove_item=1&pid=<?= $i['id'] ?>">
       Remove
    </a>
    <a class="edit-btn"
       href="products.php?edit_item=1&pid=<?= $i['id'] ?>">
       Edit
    </a>
  </div>

  <div class="price">₹<?= number_format($i['line'],2) ?></div>

</div>

<?php endforeach; ?>

<hr>

<div class="summary">
  <span>Subtotal</span>
  <span>₹<?= number_format($subtotal,2) ?></span>
</div>

<div class="summary">
  <span>GST (5%)</span>
  <span>₹<?= number_format($gst,2) ?></span>
</div>

<div class="summary total">
  <span>Total</span>
  <span>₹<?= number_format($grand,2) ?></span>
</div>

<button class="btn-main mt-3" style="background-color: green;color: white ;">Proceed to Payment</button>

</div>
</div>
</form>

</div>
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
