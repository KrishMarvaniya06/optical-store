<?php
session_start();
require_once 'db_connect.php';

$token = trim($_GET['token'] ?? "");

if (!$token) {
    die("Invalid token");
}

$stmt = $mysqli->prepare("
    SELECT id 
    FROM user 
    WHERE reset_token=? 
    AND reset_expires > NOW()
");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("Token expired or invalid");
}

$stmt->bind_result($user_id);
$stmt->fetch();

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $update = $mysqli->prepare("
        UPDATE user 
        SET password=?, reset_token=NULL, reset_expires=NULL 
        WHERE id=?
    ");
    $update->bind_param("si", $newpass, $user_id);
    $update->execute();

    $success = "Password reset successful. <a href='login.php'>Login</a>";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<style>
body{font-family:Poppins;background:#f4f4f4;height:100vh;display:flex;align-items:center;justify-content:center;}
form{background:#fff;padding:30px;width:350px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.08);}
input{width:100%;padding:12px;margin:8px 0;border-radius:8px;border:1px solid #ccc;}
.btn{padding:10px 18px;border-radius:10px;background:#D4AF37;color:#000;font-weight:700;border:none;cursor:pointer;width:100%;}
.success{color:green;margin-top:10px;}
</style>
</head>
<body>

<form method="POST">
  <h2>Reset Password</h2>

  <input type="password" name="password" placeholder="New Password" required>

  <button class="btn">Reset Password</button>

  <?php if($success) echo "<div class='success'>$success</div>"; ?>
</form>

</body>
</html>
