<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = "Registration submitted. Please contact admin to activate your account.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .register-card {
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            width: 340px;
        }
        .register-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 14px;
            background: #eef3ff;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 16px;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #198754;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #157347;
        }
        .message {
            color: #0f5132;
            background: #d1e7dd;
            border: 1px solid #badbcc;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 14px;
        }
        .auth-link {
            margin-top: 12px;
            text-align: center;
            font-size: 14px;
        }
        .auth-link a {
            color: #0b5ed7;
            text-decoration: none;
            font-weight: bold;
        }
        .auth-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <img class="register-image" src="assets/images/park-adventure.svg" alt="Park registration">
        <h2>Register</h2>
        <?php if ($message !== ""): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Create Account</button>
        </form>
        <div class="auth-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
