<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if (!$type || !$id) {
    die("Invalid request.");
}

switch ($type) {
    case 'main':
        // Set related subcategories' main_category_id to NULL
        $mysqli->query("UPDATE brands SET main_category_id = NULL WHERE main_category_id = $id");
        $mysqli->query("UPDATE genders SET main_category_id = NULL WHERE main_category_id = $id");
        $mysqli->query("UPDATE price_categories SET main_category_id = NULL WHERE main_category_id = $id");
        // Delete the main category
        $stmt = $mysqli->prepare("DELETE FROM main_categories WHERE id = ?");
        break;

    case 'brand':
        // Remove foreign key from products
        $mysqli->query("UPDATE products SET brand_id = NULL WHERE brand_id = $id");
        $stmt = $mysqli->prepare("DELETE FROM brands WHERE id = ?");
        break;

    case 'gender':
        $mysqli->query("UPDATE products SET gender_id = NULL WHERE gender_id = $id");
        $stmt = $mysqli->prepare("DELETE FROM genders WHERE id = ?");
        break;

    case 'price':
        $stmt = $mysqli->prepare("DELETE FROM price_categories WHERE id = ?");
        break;

    default:
        die("Invalid category type.");
}

$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: manage-categories.php");
    exit;
} else {
    die("Error deleting category: " . $stmt->error);
}
