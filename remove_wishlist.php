<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
$wid = intval($_POST['wishlist_id'] ?? 0);

if ($user_id == 0 || $wid == 0) {
    header("Location: wishlist.php");
    exit;
}

$del = $mysqli->prepare("DELETE FROM wishlist WHERE id=? AND user_id=?");
$del->bind_param("ii", $wid, $user_id);
$del->execute();

header("Location: wishlist.php");
exit;
