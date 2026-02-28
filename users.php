<?php
session_start();
require_once __DIR__ . "/db.php";

if (empty($_SESSION["logged_in"])) {
    header("Location: auth.php");
    exit;
}

$error = "";
$message = isset($_GET["updated"]) && $_GET["updated"] === "1"
    ? "Profile updated successfully."
    : "";
$user = null;
$profilePictureColumnExists = false;

try {
    $column = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    $profilePictureColumnExists = (bool) $column->fetch();

    $selectFields = "id, full_name, email, username, phone, created_at";
    if ($profilePictureColumnExists) {
        $selectFields .= ", profile_picture";
    }

    $stmt = $pdo->prepare("SELECT {$selectFields} FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([":username" => $_SESSION["username"]]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error = "Unable to load your details.";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
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
<p class="gov-title">My Profile</p>
<p class="gov-subtitle">Your registered details</p>
</div>
</section>

<div class="container">
<div class="card">
<h2 class="profile-title">My Details</h2>
<?php if ($message !== ""): ?>
    <p class="success-text"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<?php if ($error !== ""): ?>
    <p class="error-text"><?php echo htmlspecialchars($error); ?></p>
<?php elseif (!$user): ?>
    <p>No details found.</p>
<?php else: ?>
    <?php $profilePicture = $user["profile_picture"] ?? ""; ?>
    <div class="profile-picture-wrap">
        <div class="profile-picture-frame">
            <?php if ($profilePicture !== "" && file_exists(__DIR__ . "/" . $profilePicture)): ?>
                <img class="profile-picture" src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile picture">
            <?php else: ?>
                <div class="profile-avatar-fallback"><?php echo strtoupper(substr($user["username"], 0, 1)); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <p class="profile-name"><?php echo htmlspecialchars($user["full_name"]); ?></p>
    <div class="profile-details">
        <div class="profile-row"><span class="profile-label">ID</span><span class="profile-value"><?php echo htmlspecialchars($user["id"]); ?></span></div>
        <div class="profile-row"><span class="profile-label">Full Name</span><span class="profile-value"><?php echo htmlspecialchars($user["full_name"]); ?></span></div>
        <div class="profile-row"><span class="profile-label">Email</span><span class="profile-value"><?php echo htmlspecialchars($user["email"]); ?></span></div>
        <div class="profile-row"><span class="profile-label">Username</span><span class="profile-value"><?php echo htmlspecialchars($user["username"]); ?></span></div>
        <div class="profile-row"><span class="profile-label">Phone</span><span class="profile-value"><?php echo htmlspecialchars($user["phone"]); ?></span></div>
        <div class="profile-row"><span class="profile-label">Created At</span><span class="profile-value"><?php echo htmlspecialchars($user["created_at"]); ?></span></div>
    </div>
    <div class="profile-actions">
        <a href="edit_profile.php" class="btn profile-action-btn">Edit Details</a>
    </div>
<?php endif; ?>
</div>
</div>

<footer>&copy; 2026 Government Park Department</footer>

</body>
</html>
