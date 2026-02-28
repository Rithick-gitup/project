<?php
$totalBookings = 0;
$totalVisitors = 0;
$todayVisitors = 0;
$statsError = "";

try {
    require_once __DIR__ . "/ticket_db.php";

    $summaryStmt = $bookingPdo->query(
        "SELECT COUNT(*) AS total_bookings, COALESCE(SUM(tickets_count), 0) AS total_visitors FROM bookings"
    );
    $summary = $summaryStmt->fetch();
    if ($summary) {
        $totalBookings = (int) $summary["total_bookings"];
        $totalVisitors = (int) $summary["total_visitors"];
    }

    $todayStmt = $bookingPdo->query(
        "SELECT COALESCE(SUM(tickets_count), 0) AS today_visitors FROM bookings WHERE visit_date = CURDATE()"
    );
    $today = $todayStmt->fetch();
    if ($today) {
        $todayVisitors = (int) $today["today_visitors"];
    }
} catch (Throwable $e) {
    $statsError = "Booking statistics unavailable. Configure ticket booking database first.";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<div>Admin Dashboard</div>
<nav>
<a href="index.php">Home</a>
</nav>
</header>

<section class="gov-banner">
<img class="gov-emblem" src="assets/images/gov-emblem.svg" alt="National Emblem">
<div>
<p class="gov-title">Government Park Services Portal</p>
<p class="gov-subtitle">Administrative monitoring panel</p>
</div>
</section>

<div class="container">
<div class="card">
<img class="page-image" src="assets/images/hero-admin.svg" alt="Government office">
<h3>System Overview</h3>
<p>Total Parks: 3</p>
<p>Status: Online</p>
<?php if ($statsError !== ""): ?>
<p class="error-text"><?php echo htmlspecialchars($statsError); ?></p>
<?php else: ?>
<p>Total Ticket Bookings: <?php echo htmlspecialchars((string) $totalBookings); ?></p>
<p>Total People Visiting: <?php echo htmlspecialchars((string) $totalVisitors); ?></p>
<p>Visitors for Today: <?php echo htmlspecialchars((string) $todayVisitors); ?></p>
<?php endif; ?>
</div>
</div>

</body>
</html>
