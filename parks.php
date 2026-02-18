<!DOCTYPE html>
<html>
<head>
<title>Select Park</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<div>Select Park</div>
<nav>
<a href="index.php">Home</a>
</nav>
</header>

<section class="gov-banner">
<img class="gov-emblem" src="assets/images/gov-emblem.svg" alt="National Emblem">
<div>
<p class="gov-title">Government Park Services Portal</p>
<p class="gov-subtitle">Verified ticketing for public parks</p>
</div>
</section>

<div class="container">
<div class="card">
<h2>Available Parks</h2>
<p class="lead">Select a park below to continue your ticket booking.</p>

<div class="park-box">
<img class="park-image" src="assets/images/park-botanical.svg" alt="Botanical Garden">
<h3>Botanical Garden</h3>
<p><strong>&#8377;50</strong> per ticket</p>
<p>A calm green space with rare plants, themed flower sections, and family-friendly walking paths.</p>
<p>Visiting hours: 8:00 AM to 6:00 PM</p>
<a href="booking.php?park=Botanical%20Garden&price=50">
<button class="btn">Book Botanical Garden</button>
</a>
</div>

<div class="park-box">
<img class="park-image" src="assets/images/park-eco.svg" alt="Eco Park">
<h3>Eco Park</h3>
<p><strong>&#8377;30</strong> per ticket</p>
<p>Ideal for nature lovers, with shaded trails, lake-side seating, and open-air relaxation zones.</p>
<p>Visiting hours: 7:30 AM to 7:00 PM</p>
<a href="booking.php?park=Eco%20Park&price=30">
<button class="btn">Book Eco Park</button>
</a>
</div>

<div class="park-box">
<img class="park-image" src="assets/images/park-adventure.svg" alt="Adventure Park">
<h3>Adventure Park</h3>
<p><strong>&#8377;100</strong> per ticket</p>
<p>Great for groups and young visitors with activity areas, rope courses, and outdoor games.</p>
<p>Visiting hours: 9:00 AM to 8:00 PM</p>
<a href="booking.php?park=Adventure%20Park&price=100">
<button class="btn">Book Adventure Park</button>
</a>
</div>

<p class="note">Note: Tickets are valid only for the selected date and park. Entry staff may verify your ticket ID at the gate.</p>

</div>
</div>

</body>
</html>
