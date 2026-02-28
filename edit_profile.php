<?php
session_start();
require_once __DIR__ . "/db.php";

if (empty($_SESSION["logged_in"])) {
    header("Location: auth.php");
    exit;
}

$error = "";
$user = null;
$profilePictureColumnExists = false;

function hasProfilePictureColumn(PDO $pdo): bool
{
    try {
        $column = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
        return (bool) $column->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

try {
    $profilePictureColumnExists = hasProfilePictureColumn($pdo);

    $selectFields = "id, full_name, email, username, phone";
    if ($profilePictureColumnExists) {
        $selectFields .= ", profile_picture";
    }

    $stmt = $pdo->prepare("SELECT {$selectFields} FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([":username" => $_SESSION["username"]]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Unable to load your profile.";
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        $fullName = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $phone = trim($_POST["phone"] ?? "");

        if ($fullName === "" || $email === "" || $username === "" || $phone === "") {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $username)) {
            $error = "Username must be 4-20 characters and only use letters, numbers, or underscore.";
        }

        $newProfilePicturePath = $user["profile_picture"] ?? null;
        if ($error === "" && $profilePictureColumnExists && !empty($_FILES["profile_picture"]["name"])) {
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
                                $newProfilePicturePath = "assets/uploads/profile_pictures/" . $fileName;
                            }
                        }
                    }
                }
            }
        } else {
            $check = $pdo->prepare(
                "SELECT id FROM users WHERE (email = :email OR username = :username) AND id <> :id LIMIT 1"
            );
            $check->execute([
                ":email" => $email,
                ":username" => $username,
                ":id" => $user["id"]
            ]);

            if ($check->fetch()) {
                $error = "Email or username is already in use.";
            } else {
                if ($profilePictureColumnExists) {
                    $update = $pdo->prepare(
                        "UPDATE users
                         SET full_name = :full_name, email = :email, username = :username, phone = :phone, profile_picture = :profile_picture
                         WHERE id = :id"
                    );
                    $update->execute([
                        ":full_name" => $fullName,
                        ":email" => $email,
                        ":username" => $username,
                        ":phone" => $phone,
                        ":profile_picture" => $newProfilePicturePath,
                        ":id" => $user["id"]
                    ]);
                } else {
                    $update = $pdo->prepare(
                        "UPDATE users
                         SET full_name = :full_name, email = :email, username = :username, phone = :phone
                         WHERE id = :id"
                    );
                    $update->execute([
                        ":full_name" => $fullName,
                        ":email" => $email,
                        ":username" => $username,
                        ":phone" => $phone,
                        ":id" => $user["id"]
                    ]);
                }

                $oldProfilePicture = $user["profile_picture"] ?? "";
                if (
                    $profilePictureColumnExists &&
                    is_string($oldProfilePicture) &&
                    $oldProfilePicture !== "" &&
                    $newProfilePicturePath !== $oldProfilePicture &&
                    str_starts_with($oldProfilePicture, "assets/uploads/profile_pictures/")
                ) {
                    $oldPath = __DIR__ . "/" . $oldProfilePicture;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $_SESSION["username"] = $username;
                header("Location: users.php?updated=1");
                exit;
            }
        }

        $user["full_name"] = $fullName;
        $user["email"] = $email;
        $user["username"] = $username;
        $user["phone"] = $phone;
        if ($profilePictureColumnExists) {
            $user["profile_picture"] = $newProfilePicturePath;
        }
    }
} catch (PDOException $e) {
    $error = "Unable to update your profile.";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<link rel="stylesheet" href="style.css?v=<?php echo filemtime(__DIR__ . '/style.css'); ?>">
</head>
<body>

<header>
<div>Gov Park Portal</div>
<nav>
<a href="index.php">Home</a>
<a href="parks.php">Parks</a>
<a href="admin.php">Admin</a>
<a href="users.php">Profile</a>
<?php if (($_SESSION["username"] ?? "") === "iam_rithick"): ?>
<a href="bookings_details.php">Bookings</a>
<?php endif; ?>
<a href="logout.php">Logout</a>
</nav>
</header>

<section class="gov-banner">
<div class="user-banner">Welcome, <?php echo htmlspecialchars($_SESSION["username"] ?? "User"); ?></div>
<img class="gov-emblem" src="assets/images/gov-emblem.svg" alt="National Emblem">
<div>
<p class="gov-title">Edit Profile</p>
<p class="gov-subtitle">Update your registered details</p>
</div>
</section>

<div class="container">
<div class="card">
<h2 class="profile-title">Edit Details</h2>
<?php if ($error !== ""): ?>
    <p class="error-text"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($user): ?>
    <?php $profilePicture = $user["profile_picture"] ?? ""; ?>
    <?php if ($profilePictureColumnExists): ?>
    <div class="profile-picture-wrap">
        <div class="profile-picture-frame">
            <?php if ($profilePicture !== "" && file_exists(__DIR__ . "/" . $profilePicture)): ?>
                <img class="profile-picture" src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile picture">
            <?php else: ?>
                <div class="profile-avatar-fallback"><?php echo strtoupper(substr($user["username"], 0, 1)); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <form method="post" action="edit_profile.php" class="profile-edit-form" enctype="multipart/form-data">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($user["full_name"]); ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user["email"]); ?>">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required pattern="[A-Za-z0-9_]{4,20}" title="4-20 characters: letters, numbers, underscore only" value="<?php echo htmlspecialchars($user["username"]); ?>">

        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" required value="<?php echo htmlspecialchars($user["phone"]); ?>">

        <?php if ($profilePictureColumnExists): ?>
        <label for="profile_picture">Change Profile Picture (optional)</label>
        <input type="file" id="profile_picture" name="profile_picture" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <?php endif; ?>

        <button type="submit" class="btn">Save Changes</button>
        <a href="users.php" class="btn profile-cancel-btn">Cancel</a>
    </form>
<?php endif; ?>
</div>
</div>

<footer>&copy; 2026 Government Park Department</footer>

</body>
</html>
