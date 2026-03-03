-- Mobile Repository Database Setup
-- SQL file to create database and tables for the mobile phone repository system

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS mobile_repo;

-- Use the database
USE mobile_repo;

-- Create UserLogin table for authentication
CREATE TABLE IF NOT EXISTS UserLogin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userid VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create mobile_phone table for storing mobile phone data
CREATE TABLE IF NOT EXISTS mobile_phone (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mobile_name VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample admin user (password: admin123)
-- Password hash for 'admin123': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO UserLogin (userid, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE userid = userid;

-- Insert sample mobile phone data
INSERT INTO mobile_phone (mobile_name, brand, price) VALUES 
('iPhone 14', 'Apple', 85000),
('Samsung Galaxy S23', 'Samsung', 75000),
('OnePlus 11', 'OnePlus', 55000),
('Xiaomi Redmi Note 12', 'Xiaomi', 18000),
('Realme 10 Pro', 'Realme', 22000),
('Vivo V27', 'Vivo', 35000),
('Oppo Reno 8', 'Oppo', 28000),
('Motorola Edge 40', 'Motorola', 25000),
('Nokia X30', 'Nokia', 42000),
('Google Pixel 7', 'Google', 58000)
ON DUPLICATE KEY UPDATE mobile_name = mobile_name;

-- Display success message
SELECT 'Database setup completed successfully!' AS message;
