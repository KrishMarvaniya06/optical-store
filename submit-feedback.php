<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit feedback.");
}

$user_id = $_SESSION['user_id'];

// Check required POST data
if (!isset($_POST['product_id'], $_POST['rating'], $_POST['review'])) {
    die("Invalid request.");
}

$product_id = intval($_POST['product_id']);
$rating = intval($_POST['rating']);
$review = trim($_POST['review']);

// Validate rating
if ($rating < 1 || $rating > 5) {
    die("Invalid rating value.");
}

// Optional: prevent duplicate feedback for same product
$stmtCheck = $mysqli->prepare("SELECT id FROM feedback WHERE user_id=? AND product_id=?");
$stmtCheck->bind_param("ii", $user_id, $product_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
if ($resultCheck->num_rows > 0) {
    $stmtCheck->close();
    die("You have already submitted feedback for this product.");
}
$stmtCheck->close();

// Insert feedback
$stmt = $mysqli->prepare("INSERT INTO feedback(user_id, product_id, rating, review) VALUES(?,?,?,?)");
$stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
if ($stmt->execute()) {
    $stmt->close();
    // Redirect to index.php after feedback submission
    header("Location: index.php?feedback=success");
    exit;
} else {
    $stmt->close();
    die("Error submitting feedback. Please try again.");
}
