CREATE DATABASE IF NOT EXISTS park_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE park_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL,
  profile_picture VARCHAR(255) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- For existing databases created before this change, run:
-- ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER phone;
