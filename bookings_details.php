<?php
session_start();

if (empty($_SESSION["logged_in"])) {
    header("Location: auth.php");
    exit;
}

if (($_SESSION["username"] ?? "") !== "iam_rithick") {
    http_response_code(403);
    die("Access denied.");
}

require_once __DIR__ . "/ticket_db.php";

$error = "";
$bookings = [];

try {
    $stmt = $bookingPdo->query(
        "SELECT ticket_id, park_name, visitor_name, visitor_email, visit_date, tickets_count, parking_type, payment_method, total_amount, created_at
         FROM bookings
         ORDER BY created_at DESC"
    );
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Unable to load booking details.";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Booking Details</title>
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
<a href="bookings_details.php">Bookings</a>
<a href="logout.php">Logout</a>
</nav>
</header>

<section class="gov-banner">
<div class="user-banner">Welcome, <?php echo htmlspecialchars($_SESSION["username"] ?? "User"); ?></div>
<img class="gov-emblem" src="assets/images/gov-emblem.svg" alt="National Emblem">
<div>
<p class="gov-title">Booking Details</p>
<p class="gov-subtitle">All confirmed ticket bookings</p>
</div>
</section>

<div class="container">
<div class="card card-wide">
<h2>Bookings List</h2>
<?php if ($error !== ""): ?>
    <p class="error-text"><?php echo htmlspecialchars($error); ?></p>
<?php elseif (count($bookings) === 0): ?>
    <p>No booking data available.</p>
<?php else: ?>
    <div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Park</th>
                <th>Visitor</th>
                <th>Email</th>
                <th>Visit Date</th>
                <th>Tickets</th>
                <th>Parking</th>
                <th>Payment</th>
                <th>Total</th>
                <th>Booked At</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking["ticket_id"]); ?></td>
                <td><?php echo htmlspecialchars($booking["park_name"]); ?></td>
                <td><?php echo htmlspecialchars($booking["visitor_name"]); ?></td>
                <td><?php echo htmlspecialchars($booking["visitor_email"]); ?></td>
                <td><?php echo htmlspecialchars($booking["visit_date"]); ?></td>
                <td><?php echo htmlspecialchars((string) $booking["tickets_count"]); ?></td>
                <td><?php echo htmlspecialchars($booking["parking_type"]); ?></td>
                <td><?php echo htmlspecialchars($booking["payment_method"]); ?></td>
                <td><?php echo htmlspecialchars((string) $booking["total_amount"]); ?></td>
                <td><?php echo htmlspecialchars($booking["created_at"]); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
</div>
</div>

<footer>&copy; 2026 Government Park Department</footer>

</body>
</html>
