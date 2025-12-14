```sql
CREATE DATABASE boys_clothing;
USE boys_clothing;

-- Users table (Buyers, Sellers, Admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('buyer', 'seller', 'admin') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Plain text for testing, hash in production
    email VARCHAR(100) NOT NULL UNIQUE,
    nid VARCHAR(20), -- For sellers
    certificate VARCHAR(255), -- File path for chairman certificate
    verified ENUM('pending', 'approved', 'rejected') DEFAULT 'pending', -- Seller verification
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table (changed 'condition' to 'item_condition')
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('polo', 'casual_shirt', 'formal_shirt', 'tshirt', 'shoes', 'hygiene') NOT NULL,
    size ENUM('S', 'M', 'L', 'XL', 'XXL') NOT NULL,
    item_condition ENUM('new', 'like_new', 'good', 'fair', 'worn') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    images JSON, -- Store array of image paths
    laundry_memo VARCHAR(255), -- File path for hygiene products
    hygiene_verified ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    status ENUM('available', 'sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT,
    product_id INT,
    status ENUM('pending', 'shipped', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Wishlist table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Chat messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    product_id INT,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Returns table
CREATE TABLE returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_decision ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Payouts table
CREATE TABLE payouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

-- Insert admin user (plain text password for testing)
INSERT INTO users (role, username, password, email, verified)
VALUES ('admin', 'admin_user', 'admin123', 'admin@boysclothing.com', 'approved');
```

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    type ENUM('product_approved', 'product_rejected') NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_cart (buyer_id, product_id)
);
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE notifications MODIFY COLUMN type ENUM('product_approved', 'product_rejected', 'order_placed') NOT NULL;

CREATE TABLE seller_payout_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,          -- Amount requested for payout
    method ENUM('bkash', 'rocket', 'bank') DEFAULT 'bkash',
    phone_number VARCHAR(20) NOT NULL,      -- Phone number for mobile payment
    status ENUM('pending', 'completed', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (seller_id) REFERENCES users(id)
);
