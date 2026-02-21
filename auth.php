<?php
session_start();

if (!empty($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-card {
            width: 360px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            padding: 24px;
            text-align: center;
        }
        .auth-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 14px;
            background: #eef3ff;
        }
        h2 {
            margin: 0 0 8px;
        }
        p {
            margin: 0 0 16px;
            color: #555;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        .btn-login {
            background: #0b5ed7;
            color: #fff;
        }
        .btn-register {
            background: #198754;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <img class="auth-image" src="assets/images/park-botanical.svg" alt="Park entry">
        <h2>Welcome</h2>
        <p>Please login or register to continue.</p>
        <a class="btn btn-login" href="login.php">Login</a>
        <a class="btn btn-register" href="register.php">Register</a>
    </div>
</body>
</html>
