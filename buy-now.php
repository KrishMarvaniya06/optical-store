<?php
session_start();

// If no product id, go home
if (!isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$product_id = (int) $_GET['id'];

// Add to cart (quantity = 1)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'][$product_id] = 1;

// Go to checkout
header("Location: checkout.php");
exit;
?>