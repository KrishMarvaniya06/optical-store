<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $error = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare(
            "INSERT INTO user (username, email, password) VALUES (?, ?, ?)"
        );

        if ($stmt) {

            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {

                // ✅ Redirect to login page after successful registration
                header("Location: login.php");
                exit;

            } else {

                // Duplicate username/email or other DB error
                $error = "Username or email already exists.";
            }

            $stmt->close();

        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register — Lumineux Opticals</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap + Icons -->
<link href="bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* === Animated Background === */
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

/* === Register Card === */
.register-card{
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

.register-card h2{
    font-weight:700;
    text-align:center;
    margin-bottom:25px;
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
.login-link{
    text-align:center;
    margin-top:15px;
}

.login-link a{
    color:#ff69b4;
    font-weight:600;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="register-card">

    <div class="text-center mb-3">
        <img src="assets/images/main-logo.jpg" alt="Lumineux" height="45">
    </div>

    <h2 style="color:black;">Create Account</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa fa-user"></i></span>
            <input type="text" name="username" class="form-control"
                   placeholder="Username" required>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
            <input type="email" name="email" class="form-control"
                   placeholder="Email" required>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa fa-lock"></i></span>
            <input type="password" name="password" class="form-control"
                   placeholder="Password" required>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text"><i class="fa fa-lock"></i></span>
            <input type="password" name="confirm" class="form-control"
                   placeholder="Confirm Password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">
            Register
        </button>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>