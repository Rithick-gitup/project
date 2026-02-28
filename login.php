<?php
session_start();
require_once __DIR__ . "/db.php";

if (!empty($_SESSION["logged_in"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter your email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([":email" => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password_hash"])) {
                $_SESSION["logged_in"] = true;
                $_SESSION["username"] = $user["username"];
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Login failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-card {
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            width: 320px;
        }
        .login-image {
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
            background: #0b5ed7;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #0a53be;
        }
        .error {
            color: #b00020;
            margin-bottom: 12px;
            text-align: center;
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
    <div class="login-card">
        <img class="login-image" src="assets/images/hero-home.svg" alt="Park portal">
        <h2>Login</h2>
        <?php if ($error !== ""): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign In</button>
        </form>
        <div class="auth-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
