<?php
session_start();
require_once 'db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $mysqli->prepare("SELECT id, password, status FROM user WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $error = "Invalid email or password";
    } 
    else {

        $stmt->bind_result($user_id, $hashed, $status);
        $stmt->fetch();

        // ⭐ PASSWORD CHECK
        if (!password_verify($password, $hashed)) {
            $error = "Invalid email or password";
        }

        // ⭐ BLOCK CHECK FIRST
        elseif($status === "blocked"){
            $error = "Your account is blocked. Contact admin.";
        }

        // ⭐ LOGIN SUCCESS
        else {
            $_SESSION['user_id'] = $user_id;
            header("Location: index.php");
            exit;
        }
    }
}

?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap + Icons (same as register.php) -->
<link href="bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* === Animated Background (SAME AS REGISTER) === */
body{
    min-height:100vh;
    margin:0;
    display:flex;
    align-items:center;
    justify-content:center;

    background:
        linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
        url('assets/images/register-bg.jpg') center/cover no-repeat;

    animation:bgZoom 22s ease-in-out infinite alternate;
    font-family: 'Poppins', sans-serif;
}

@keyframes bgZoom{
    from{ background-size:100%; }
    to{ background-size:112%; }
}

/* === Glass Card === */
.login-card{
    background:rgba(255,255,255,.94);
    backdrop-filter:blur(6px);
    border-radius:20px;
    padding:40px;
    max-width:420px;
    width:100%;
    box-shadow:0 20px 45px rgba(0,0,0,.35);
    animation: fadeUp .8s ease;
}

@keyframes fadeUp{
    from{ opacity:0; transform:translateY(25px); }
    to{ opacity:1; transform:translateY(0); }
}

.login-card h2{
    font-weight:700;
    text-align:center;
    margin-bottom:25px;
    color:black;
}

/* === Form === */
.form-control{
    border-radius:12px;
    padding:12px 16px;
}

.input-group-text{
    background:#f1f1f1;
    border-radius:12px 0 0 12px;
}

/* === Button === */
.btn-primary{
    background:#ff69b4;
    border:none;
    border-radius:30px;
    padding:12px;
    font-weight:700;
}

.btn-primary:hover{
    background:#ff4fa8;
}

/* === Alerts === */
.alert{
    border-radius:12px;
}

/* === Links === */
.auth-link{
    text-align:center;
    margin-top:15px;
}

.auth-link a{
    color:#ff69b4;
    font-weight:600;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="login-card">

    <!-- Logo -->
    <div class="text-center mb-3">
        <img src="assets/images/main-logo.jpg" alt="Lumineux" height="45">
    </div>

    <h2>Welcome Back</h2>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text"><i class="fa fa-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Login
        </button>

        <div class="auth-link">
            Don’t have an account?
            <a href="register.php">Register</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
