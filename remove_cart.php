<?php
session_start();

// Check if cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product ID from POST
$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

// Redirect back to cart page
header("Location: cart.php");
exit;
?>
