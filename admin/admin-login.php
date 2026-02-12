<?php

session_start();

$default_username = 'admin';
$default_password = 'admin123'; // You can change this

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = 'Enter credentials';
    } else {
        if ($username === $default_username && $password === $default_password) {
            $_SESSION['admin_id'] = 1; // default admin ID
            $_SESSION['admin_user'] = $default_username;
            header('Location: admin-dashboard.php');
            exit;
        }

        if ($username !== $default_username || $password !== $default_password) {
            $err = 'Invalid credentials';
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — Lumineux</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--emerald:#046307;--gold:#D4AF37;--white:#fff}
*{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif}
body{display:flex;align-items:center;justify-content:center;height:100vh;background:#f5f6f7}
.card{width:380px;padding:28px;border-radius:12px;background:#fff;box-shadow:0 12px 40px rgba(4,99,7,0.06);border:1px solid rgba(0,0,0,0.04)}
h2{color:var(--emerald);text-align:center;margin-bottom:12px}
input{width:100%;padding:12px;border-radius:10px;border:1px solid rgba(0,0,0,0.10);margin-top:10px}
button{width:100%;padding:12px;margin-top:14px;border-radius:10px;border:none;background:var(--gold);font-weight:700;cursor:pointer}
.err{color:#c00;margin-top:10px;text-align:center}
</style>
</head>
<body>
  <div class="card">
    <h2>Admin Login</h2>
    <form method="post">
      <input name="username" placeholder="Username" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <?php if ($err): ?><div class="err"><?=htmlspecialchars($err)?></div><?php endif; ?>
  </div>
</body>
</html>