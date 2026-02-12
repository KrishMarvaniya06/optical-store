<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    die("Invalid Order ID");
}

/* Fetch categories for navbar */
$categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")
    ->fetch_all(MYSQLI_ASSOC);

/* Fetch order */
$stmt = $mysqli->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found");
}

/* ---------------- CANCEL ORDER ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {

    if (in_array($order['status'], ['pending','processing'])) {

        $up = $mysqli->prepare(
            "UPDATE orders SET status='cancelled' WHERE id=? AND user_id=?"
        );
        $up->bind_param("ii", $order_id, $user_id);
        $up->execute();
        $up->close();

        header("Location: ".$_SERVER['PHP_SELF']."?id=".$order_id);
        exit;
    }
}
/* ------------------------------------------------ */

/* DELIVERY DATE = ORDER DATE + 5 DAYS */
$orderDate = new DateTime($order['created_at']);
$deliveryDate = clone $orderDate;
$deliveryDate->modify('+5 days');

/* Fetch items */
$items = $mysqli->query("
    SELECT oi.*, p.name, p.image_path, p.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order #<?= $order_id ?> — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
    --bg:#F6F5F3;
    --card:#FFFFFF;
    --text:#111;
    --muted:#666;
    --gold:#C8A062;
    --shadow:rgba(0,0,0,0.12);
}
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:var(--bg);
    color:var(--text);
}
.navbar{background:#111;}
.navbar .nav-link{color:#fff;margin-right:25px;}
.navbar .nav-link:hover{color:#ff69b4;}
.page-wrap{max-width:1400px;margin:auto;padding:40px 20px;}
.order-card{
    background:var(--card);
    border-radius:22px;
    box-shadow:0 18px 40px var(--shadow);
    padding:30px;
    max-width:900px;
    margin:0 auto;
}
.order-info{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    margin-bottom:30px;
    border-bottom:1px solid #eee;
    padding-bottom:20px;
}
.order-item{
    display:flex;
    gap:18px;
    margin-bottom:18px;
    padding-bottom:18px;
    border-bottom:1px solid #eee;
}
.order-item img{
    width:90px;
    height:90px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ddd;
}
.center-btn{text-align:center;margin-top:35px;}
.view-btn{
    background:var(--gold);
    color:#111;
    border:none;
    padding:12px 26px;
    border-radius:30px;
    font-weight:600;
    margin:0 8px;
    text-decoration:none;
    display:inline-block;
}
@media print{
    .navbar,.center-btn,footer{display:none}
    body{background:#fff}
    .order-card{box-shadow:none;border:1px solid #ccc}
}
</style>
</head>

<body>

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

<h2 class="text-center mb-4" style="color:black;">Order #<?= $order_id ?></h2>

<div class="order-card">

<div class="order-info">
    <div><strong>Name:</strong> <?= htmlspecialchars($order['name']) ?></div>
    <div><strong>Status:</strong> <?= ucfirst($order['status']) ?></div>
    <div><strong>Order Date:</strong> <?= $orderDate->format('d M Y') ?></div>
    <div><strong>Delivery Date:</strong> <?= $deliveryDate->format('d M Y') ?></div>
    <div><strong>Total:</strong> ₹<?= number_format($order['total_amount'],2) ?></div>
    <div><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></div>
</div>

<?php foreach($items as $item):
$img = (!empty($item['image_path']) && file_exists($item['image_path']))
    ? $item['image_path']
    : "assets/images/default.jpg";
?>
<div class="order-item">
    <img src="<?= $img ?>">
    <div>
        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
        Qty: <?= $item['quantity'] ?> × ₹<?= number_format($item['price'],2) ?><br>
        <strong>Subtotal:</strong>
        ₹<?= number_format($item['quantity']*$item['price'],2) ?>
    </div>
</div>
<?php endforeach; ?>

<!-- BUTTONS -->
<div class="center-btn">

    <?php if ($order['status'] !== 'cancelled'): ?>
        <a href="Invoice.php?id=<?= $order_id ?>" class="view-btn">Print Invoice</a>
    <?php endif; ?>

    <a href="user-order-history.php" class="view-btn">Back to Orders</a>

    <?php if (in_array($order['status'], ['pending','processing'])): ?>
        <form method="post" style="display:inline;">
            <button type="submit"
                    name="cancel_order"
                    class="view-btn"
                    style="background:#ff4d4d;color:#fff;"
                    onclick="return confirm('Are you sure you want to cancel this order?');">
                Cancel Order
            </button>
        </form>
    <?php endif; ?>

    <?php if (strtolower($order['status']) === 'delivered'): ?>
        <a href="feedback.php?order=<?= $order_id ?>" 
           class="view-btn" 
           style="background:#ff69b4;color:#fff;">
            Give Feedback
        </a>
    <?php endif; ?>

</div>

</div>
</div>

<footer style="background:#000; padding:80px 0 40px; color:#fff;">
    <div class="container">
        <div class="row">

            <div class="col-lg-3 col-md-6 mb-4">
                <h4 style="color:#ff69b4; font-weight:700;">Lumineux Opticals</h4>
                <p style="color:#ccc;">
                    Discover premium eyewear crafted for clarity, comfort, and unmatched luxury. 
                    Elevate your vision with style.
                </p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">About</h5>
                <p style="color:#ccc;">
                    Lumineux Opticals offers high-quality frames and lenses to elevate your style while ensuring clarity and comfort.
                </p>
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
        <p class="text-center text-muted" style="color:#777;">
            © 2026 Lumineux Opticals — All Rights Reserved.
        </p>
    </div>
</footer>

</body>
</html>
