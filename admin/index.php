<?php
// Start session to check login
session_start();

// If admin is logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: admin-dashboard.php');
    exit;
}

// If not logged in, redirect to admin login page
header('Location: admin-login.php');
exit;
?>