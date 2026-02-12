<?php
session_start();
require_once 'db_connect.php'; // Database connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';
$pass_success = '';
$pass_error = '';

// Fetch current user data
$stmt = $mysqli->prepare("SELECT username, email, password FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit;
}

// Handle profile update (username/email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$username || !$email) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt_check = $mysqli->prepare("SELECT id FROM user WHERE (username = ? OR email = ?) AND id != ?");
        $stmt_check->bind_param("ssi", $username, $email, $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username or email already taken by another user.";
        } else {
            $stmt_update = $mysqli->prepare("UPDATE user SET username = ?, email = ? WHERE id = ?");
            $stmt_update->bind_param("ssi", $username, $email, $user_id);
            if ($stmt_update->execute()) {
                $success = "Profile updated successfully!";
                $user['username'] = $username;
                $user['email'] = $email;
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
        $stmt_check->close();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if (!$current_pass || !$new_pass || !$confirm_pass) {
        $pass_error = "All password fields are required.";
    } elseif (!password_verify($current_pass, $user['password'])) {
        $pass_error = "Current password is incorrect.";
    } elseif ($new_pass !== $confirm_pass) {
        $pass_error = "New password and confirmation do not match.";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt_update = $mysqli->prepare("UPDATE user SET password = ? WHERE id = ?");
        $stmt_update->bind_param("si", $hashed_pass, $user_id);
        if ($stmt_update->execute()) {
            $pass_success = "Password changed successfully!";
        } else {
            $pass_error = "Failed to update password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile — Lumineux Opticals</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
        color: #333;
    }
    header {
        background-color: #0f172a;
        color: #fff;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header h1 { font-size: 28px; }
    nav a {
        color: #fff;
        margin-left: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
    }
    nav a:hover { color: #38bdf8; }

    .profile-container {
        max-width: 500px;
        margin: 60px auto;
        background-color: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .profile-container h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #0f172a;
    }
    .profile-item {
        margin-bottom: 20px;
    }
    .profile-item label {
        display: block;
        font-weight: 700;
        margin-bottom: 5px;
        color: #0f172a;
    }
    .profile-item input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }
    .btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        text-align: center;
        background-color: #0f172a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn:hover { background-color: #38bdf8; }
    .success { color: green; margin-bottom: 15px; text-align: center; }
    .error { color: red; margin-bottom: 15px; text-align: center; }

    footer {
        background-color: #0f172a;
        color: #fff;
        text-align: center;
        padding: 25px 40px;
        margin-top: 60px;
    }
    hr {
        margin: 40px 0;
        border: 0;
        border-top: 1px solid #ccc;
    }
</style>
</head>
<body>
<header>
    <h1>Lumineux Opticals</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="profile-container">
    <h2>Edit Profile</h2>

    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <input type="hidden" name="update_profile" value="1">
        <div class="profile-item">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="profile-item">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>

    <hr>

    <h2>Change Password</h2>
    <?php if($pass_success) echo "<p class='success'>$pass_success</p>"; ?>
    <?php if($pass_error) echo "<p class='error'>$pass_error</p>"; ?>

    <form method="POST" action="">
        <input type="hidden" name="change_password" value="1">
        <div class="profile-item">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="profile-item">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="profile-item">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Change Password</button>
    </form>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Lumineux Opticals. All Rights Reserved.
</footer>
</body>
</html>
