<?php
if (!isset($_GET['park']) || !isset($_GET['price'])) {
    header("Location: parks.php");
    exit();
}

$park = $_GET['park'];
$price = (int)$_GET['price'];
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
<title>Booking</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<div>Booking - <?php echo htmlspecialchars($park); ?></div>
<nav>
<a href="index.php">Home</a>
</nav>
</header>

<section class="gov-banner">
<img class="gov-emblem" src="assets/images/gov-emblem.svg" alt="National Emblem">
<div>
<p class="gov-title">Government Park Services Portal</p>
<p class="gov-subtitle">Official visitor details form</p>
</div>
</section>

<div class="container">
<div class="card">

<img class="page-image" src="assets/images/hero-booking.svg" alt="Park pathway">
<h2>Visitor Details</h2>
<p class="lead">Fill in the details below to reserve your park entry tickets.</p>
<p>Ticket price for this park: <strong>&#8377;<?php echo htmlspecialchars((string)$price); ?></strong> per visitor.</p>
<p class="note">Please enter the same email address you use regularly. Booking confirmation will be tied to these details.</p>

<form action="payment.php" method="POST">
<input type="hidden" name="park" value="<?php echo htmlspecialchars($park); ?>">
<input type="hidden" name="price" value="<?php echo htmlspecialchars((string)$price); ?>">

<input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email Address" required>
<input type="date" name="date" min="<?php echo htmlspecialchars($today); ?>" required>
<input type="number" name="tickets" placeholder="Number of Tickets" min="1" required>
<label for="parking_type">Parking Requirement</label>
<select name="parking_type" id="parking_type" required>
<option value="None" selected>No Parking Needed</option>
<option value="Two Wheeler">Two Wheeler Parking - &#8377;20</option>
<option value="Car">Car Parking - &#8377;50</option>
<option value="Bus">Bus/Van Parking - &#8377;100</option>
</select>

<p class="note">Important: Carry a valid ID proof during your visit. One ticket is required per person.</p>
<button type="submit" class="btn">Proceed to Payment</button>
</form>

</div>
</div>

</body>
</html>
