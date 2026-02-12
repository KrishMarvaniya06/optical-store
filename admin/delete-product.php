<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

// Get product ID from URL
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die("Invalid request.");
}

// Optional: Delete product images from server
$stmt = $mysqli->prepare("SELECT image FROM products WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($product && !empty($product['image'])) {
    $imagePath = __DIR__ . '/../assets/images/products/' . $product['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete product from database
$stmt = $mysqli->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

// Redirect back to manage products page
header("Location: manage-product.php");
exit;
?>
