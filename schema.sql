-- Drop tables if they already exist (for clean setup)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- Table for users (Super Admin and Admin/Cashier)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'superadmin') NOT NULL,
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table for products (Blend S coffee menu)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    added_by INT NOT NULL,  -- Foreign key to users.id
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id)
);

-- Table for main orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cashier_id INT NOT NULL, -- Foreign key to users.id
    total_amount DECIMAL(10, 2) NOT NULL,
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP, -- This is the transaction date
    FOREIGN KEY (cashier_id) REFERENCES users(id)
);

-- Table for items within an order
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,    -- Foreign key to orders.id
    product_id INT NOT NULL,  -- Foreign key to products.id
    quantity INT NOT NULL,
    price_per_item DECIMAL(10, 2) NOT NULL, -- Price at the time of sale
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create default Super Administrators
-- Username: superadmin | Password: superadmin
-- Username: anon       | Password: superadmin
INSERT INTO users (username, password_hash, role, status)
VALUES
('superadmin', '$2y$10$E5EFBy23ym5Tzq61u.ZPE.UPIz5Wh6.V5qi8o.UIkBu39wJD6mQb6', 'superadmin', 'active'),
('anon', '$2y$10$E5EFBy23ym5Tzq61u.ZPE.UPIz5Wh6.V5qi8o.UIkBu39wJD6mQb6', 'superadmin', 'active');

-- Insert sample coffee products (with updated image URLs)
INSERT INTO products (name, price, image_path, added_by) VALUES
('Americano', 69.00, 'https://assets.beanbox.com/blog_images/AB7ud4YSE6nmOX0iGlgA.jpeg', 1),
('Cafe Mocha', 69.00, 'https://images.squarespace-cdn.com/content/v1/5ea3b22556f3d073f3d9cae4/68ebb661-a0e6-47fa-9d00-42c2a438fb8a/Screen+Shot+2022-02-25+at+2.12.39+PM.jpg', 1),
('Caramel Macchiato', 69.00, 'https://www.allrecipes.com/thmb/LgtetzzQWH3GMxFISSii84XEAB8=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/258686-IcedCaramelMacchiato-ddmps-4x3-104704-2effb74f7d504b8aa5fbd52204d0e2e5.jpg', 1),
('Chocolate Almond', 69.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRNwlKgyH8Lf_TGZanT-Z0NKWZIATpK01NjXA&s', 1),
('French Vanilla', 69.00, 'https://deliciousmadeeasy.com/wp-content/uploads/2019/07/french-vanilla-iced-coffee-8-of-20-720x540.jpg', 1),
('Salted Caramel', 69.00, 'https://gigglesgalore.net/wp-content/uploads/2018/12/java-house-hot-salted-caramel-coffee-cup-480x480.jpg', 1),
('Spanish Latte', 69.00, 'https://mywirsh.com/cdn/shop/articles/Spanish_Latte.jpg?v=1714830807', 1),
('Sweet Almond', 69.00, 'https://www.allrecipes.com/thmb/Hqro0FNdnDEwDjrEoxhMfKdWfOY=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/21667-easy-iced-coffee-ddmfs-4x3-0093-7becf3932bd64ed7b594d46c02d0889f.jpg', 1),
('White Mocha', 69.00, 'https://coffeecopycat.com/wp-content/uploads/2023/06/HotWhiteMocha-1200-x-1200.jpg', 1);

