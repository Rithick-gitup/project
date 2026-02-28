<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid Access!");
}

require_once __DIR__ . "/ticket_db.php";

function isValidFutureOrTodayDate(string $date): bool
{
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date) {
        return false;
    }

    $today = (new DateTime('today'))->format('Y-m-d');
    return $date >= $today;
}

$park = $_POST['park'] ?? '';
$name = $_POST['name'] ?? '';
$email = trim($_POST['email'] ?? '');
$date = $_POST['date'] ?? '';
$tickets = (int)($_POST['tickets'] ?? 0);
$parkingType = $_POST['parking_type'] ?? 'None';
$parkingFee = (int)($_POST['parking_fee'] ?? 0);
$total = (int)($_POST['total'] ?? 0);
$payment = $_POST['payment'] ?? 'N/A';

$ticketID = "GP" . strtoupper(bin2hex(random_bytes(4)));
$saveError = "";

if (
    $park === "" ||
    $name === "" ||
    $email === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !isValidFutureOrTodayDate($date) ||
    $tickets < 1 ||
    $total < 0 ||
    $payment === "N/A"
) {
    die("Invalid booking details submitted. Past dates are not allowed.");
}

try {
    $insert = $bookingPdo->prepare(
        "INSERT INTO bookings
         (ticket_id, park_name, visitor_name, visitor_email, visit_date, tickets_count, parking_type, parking_fee, total_amount, payment_method)
         VALUES
         (:ticket_id, :park_name, :visitor_name, :visitor_email, :visit_date, :tickets_count, :parking_type, :parking_fee, :total_amount, :payment_method)"
    );

    $insert->execute([
        ":ticket_id" => $ticketID,
        ":park_name" => $park,
        ":visitor_name" => $name,
        ":visitor_email" => $email,
        ":visit_date" => $date,
        ":tickets_count" => $tickets,
        ":parking_type" => $parkingType,
        ":parking_fee" => $parkingFee,
        ":total_amount" => $total,
        ":payment_method" => $payment
    ]);
} catch (PDOException $e) {
    $saveError = "Booking saved in bill view, but database write failed.";
}
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
    <nav>
    <a href="index.php">Home</a>
    </nav>
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
<p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
<p><strong>Park:</strong> <?php echo htmlspecialchars($park); ?></p>
<p><strong>Date:</strong> <?php echo htmlspecialchars($date); ?></p>
<p><strong>Tickets:</strong> <?php echo htmlspecialchars((string)$tickets); ?></p>
<p><strong>Parking:</strong> <?php echo htmlspecialchars($parkingType); ?></p>
<p><strong>Parking Fee:</strong> &#8377;<?php echo htmlspecialchars((string)$parkingFee); ?></p>
<p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment); ?></p>
<p><strong>Total Paid:</strong> &#8377;<?php echo htmlspecialchars((string)$total); ?></p>
<?php if ($saveError !== ""): ?>
<p class="error-text"><?php echo htmlspecialchars($saveError); ?></p>
<?php endif; ?>

<p class="note">Please show this ticket QR code at the entry gate. Keep a screenshot saved in your phone for faster verification.</p>

<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($ticketID); ?>" alt="QR Code">

<button onclick="window.print()" class="btn">Print Ticket</button>

</div>
</div>

</body>
</html>
