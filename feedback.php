<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET['order'] ?? 0);

if (!$order_id) {
    die("Invalid Order");
}

/* =======================
   CHECK ORDER & STATUS
======================= */

$stmt = $mysqli->prepare("
    SELECT id, status 
    FROM orders 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order || strtolower($order['status']) !== 'delivered') {
    die("Order not found or not delivered");
}

/* =======================
   FETCH PRODUCTS OF ORDER
======================= */

$stmt = $mysqli->prepare("
    SELECT oi.product_id, p.name
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!$items) {
    die("No products found for this order.");
}

/* =======================
   CHECK FEEDBACK ALREADY GIVEN FOR THIS ORDER
======================= */

$stmt = $mysqli->prepare("
    SELECT id 
    FROM feedback 
    WHERE user_id = ? AND order_id = ?
");
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$stmt->store_result();
$already = $stmt->num_rows;
$stmt->close();

if ($already > 0) {
    die("
        <h2 style='text-align:center;margin-top:50px;color:#ff69b4'>
            You already submitted feedback for this order.
        </h2>
        <p style='text-align:center;'>
            <a href='user-order-history.php'>Back to Orders</a>
        </p>
    ");
}

/* =======================
   SUBMIT FEEDBACK
======================= */

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = intval($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? "");

    if ($rating < 1 || $rating > 5) {
        $error = "Please select a rating";
    } else {

        $stmt2 = $mysqli->prepare("
            INSERT INTO feedback 
            (user_id, order_id, product_id, rating, review)
            VALUES (?,?,?,?,?)
        ");

        foreach ($items as $it) {

            $pid = $it['product_id'];

            $stmt2->bind_param(
                "iiiis",
                $user_id,
                $order_id,
                $pid,
                $rating,
                $review
            );

            $stmt2->execute();
        }

        $stmt2->close();

        echo "<script>
                alert('Thanks for your feedback!');
                window.location='user-order-history.php';
              </script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{background:#fafafa;font-family:'Poppins',sans-serif;margin:0;}
.top-banner{
    background:#fdf0f6;
    border-bottom:1px solid #ffd7e4;
    padding:25px;
    text-align:center;
}
.feedback-card{
    max-width:600px;
    background:#fff;
    margin:40px auto;
    padding:35px 30px;
    border:1px solid #eee;
    border-radius:12px;
}
.product-box{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:15px;
}
.product-box i{
    font-size:40px;
    color:#ff69b4;
}
.star-rating i{
    font-size:30px;
    color:#ccc;
    cursor:pointer;
    margin:3px;
    transition:.2s;
}
.star-rating i.active{color:#ffb700;}
textarea{
    width:100%;
    border:1px solid #ddd;
    border-radius:8px;
    padding:12px;
    min-height:110px;
    resize:none;
}
.btn-submit{
    background:#ff69b4;
    color:#fff;
    border:none;
    padding:12px 26px;
    font-weight:600;
    border-radius:30px;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="top-banner"></div>

<div class="feedback-card">

    <h4 style="text-align:center;">Hi 👋</h4>
    <p style="text-align:center;">Thanks again for your purchase! Please rate your experience.</p>

    <?php foreach($items as $it): ?>
        <div class="product-box">
            <div>
                <strong><?= htmlspecialchars($it['name']) ?></strong>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!empty($error)): ?>
        <p style="color:red;text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">

        <div style="text-align:center" class="star-rating" id="stars">
            <?php for($i=1;$i<=5;$i++): ?>
                <i class="fa fa-star" data-value="<?= $i ?>"></i>
            <?php endfor; ?>
        </div>

        <input type="hidden" name="rating" id="ratingValue">

        <textarea name="review" placeholder="Tell us what you liked or disliked..."></textarea>

        <br><br>

        <center>
            <button type="submit" class="btn-submit">Submit Review</button>
        </center>

    </form>

</div>

<script>
let stars = document.querySelectorAll('#stars i');
let ratingInput = document.getElementById('ratingValue');

stars.forEach((star, index) => {
    star.addEventListener('click', function () {

        let val = this.getAttribute('data-value');
        ratingInput.value = val;

        stars.forEach(s => s.classList.remove('active'));

        for (let i = 0; i < val; i++) {
            stars[i].classList.add('active');
        }
    });
});
</script>

</body>
</html>
