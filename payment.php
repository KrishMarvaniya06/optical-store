<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db_connect.php';

/* LOAD SESSION DATA */
$cart = $_SESSION['cart'] ?? [];
$checkout = $_SESSION['checkout'] ?? [];
$user_id = $_SESSION['user_id'] ?? 0;

if (!$cart || !$checkout) die("Cart is empty");

/* FETCH USER DETAILS */
$userData = [];
if($user_id){

    $stmtUser = $mysqli->prepare("SELECT username,email,phone,address FROM user WHERE id=?");

    if($stmtUser){
        $stmtUser->bind_param("i",$user_id);
        $stmtUser->execute();
        $res = $stmtUser->get_result();
        $userData = $res->fetch_assoc() ?? [];
        $stmtUser->close();
    }
}

/* FETCH CART PRODUCTS */
$ids = implode(",", array_map('intval', array_keys($cart)));
$res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");

$items = [];
$subtotal = 0;

while ($row = $res->fetch_assoc()) {
    $row['qty'] = $cart[$row['id']];
    $row['line'] = $row['qty'] * $row['price'];
    $subtotal += $row['line'];
    $items[] = $row;
}

/* COST */
$shipping = 40;
$tax = round($subtotal * 0.05, 2);
$grand = $subtotal + $shipping + $tax;

/* PAYMENT PROCESS */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {

    $method = $_POST['payment_method'];

    if ($method === "CARD" && !preg_match('/^\d{12}$/', $_POST['card_number'] ?? '')) {
        die("Invalid Card Number");
    }

    if ($method === "NET_BANKING" && !preg_match('/^\d{11}$/', $_POST['account_number'] ?? '')) {
        die("Invalid Account Number");
    }

    $status = ($method === 'COD') ? 'pending' : 'success';
    $txn = "TXN" . strtoupper(uniqid());
    $info = "";

    if ($method === "CARD") {
        $info = "CARD ****" . substr($_POST['card_number'], -4);
    }
    elseif ($method === "UPI") {
        $info = "UPI: " . $_POST['upi_id'];
    }
    elseif ($method === "NET_BANKING") {
        $info = $_POST['bank_name'] . " / ****" . substr($_POST['account_number'], -4);
    }

    /* INSERT ORDER */
    $stmt = $mysqli->prepare(
        "INSERT INTO orders(user_id,name,email,shipping_address,total_amount,status,payment_method)
         VALUES(?,?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "isssdss",
        $user_id,
        $checkout['name'],
        $checkout['email'],
        $checkout['address'],
        $grand,
        $status,
        $method
    );

    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    /* INSERT ORDER ITEMS */
    foreach ($items as $p) {

        $st = $mysqli->prepare(
            "INSERT INTO order_items(order_id,product_id,quantity,price)
             VALUES(?,?,?,?)"
        );

        $st->bind_param("iiid",$order_id,$p['id'],$p['qty'],$p['price']);
        $st->execute();
        $st->close();
    }

    /* INSERT PAYMENT */
    $st = $mysqli->prepare(
        "INSERT INTO payments(order_id,payment_method,amount,payment_status,transaction_id,payment_info)
         VALUES(?,?,?,?,?,?)"
    );

    $st->bind_param("isdsss",$order_id,$method,$grand,$status,$txn,$info);
    $st->execute();
    $st->close();

    unset($_SESSION['cart'], $_SESSION['checkout']);

    header("Location: index.php?order_id=".$order_id);
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payment — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{background:#F6F5F3;font-family:'Poppins',sans-serif;}

.page-wrap{max-width:1400px;margin:auto;padding:40px 20px;}

.checkout-grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:30px;
}

.card-box{
background:#fff;
border-radius:22px;
padding:25px;
box-shadow:0 18px 40px rgba(0,0,0,.12);
}

input,select{
width:100%;
padding:10px;
border:1px solid #ddd;
border-radius:10px;
margin-bottom:15px;
}

.btn-main{
background:green;
color:#fff;
border:none;
width:100%;
padding:14px;
border-radius:30px;
font-weight:600;
}

.product{
display:flex;
gap:15px;
margin-bottom:15px;
align-items:flex-start;
}

.product img{
width:80px;
height:80px;
object-fit:cover;
border-radius:12px;
}

.summary{
display:flex;
justify-content:space-between;
margin-bottom:10px;
}

.total{
font-size:18px;
font-weight:700;
}

.hide{display:none;}

@media(max-width:992px){
.checkout-grid{grid-template-columns:1fr;}
}
/* Navbar */
.navbar{background:#111;}
.navbar .navbar-nav .nav-link{margin-right:25px;color:#fff;}
.navbar .navbar-nav .nav-link.active,
.navbar .navbar-nav .nav-link:hover{color:#ff69b4;}
.navbar .dropdown-menu{background:#111;}
.navbar .dropdown-item{color:#fff;}
.navbar .dropdown-item:hover{background:#ff69b4;color:#111;}

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
<div class="page-wrap">

<h1 style="text-align:center;margin-bottom:30px;color: black;">Secure Payment</h1>

<form method="post" id="payForm">

<div class="checkout-grid">

<!-- PAYMENT METHOD -->
<div class="card-box">

<h4 style="color:black;">Payment Method</h4>

<?php if($userData): ?>
<div style="background:#f9f9f9;padding:15px;border-radius:15px;margin-bottom:20px;">
<strong><?= htmlspecialchars($userData['username'] ?? '') ?></strong><br>
<?= htmlspecialchars($userData['email'] ?? '') ?><br>
<?= htmlspecialchars($userData['phone'] ?? '') ?><br>
<?= htmlspecialchars($userData['address'] ?? '') ?>
</div>
<?php endif; ?>

<select name="payment_method" onchange="togglePay(this.value)" required>
<option value="">Select Method</option>
<option value="CARD">Card</option>
<option value="UPI">UPI</option>
<option value="NET_BANKING">Net Banking</option>
<option value="COD">Cash On Delivery</option>
</select>

<div id="CARD" class="hide">
<input type="text" name="card_number" placeholder="12 Digit Card Number" maxlength="12">
</div>

<div id="UPI" class="hide">
<input type="text" name="upi_id" placeholder="example@bank">
</div>

<div id="NET_BANKING" class="hide">
<select name="bank_name">
<option>SBI</option>
<option>HDFC</option>
<option>ICICI</option>
</select>
<input type="text" name="account_number" placeholder="11 Digit Account Number" maxlength="11">
</div>

</div>


<!-- ORDER SUMMARY -->
<div class="card-box">

<h4 style="text-align:center; color: black;">Order Summary</h4>

<?php foreach($items as $i):

$img = (!empty($i['image_path']) && file_exists($i['image_path']))
        ? $i['image_path']
        : "assets/images/default.jpg";
?>

<div class="product">

<img src="<?= $img ?>">

<div style="flex:1;">
<strong><?= htmlspecialchars($i['name']) ?></strong>
<br>Qty: <?= $i['qty'] ?>
</div>

<div>₹<?= number_format($i['line'],2) ?></div>

</div>

<?php endforeach; ?>

<hr>

<div class="summary">
<span>Subtotal</span>
<span>₹<?= number_format($subtotal,2) ?></span>
</div>

<div class="summary">
<span>Tax</span>
<span>₹<?= number_format($tax,2) ?></span>
</div>

<div class="summary">
<span>Shipping</span>
<span>₹<?= number_format($shipping,2) ?></span>
</div>

<div class="summary total">
<span>Total</span>
<span>₹<?= number_format($grand,2) ?></span>
</div>

<button class="btn-main mt-3">Confirm & Pay</button>

</div>

</div>
</form>
</div>

<script>
function togglePay(method){

    ['CARD','UPI','NET_BANKING'].forEach(id=>{
        let el = document.getElementById(id);
        if(el) el.classList.add('hide');
    });

    if(document.getElementById(method)){
        document.getElementById(method).classList.remove('hide');
    }
}
</script>
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
</body>
</html>