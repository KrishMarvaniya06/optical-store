<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
$id = intval($_POST['product_id'] ?? 0);

// Debug prints
// Remove after testing
echo "USER: $user_id<br>";
echo "PRODUCT: $id<br>";

// Must login
if ($user_id == 0) {
    die("ERROR: user not logged in");
}

// Check if item already in wishlist
$check = $mysqli->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
if(!$check) die("Prepare error: " . $mysqli->error);

$check->bind_param("ii", $user_id, $id);
$check->execute();
$result = $check->get_result();

if(!$result) die("Query failed: " . $mysqli->error);

if ($result->num_rows > 0) {
    header("Location: product-detail.php?id=$id&already_in_wishlist=1");
    exit;
}

// Add item
$stm = $mysqli->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
if(!$stm) die("Insert prepare error: " . $mysqli->error);

$stm->bind_param("ii", $user_id, $id);
$done = $stm->execute();

if(!$done) die("Insert failed: " . $stm->error);

header("Location: product-detail.php?id=$id&added_to_wishlist=1");
exit;
?>