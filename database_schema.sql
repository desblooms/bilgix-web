-- Database schema for Inventory Manager Mobile App

-- Create database
CREATE DATABASE IF NOT EXISTS u345095192_bilgixavoakdb;
USE u345095192_bilgixavoakdb;




-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'manager', 'staff') NOT NULL DEFAULT 'staff',
    active BOOLEAN NOT NULL DEFAULT TRUE,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME
);

-- Vendors table
CREATE TABLE IF NOT EXISTS vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contactPerson VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    gstNumber VARCHAR(50),
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME
);

-- Products table for inventory items
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendorId INT,
    itemCode VARCHAR(50) NOT NULL UNIQUE,
    itemName VARCHAR(100) NOT NULL,
    hsn VARCHAR(50),
    priceUnit DECIMAL(10, 2) NOT NULL,
    qty DECIMAL(10, 2) NOT NULL DEFAULT 0,
    unitType VARCHAR(20) NOT NULL DEFAULT 'Meter',
    totalPrice DECIMAL(10, 2),
    expense DECIMAL(10, 2) DEFAULT 0,
    shippingCost DECIMAL(10, 2) DEFAULT 0,
    gst DECIMAL(5, 2) DEFAULT 0,
    totalProductCost DECIMAL(10, 2),
    salePrice DECIMAL(10, 2) NOT NULL,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME,
    FOREIGN KEY (vendorId) REFERENCES vendors(id) ON DELETE SET NULL
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME
);

-- Sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoiceNumber VARCHAR(50) NOT NULL UNIQUE,
    customerId INT,
    totalPrice DECIMAL(10, 2) NOT NULL,
    paymentMethod VARCHAR(50) DEFAULT 'Cash',
    paymentStatus ENUM('Paid', 'Partial', 'Unpaid') DEFAULT 'Paid',
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME,
    FOREIGN KEY (customerId) REFERENCES customers(id) ON DELETE SET NULL
);

-- Sale items table
CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    saleId INT NOT NULL,
    productId INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (saleId) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES products(id) ON DELETE RESTRICT
);

-- Purchases table
CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchaseNumber VARCHAR(50) NOT NULL UNIQUE,
    vendorId INT,
    totalAmount DECIMAL(10, 2) NOT NULL,
    paymentStatus ENUM('Paid', 'Partial', 'Unpaid') DEFAULT 'Paid',
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME,
    FOREIGN KEY (vendorId) REFERENCES vendors(id) ON DELETE SET NULL
);

-- Purchase items table
CREATE TABLE IF NOT EXISTS purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchaseId INT NOT NULL,
    productId INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (purchaseId) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES products(id) ON DELETE RESTRICT
);

-- Expense categories table
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoryId INT,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expenseDate DATE NOT NULL,
    paymentMethod VARCHAR(50) DEFAULT 'Cash',
    reference VARCHAR(100),
    createdAt DATETIME NOT NULL,
    FOREIGN KEY (categoryId) REFERENCES expense_categories(id) ON DELETE SET NULL
);

-- Inventory log table to track stock changes
CREATE TABLE IF NOT EXISTS inventory_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    productId INT NOT NULL,
    adjustmentType ENUM('add', 'remove') NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    previousQty DECIMAL(10, 2) NOT NULL,
    newQty DECIMAL(10, 2) NOT NULL,
    reason VARCHAR(100) NOT NULL,
    userId INT,
    createdAt DATETIME NOT NULL,
    FOREIGN KEY (productId) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default expense categories
INSERT INTO expense_categories (name, description) VALUES
('Rent', 'Office or store rent payments'),
('Utilities', 'Water, electricity, internet bills'),
('Salaries', 'Employee salaries and wages'),
('Transportation', 'Delivery and transportation costs'),
('Supplies', 'Office supplies and non-inventory items'),
('Marketing', 'Advertising and promotional expenses'),
('Maintenance', 'Equipment and premises maintenance'),
('Miscellaneous', 'Other uncategorized expenses');

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, name, email, role, active, createdAt) VALUES
('admin', '$2y$10$rBnPNiZCffwOm5l2dAZhTe8QvFojjP4/YpbZH1lz4XSx9ywbZz4m.', 'Admin User', 'admin@example.com', 'admin', TRUE, NOW());

-- Initial sample data
INSERT INTO vendors (name, contactPerson, phone, email, address, gstNumber, createdAt) VALUES
('ABC Suppliers', 'John Smith', '9876543210', 'john@abc.com', '123 Supplier Street', 'GST123456789', NOW()),
('XYZ Textiles', 'Jane Doe', '8765432109', 'jane@xyz.com', '456 Textile Road', 'GST987654321', NOW());

-- Add index to improve query performance
CREATE INDEX idx_products_vendorid ON products(vendorId);
CREATE INDEX idx_products_itemcode ON products(itemCode);
CREATE INDEX idx_sales_customerid ON sales(customerId);
CREATE INDEX idx_sales_createdat ON sales(createdAt);
CREATE INDEX idx_sale_items_productid ON sale_items(productId);
CREATE INDEX idx_inventory_log_productid ON inventory_log(productId);
CREATE INDEX idx_inventory_log_createdat ON inventory_log(createdAt);


