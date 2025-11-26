CREATE DATABASE IF NOT EXISTS sistema_inventario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_inventario;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
    active TINYINT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role, active) VALUES
('Admin User', 'admin@demo.com', '$2y$10$wBCahvVByT8pMtbCMYSiXusfm2ScW/qoIN1t/YgaTpapkfc6hW5Be', 'admin', 1),
('Empleado Demo', 'empleado@demo.com', '$2y$10$NC7f/m7onn9yL1XRY2FAie7zSQrqa7ugMUsn/iYVC.QnY516htnRW', 'employee', 1);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL DEFAULT 'General',
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_quantity INT NOT NULL DEFAULT 0,
    min_stock_level INT NOT NULL DEFAULT 0,
    image_url TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, description, category, price, cost, stock_quantity, min_stock_level, image_url) VALUES
('Laptop HP 15', 'Laptop HP 15 con 8GB RAM y 256GB SSD', 'Electrónica', 699.99, 549.99, 15, 5, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1471&q=80'),
('Wireless Mouse', 'Mouse ergonómico inalámbrico', 'Accesorios', 24.99, 12.50, 42, 10, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1167&q=80'),
('Wireless Keyboard', 'Teclado inalámbrico tamaño completo', 'Accesorios', 39.99, 22.50, 3, 5, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1165&q=80'),
('Monitor 24\"', 'Monitor Full HD de 24 pulgadas', 'Electrónica', 149.99, 99.99, 8, 3, 'https://images.unsplash.com/photo-1586776977607-310e9c725c37?auto=format&fit=crop&w=1170&q=80');

CREATE TABLE IF NOT EXISTS movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    type ENUM('in', 'out') NOT NULL,
    quantity INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes VARCHAR(255) DEFAULT '',
    user_id INT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_movements_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_movements_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO movements (product_id, type, quantity, date, notes, user_id, status) VALUES
(1, 'in', 20, '2023-05-15 10:00:00', 'Stock inicial', 1, 'approved'),
(1, 'out', 5, '2023-05-18 12:00:00', 'Venta a cliente', 2, 'approved'),
(2, 'in', 50, '2023-05-10 09:00:00', 'Stock inicial', 1, 'approved'),
(2, 'out', 8, '2023-05-20 14:00:00', 'Venta a cliente', 2, 'approved');
