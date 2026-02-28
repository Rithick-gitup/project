<?php
$BOOKING_DB_HOST = "localhost";
$BOOKING_DB_NAME = "park_ticket_db";
$BOOKING_DB_USER = "root";
$BOOKING_DB_PASS = "";

$bookingDsn = "mysql:host={$BOOKING_DB_HOST};dbname={$BOOKING_DB_NAME};charset=utf8mb4";
$bookingOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$bookingPdo = new PDO($bookingDsn, $BOOKING_DB_USER, $BOOKING_DB_PASS, $bookingOptions);
