<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;

/* ---------- PRODUCT FETCH ---------- */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$productId = (int) $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$product = $res->fetch_assoc();

/* ---------- ADD TO WISHLIST (SAME FILE) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_wishlist']) && $user_id) {

    $check = $mysqli->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
    $check->bind_param("ii", $user_id, $productId);
    $check->execute();
    $exists = $check->get_result()->num_rows;

    if ($exists > 0) {
        $_SESSION['popup'] = [
            'type' => 'info',
            'title' => 'Info',
            'msg' => 'Product already in wishlist'
        ];
    } else {
        $ins = $mysqli->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?,?)");
        $ins->bind_param("ii", $user_id, $productId);
        $ins->execute();

        $_SESSION['popup'] = [
            'type' => 'success',
            'title' => 'Success',
            'msg' => 'Product added to wishlist'
        ];
    }

    header("Location: product-detail.php?id=".$productId);
    exit;
}

/* ---------- FETCH NAV DATA ---------- */
$categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$brands = $mysqli->query("SELECT * FROM brands")->fetch_all(MYSQLI_ASSOC);
$genders = $mysqli->query("SELECT * FROM genders")->fetch_all(MYSQLI_ASSOC);
$prices = $mysqli->query("SELECT * FROM price_categories")->fetch_all(MYSQLI_ASSOC);

/* ---------- CHECK WISHLIST ---------- */
$wishlist_ids = [];
if ($user_id) {
    $wl = $mysqli->prepare("SELECT product_id FROM wishlist WHERE user_id=?");
    $wl->bind_param("i", $user_id);
    $wl->execute();
    $r = $wl->get_result();
    while ($row = $r->fetch_assoc()) {
        $wishlist_ids[] = $row['product_id'];
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($product['name']) ?></title>
<link rel="stylesheet" href="bootstrap.min.css">

<style>
body{margin:0;font-family:Poppins;background:#f6f5f3;}
.navbar{background:#111;}
.page-wrap{max-width:1200px;margin:auto;padding:40px;}

.custom-popup{
    position:fixed;
    top:90px;
    right:20px;
    background:#fff;
    min-width:320px;
    padding:14px;
    display:flex;
    gap:12px;
    border-radius:8px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
    z-index:9999;
    animation:slide .4s ease;
}
.custom-popup.success{border-left:6px solid #2ecc71;}
.custom-popup.info{border-left:6px solid #3498db;}
.custom-popup .icon{
    width:26px;height:26px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:bold;
    background:#2ecc71;
}
.custom-popup.info .icon{background:#3498db;}
.custom-popup strong{display:block;}
.custom-popup .close{border:none;background:none;font-size:18px;cursor:pointer;}

@keyframes slide{
    from{transform:translateX(120%);opacity:0}
    to{transform:none;opacity:1}
}

.card-box{
    background:#fff;
    border-radius:20px;
    padding:30px;
    display:flex;
    gap:30px;
}
.card-box img{width:300px;border-radius:12px;}
.btn-gold{
    background:#C8A062;border:none;
    padding:10px 20px;border-radius:20px;
    font-weight:600;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
  <a class="navbar-brand" href="index.php">Lumineux Opticals</a>
</nav>

<!-- POPUP AFTER HEADER -->
<?php if (!empty($_SESSION['popup'])): ?>
<div class="custom-popup <?= $_SESSION['popup']['type'] ?>">
    <div class="icon">✓</div>
    <div>
        <strong><?= $_SESSION['popup']['title'] ?></strong>
        <span><?= $_SESSION['popup']['msg'] ?></span>
    </div>
    <button class="close" onclick="this.parentElement.remove()">×</button>
</div>
<?php unset($_SESSION['popup']); endif; ?>

<div class="page-wrap">
<div class="card-box">

    <img src="<?= $product['image_path'] ?>">

    <div>
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p><b>Price:</b> ₹<?= number_format($product['price']) ?></p>

        <form method="post">
            <button name="add_wishlist"
                class="btn-gold"
                <?= in_array($productId,$wishlist_ids)?'disabled':'' ?>>
                <?= in_array($productId,$wishlist_ids)?'In Wishlist':'Add to Wishlist' ?>
            </button>
        </form>
    </div>

</div>
</div>

<script>
setTimeout(()=>{document.querySelector('.custom-popup')?.remove();},3000);
</script>

</body>
</html>