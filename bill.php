<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid Access!");
}

$park = $_POST['park'] ?? '';
$name = $_POST['name'] ?? '';
$date = $_POST['date'] ?? '';
$tickets = (int)($_POST['tickets'] ?? 0);
$parkingType = $_POST['parking_type'] ?? 'None';
$parkingFee = (int)($_POST['parking_fee'] ?? 0);
$total = (int)($_POST['total'] ?? 0);
$payment = $_POST['payment'] ?? 'N/A';

$ticketID = "GP" . rand(10000, 99999);
?>
<!DOCTYPE html>
<html>
<head>
<title>Bill</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">Government Park Booking</div>
</header>

<section class="gov-banner">
<img class="gov-emblem" src="assets/images/gov-emblem.svg" alt="National Emblem">
<div>
<p class="gov-title">Government Park Services Portal</p>
<p class="gov-subtitle">Official booking confirmation</p>
</div>
</section>

<div class="container">
<div class="card bill">

<img class="page-image" src="assets/images/hero-bill.svg" alt="Government building">
<h2>Booking Confirmation</h2>
<p class="lead">Your park ticket has been successfully booked.</p>

<p><strong>Ticket ID:</strong> <?php echo htmlspecialchars($ticketID); ?></p>
<p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
<p><strong>Park:</strong> <?php echo htmlspecialchars($park); ?></p>
<p><strong>Date:</strong> <?php echo htmlspecialchars($date); ?></p>
<p><strong>Tickets:</strong> <?php echo htmlspecialchars((string)$tickets); ?></p>
<p><strong>Parking:</strong> <?php echo htmlspecialchars($parkingType); ?></p>
<p><strong>Parking Fee:</strong> &#8377;<?php echo htmlspecialchars((string)$parkingFee); ?></p>
<p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment); ?></p>
<p><strong>Total Paid:</strong> &#8377;<?php echo htmlspecialchars((string)$total); ?></p>

<p class="note">Please show this ticket QR code at the entry gate. Keep a screenshot saved in your phone for faster verification.</p>

<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($ticketID); ?>" alt="QR Code">

<button onclick="window.print()" class="btn">Print Ticket</button>

</div>
</div>

</body>
</html>
