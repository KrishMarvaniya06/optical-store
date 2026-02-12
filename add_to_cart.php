<?php
session_start();
require_once 'db_connect.php';

/* ✅ User must be logged in */
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {

    // redirect to login page
    header("Location: login.php?login_required=1");
    exit;
}

/* get product id */
$id = intval($_POST['product_id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

/* initialize cart */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* already in cart */
if (isset($_SESSION['cart'][$id])) {

    header("Location: product-detail.php?id=$id&already_in_cart=1");
    exit;
}

/* add first time */
$_SESSION['cart'][$id] = 1;

header("Location: product-detail.php?id=$id&added_to_cart=1");
exit;
?>
