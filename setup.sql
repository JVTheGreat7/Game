-- =========================
-- PC PARTS E-COMMERCE SYSTEM
-- MySQL Database Setup Script
-- =========================

-- =========================
-- CREATE DATABASE
-- =========================
DROP DATABASE IF EXISTS pc_parts_store;
CREATE DATABASE pc_parts_store;
USE pc_parts_store;

-- =========================
-- 1. USERS (Customer, Staff, Admin)
-- =========================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'staff', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- 2. CATEGORIES
-- =========================
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- =========================
-- 3. PRODUCTS
-- =========================
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    brand VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    specifications TEXT,
    status ENUM('available', 'out_of_stock') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- =========================
-- 4. PRODUCT IMAGES
-- =========================
CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- =========================
-- 5. INVENTORY
-- =========================
CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_product (product_id)
);

-- =========================
-- 6. ORDERS
-- =========================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled')
        DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- =========================
-- 7. ORDER ITEMS
-- =========================
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- =========================
-- 8. PAYMENTS
-- =========================
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('cash', 'card', 'gcash', 'paypal') DEFAULT 'cash',
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- =========================
-- 9. REVIEWS
-- =========================
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- =========================
-- 10. INVENTORY LOGS
-- =========================
CREATE TABLE inventory_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    change_quantity INT NOT NULL,
    reason VARCHAR(100),
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- =========================
-- SAMPLE DATA - CATEGORIES
-- =========================
INSERT INTO categories (category_name) VALUES
('CPUs'),
('Motherboards'),
('RAM Memory'),
('Storage Drives'),
('Power Supplies'),
('Cooling Systems'),
('Graphics Cards'),
('Cases');

-- =========================
-- SAMPLE DATA - PRODUCTS
-- =========================
INSERT INTO products (category_id, product_name, brand, price, specifications, status) VALUES
(1, 'Intel Core i9-13900K', 'Intel', 589.99, 'Cores: 24, Base Clock: 3.0 GHz, TDP: 253W', 'available'),
(1, 'AMD Ryzen 9 7950X', 'AMD', 549.99, 'Cores: 16, Base Clock: 4.5 GHz, TDP: 162W', 'available'),
(2, 'ASUS ROG Maximus Z790-E', 'ASUS', 349.99, 'Socket: LGA1700, Form Factor: ATX, DDR5 Support', 'available'),
(2, 'MSI MAG B650E Tomahawk', 'MSI', 289.99, 'Socket: AM5, Form Factor: ATX, DDR5 Support', 'available'),
(3, 'Corsair Vengeance DDR5 32GB', 'Corsair', 149.99, 'Capacity: 32GB, Speed: 6000MHz, Type: DDR5', 'available'),
(3, 'Kingston Fury Beast 16GB DDR4', 'Kingston', 59.99, 'Capacity: 16GB, Speed: 3200MHz, Type: DDR4', 'available'),
(4, 'Samsung 990 Pro 1TB NVMe', 'Samsung', 99.99, 'Capacity: 1TB, Type: NVMe M.2, Speed: 7100MB/s', 'available'),
(4, 'WD Black SN850X 2TB', 'Western Digital', 149.99, 'Capacity: 2TB, Type: NVMe M.2, Speed: 7100MB/s', 'available'),
(5, 'Corsair RM1000e 1000W', 'Corsair', 199.99, 'Wattage: 1000W, Certification: 80+ Gold, Modular', 'available'),
(5, 'EVGA SuperNOVA 850 GA 850W', 'EVGA', 129.99, 'Wattage: 850W, Certification: 80+ Gold, Modular', 'available'),
(6, 'Noctua NH-D15 Chromax', 'Noctua', 99.99, 'Type: Air Cooler, TDP: 250W, Socket: LGA1700/AM5', 'available'),
(6, 'CORSAIR iCUE H150i Elite 360mm', 'Corsair', 149.99, 'Type: Liquid Cooler, 360mm Radiator, RGB', 'available'),
(7, 'NVIDIA RTX 4090', 'NVIDIA', 1599.99, 'VRAM: 24GB GDDR6X, CUDA Cores: 16384, TDP: 450W', 'available'),
(7, 'AMD Radeon RX 7900 XTX', 'AMD', 899.99, 'VRAM: 24GB GDDR6, Stream Processors: 6144, TDP: 420W', 'available'),
(8, 'NZXT H7 Flow RGB', 'NZXT', 119.99, 'Type: Mid Tower ATX, Tempered Glass, RGB Fans', 'available'),
(8, 'Corsair Crystal 570X RGB', 'Corsair', 129.99, 'Type: Mid Tower ATX, Tempered Glass, RGB Lighting', 'available');

-- =========================
-- SAMPLE DATA - INVENTORY
-- =========================
INSERT INTO inventory (product_id, quantity) VALUES
(1, 15),
(2, 12),
(3, 20),
(4, 18),
(5, 30),
(6, 50),
(7, 25),
(8, 22),
(9, 10),
(10, 14),
(11, 35),
(12, 16),
(13, 8),
(14, 9),
(15, 25),
(16, 28);

-- =========================
-- SAMPLE DATA - PRODUCT IMAGES
-- =========================
INSERT INTO product_images (product_id, image_url) VALUES
(1, 'https://via.placeholder.com/300x300?text=Intel+i9-13900K'),
(2, 'https://via.placeholder.com/300x300?text=AMD+Ryzen+7950X'),
(3, 'https://via.placeholder.com/300x300?text=ASUS+ROG+Z790E'),
(4, 'https://via.placeholder.com/300x300?text=MSI+B650E+Tomahawk'),
(5, 'https://via.placeholder.com/300x300?text=Corsair+Vengeance+DDR5'),
(6, 'https://via.placeholder.com/300x300?text=Kingston+Fury+Beast'),
(7, 'https://via.placeholder.com/300x300?text=Samsung+990+Pro'),
(8, 'https://via.placeholder.com/300x300?text=WD+Black+SN850X'),
(9, 'https://via.placeholder.com/300x300?text=Corsair+RM1000e'),
(10, 'https://via.placeholder.com/300x300?text=EVGA+SuperNOVA+850'),
(11, 'https://via.placeholder.com/300x300?text=Noctua+NH-D15'),
(12, 'https://via.placeholder.com/300x300?text=Corsair+iCUE+H150i'),
(13, 'https://via.placeholder.com/300x300?text=RTX+4090'),
(14, 'https://via.placeholder.com/300x300?text=RX+7900+XTX'),
(15, 'https://via.placeholder.com/300x300?text=NZXT+H7+Flow'),
(16, 'https://via.placeholder.com/300x300?text=Corsair+Crystal+570X');

-- =========================
-- SAMPLE DATA - USERS
-- =========================
-- Password: admin123
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Admin User', 'admin@pcparts.local', '$2y$10$n9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P6kFDm', 'admin');

-- Password: staff123
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Staff Member', 'staff@pcparts.local', '$2y$10$PIxl3lQK5QrNqXVJqLT5Ce2T2aDSr1g9W9JjlS2qTVQJJYL3/lIIC', 'staff');

-- Password: customer123
INSERT INTO users (full_name, email, password_hash, role) VALUES
('John Doe', 'john@example.com', '$2y$10$LjGF.CqHvC7x7vXVDPPd0Oa1TjA6nPrRFvGvp8YSGR1S6hXXCJ5tG', 'customer');

-- Password: customer123
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Jane Smith', 'jane@example.com', '$2y$10$LjGF.CqHvC7x7vXVDPPd0Oa1TjA6nPrRFvGvp8YSGR1S6hXXCJ5tG', 'customer');

-- =========================
-- SAMPLE DATA - REVIEWS
-- =========================
INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 3, 5, 'Excellent CPU! Very fast and reliable. Great for gaming and productivity.'),
(2, 4, 5, 'Amazing performance. Best processor I have used so far.'),
(5, 3, 4, 'Good RAM, fast speeds. Slightly pricey but worth it.'),
(7, 4, 5, 'The Samsung SSD is incredibly fast. Very happy with this purchase.'),
(13, 3, 5, 'Fantastic GPU! Handles everything I throw at it. Highly recommended.');
