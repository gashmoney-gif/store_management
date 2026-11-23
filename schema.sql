-- Store Management App — database schema
-- Import this file once (phpMyAdmin > Import, or `mysql -u root -p < schema.sql`)

CREATE DATABASE IF NOT EXISTS store_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE store_management;

CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(100) NOT NULL,
    role          ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    item_code    VARCHAR(50) NOT NULL UNIQUE,
    item_name    VARCHAR(150) NOT NULL,
    location     VARCHAR(100) NOT NULL,
    quantity     INT NOT NULL DEFAULT 0,
    updated_by   VARCHAR(50) NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_code (item_code),
    INDEX idx_location (location),
    INDEX idx_item_name (item_name)
) ENGINE=InnoDB;

-- Default accounts — CHANGE THESE PASSWORDS after first login.
-- admin / Admin123!   (role: admin — can add/edit/delete)
-- viewer / Viewer123! (role: viewer — read-only, can search)
INSERT INTO users (username, password_hash, full_name, role) VALUES
('admin',  '$2y$10$XGM3kNDepBvzD6/3n6tH7.Pg/lgchq9YbXLNN1lMMnwADnUAPYHpW', 'Store Admin', 'admin'),
('viewer', '$2y$10$4MCMv6wihwh/TnlMaG3TwO0AXufelmpuQnn93jn..YQwR6cc99L9e', 'Store Viewer', 'viewer');

-- Sample inventory rows (optional — delete if not needed)
INSERT INTO inventory (item_code, item_name, location, quantity, updated_by) VALUES
('SKU-1001', 'Bag of Cement (50kg)', 'Warehouse A - Rack 3', 120, 'admin'),
('SKU-1002', 'Roofing Sheets (Long Span)', 'Warehouse B - Rack 1', 45, 'admin'),
('SKU-1003', 'Iron Rods (12mm)', 'Yard - Section 2', 300, 'admin');
