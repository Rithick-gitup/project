CREATE DATABASE IF NOT EXISTS park_ticket_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE park_ticket_db;

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id VARCHAR(30) NOT NULL UNIQUE,
  park_name VARCHAR(120) NOT NULL,
  visitor_name VARCHAR(120) NOT NULL,
  visitor_email VARCHAR(150) NOT NULL,
  visit_date DATE NOT NULL,
  tickets_count INT NOT NULL,
  parking_type VARCHAR(30) NOT NULL DEFAULT 'None',
  parking_fee INT NOT NULL DEFAULT 0,
  total_amount INT NOT NULL,
  payment_method VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_visit_date (visit_date),
  INDEX idx_park_name (park_name)
);
