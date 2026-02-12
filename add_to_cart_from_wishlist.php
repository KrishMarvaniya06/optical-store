<?php
session_start();
require_once 'db_connect.php';

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id > 0) {

    // Add to cart
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    // Remove from wishlist
    if (isset($_SESSION['wishlist'])) {
        $key = array_search($product_id, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
            // Reindex the array
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        }
    }
}

// Redirect back to wishlist
header("Location: wishlist.php");
exit;
?>
