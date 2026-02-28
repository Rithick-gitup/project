<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: parks.php");
    exit();
}

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
$email = $_POST['email'] ?? '';
$date = $_POST['date'] ?? '';
$tickets = (int)($_POST['tickets'] ?? 0);
$price = (int)($_POST['price'] ?? 0);
$parkingType = $_POST['parking_type'] ?? 'None';

$parkingRates = [
    'None' => 0,
    'Two Wheeler' => 20,
    'Car' => 50,
    'Bus' => 100
];

if (!array_key_exists($parkingType, $parkingRates)) {
    $parkingType = 'None';
}

if (
    $park === '' ||
    $name === '' ||
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !isValidFutureOrTodayDate($date) ||
    $tickets < 1 ||
    $price < 0
) {
    die("Invalid booking details. Please choose today or an upcoming date.");
}

$parkingFee = $parkingRates[$parkingType];
$ticketTotal = $tickets * $price;
$total = $ticketTotal + $parkingFee;
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment</title>
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
<p class="gov-subtitle">Secure payment processing</p>
</div>
</section>

<div class="container">
<div class="card">

<img class="page-image" src="assets/images/hero-payment.svg" alt="Government landmark">
<h2>Select Payment Method</h2>
<p class="lead">Review your booking summary and choose a payment option to confirm your ticket.</p>

<div class="summary-box">
    <p><strong>Park:</strong> <?php echo htmlspecialchars($park); ?></p>
    <p><strong>Visitor Name:</strong> <?php echo htmlspecialchars($name); ?></p>
    <p><strong>Visitor Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Visit Date:</strong> <?php echo htmlspecialchars($date); ?></p>
    <p><strong>Tickets:</strong> <?php echo htmlspecialchars((string)$tickets); ?></p>
    <p><strong>Price Per Ticket:</strong> &#8377;<?php echo htmlspecialchars((string)$price); ?></p>
    <p><strong>Ticket Subtotal:</strong> &#8377;<?php echo htmlspecialchars((string)$ticketTotal); ?></p>
    <p><strong>Parking:</strong> <?php echo htmlspecialchars($parkingType); ?></p>
    <p><strong>Parking Fee:</strong> &#8377;<?php echo htmlspecialchars((string)$parkingFee); ?></p>
    <p><strong>Amount Payable:</strong> &#8377;<?php echo htmlspecialchars((string)$total); ?></p>
</div>

<form action="bill.php" method="POST">
<input type="hidden" name="park" value="<?php echo htmlspecialchars($park); ?>">
<input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
<input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
<input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
<input type="hidden" name="tickets" value="<?php echo htmlspecialchars((string)$tickets); ?>">
<input type="hidden" name="parking_type" value="<?php echo htmlspecialchars($parkingType); ?>">
<input type="hidden" name="parking_fee" value="<?php echo htmlspecialchars((string)$parkingFee); ?>">
<input type="hidden" name="total" value="<?php echo htmlspecialchars((string)$total); ?>">

<div class="payment-option">
    <input type="radio" id="credit_card" name="payment" value="Credit Card" required>
    <label for="credit_card">Credit Card</label>
</div>

<div class="payment-option">
    <input type="radio" id="debit_card" name="payment" value="Debit Card" required>
    <label for="debit_card">Debit Card</label>
</div>

<div class="payment-option">
    <input type="radio" id="upi" name="payment" value="UPI" required>
    <label for="upi">UPI</label>
</div>

<div class="payment-option">
    <input type="radio" id="net_banking" name="payment" value="Net Banking" required>
    <label for="net_banking">Net Banking</label>
</div>

<p class="note">By proceeding, you confirm that all booking details are correct and agree to park entry rules.</p>
<button class="btn">Confirm Payment</button>

</form>

</div>
</div>

</body>
</html>
