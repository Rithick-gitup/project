<?php
require_once __DIR__ . "/db.php";

$message = "";
$error = "";

function hasProfilePictureColumn(PDO $pdo): bool
{
    try {
        $column = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
        return (bool) $column->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

$profilePictureColumnExists = hasProfilePictureColumn($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $profilePicturePath = null;

    if ($name === "" || $email === "" || $username === "" || $phone === "" || $password === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $username)) {
        $error = "Username must be 4-20 characters and only use letters, numbers, or underscore.";
    }

    if ($error === "" && !empty($_FILES["profile_picture"]["name"])) {
        if (!isset($_FILES["profile_picture"]["error"]) || $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
            $error = "Profile picture upload failed. Please try again.";
        } else {
            $maxSize = 2 * 1024 * 1024; // 2MB
            $fileSize = (int) ($_FILES["profile_picture"]["size"] ?? 0);
            $tmpPath = $_FILES["profile_picture"]["tmp_name"] ?? "";

            if ($fileSize <= 0 || $fileSize > $maxSize) {
                $error = "Profile picture must be less than 2MB.";
            } elseif (!is_uploaded_file($tmpPath)) {
                $error = "Invalid upload request.";
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($tmpPath);
                $allowed = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed[$mime])) {
                    $error = "Allowed image types: JPG, PNG, WEBP.";
                } else {
                    $uploadDir = __DIR__ . "/assets/uploads/profile_pictures";
                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                        $error = "Unable to create upload folder.";
                    } else {
                        $safeName = preg_replace("/[^A-Za-z0-9_]/", "", $username);
                        $fileName = $safeName . "_" . bin2hex(random_bytes(8)) . "." . $allowed[$mime];
                        $destination = $uploadDir . "/" . $fileName;

                        if (!move_uploaded_file($tmpPath, $destination)) {
                            $error = "Unable to save profile picture.";
                        } else {
                            $profilePicturePath = "assets/uploads/profile_pictures/" . $fileName;
                        }
                    }
                }
            }
        }
    }
    
    if ($error === "") {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
            $check->execute([
                ":username" => $username,
                ":email" => $email
            ]);

            if ($check->fetch()) {
                $error = "Username or email already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                if ($profilePictureColumnExists) {
                    $insert = $pdo->prepare(
                        "INSERT INTO users (full_name, email, username, phone, profile_picture, password_hash)
                         VALUES (:name, :email, :username, :phone, :profile_picture, :hash)"
                    );
                    $insert->execute([
                        ":name" => $name,
                        ":email" => $email,
                        ":username" => $username,
                        ":phone" => $phone,
                        ":profile_picture" => $profilePicturePath,
                        ":hash" => $hash
                    ]);
                } else {
                    $insert = $pdo->prepare(
                        "INSERT INTO users (full_name, email, username, phone, password_hash)
                         VALUES (:name, :email, :username, :phone, :hash)"
                    );
                    $insert->execute([
                        ":name" => $name,
                        ":email" => $email,
                        ":username" => $username,
                        ":phone" => $phone,
                        ":hash" => $hash
                    ]);
                }

                $message = "Registration successful. You can now login.";
            }
        } catch (PDOException $e) {
            error_log("Registration failed: " . $e->getMessage());
            $error = "Registration failed. Please try again.";
        }
    }
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
        .home-link {
            margin-top: 12px;
            text-align: center;
        }
        .home-link a {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #0b5ed7;
            background: #ffffff;
            color: #0b5ed7;
            text-decoration: none;
            font-weight: 600;
        }
        .home-link a:hover {
            background: #eef4ff;
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
        <?php if ($error !== ""): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required pattern="[A-Za-z0-9_]{4,20}" title="4-20 characters: letters, numbers, underscore only">

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="profile_picture">Profile Picture (optional)</label>
            <input type="file" id="profile_picture" name="profile_picture" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

            <button type="submit">Create Account</button>
        </form>
        <div class="auth-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
        <div class="home-link">
            <a href="index.php">Home</a>
        </div>
    </div>
</body>
</html>


