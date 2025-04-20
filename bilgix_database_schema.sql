-- Database schema for Billgix Inventory Management System
-- Version 1.1.0 - Enhanced Financial Tracking

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

-- Sales table - enhanced with financial tracking
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoiceNumber VARCHAR(50) NOT NULL UNIQUE,
    customerId INT,
    totalPrice DECIMAL(10, 2) NOT NULL,
    costPrice DECIMAL(10, 2) DEFAULT 0,
    profitAmount DECIMAL(10, 2) DEFAULT 0,
    taxAmount DECIMAL(10, 2) DEFAULT 0,
    discountAmount DECIMAL(10, 2) DEFAULT 0,
    financialAccountId INT,
    paymentMethod VARCHAR(50) DEFAULT 'Cash',
    paymentStatus ENUM('Paid', 'Partial', 'Unpaid') DEFAULT 'Paid',
    paidAmount DECIMAL(10, 2) DEFAULT 0,
    dueAmount DECIMAL(10, 2) DEFAULT 0,
    invoicePath VARCHAR(255),
    notes TEXT,
    createdBy INT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME,
    FOREIGN KEY (customerId) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (financialAccountId) REFERENCES financial_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL
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

-- Expenses table - enhanced
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoryId INT,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expenseDate DATE NOT NULL,
    paymentMethod VARCHAR(50) DEFAULT 'Cash',
    financialAccountId INT,
    reference VARCHAR(100),
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
    attachmentPath VARCHAR(255),
    createdBy INT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME,
    FOREIGN KEY (categoryId) REFERENCES expense_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (financialAccountId) REFERENCES financial_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL
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

-- Settings table for application configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    settingKey VARCHAR(50) NOT NULL UNIQUE,
    settingValue TEXT,
    settingGroup VARCHAR(50) DEFAULT 'general',
    updatedAt DATETIME
);

-- Financial accounts table
CREATE TABLE IF NOT EXISTS financial_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accountName VARCHAR(100) NOT NULL,
    accountType ENUM('cash', 'bank', 'credit', 'online') NOT NULL,
    description TEXT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME
);

-- Opening balance table to track company funds at the start
CREATE TABLE IF NOT EXISTS opening_balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    financialAccountId INT NOT NULL,
    balanceAmount DECIMAL(15, 2) NOT NULL,
    asOfDate DATE NOT NULL,
    notes TEXT,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME,
    FOREIGN KEY (financialAccountId) REFERENCES financial_accounts(id) ON DELETE RESTRICT
);

-- Transactions table for all financial movement
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transactionType ENUM('sale', 'purchase', 'expense', 'deposit', 'withdrawal', 'transfer') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    financialAccountId INT NOT NULL,
    referenceType VARCHAR(50) NOT NULL,
    referenceId INT NOT NULL,
    description TEXT,
    transactionDate DATETIME NOT NULL,
    createdBy INT,
    createdAt DATETIME NOT NULL,
    FOREIGN KEY (financialAccountId) REFERENCES financial_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL
);

-- Profit tracking for each sale item
CREATE TABLE IF NOT EXISTS profit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    saleId INT NOT NULL,
    productId INT NOT NULL,
    saleItemId INT NOT NULL,
    quantitySold DECIMAL(10, 2) NOT NULL,
    costPrice DECIMAL(10, 2) NOT NULL,
    sellingPrice DECIMAL(10, 2) NOT NULL,
    totalCost DECIMAL(10, 2) NOT NULL,
    totalRevenue DECIMAL(10, 2) NOT NULL,
    grossProfit DECIMAL(10, 2) NOT NULL,
    profitMargin DECIMAL(5, 2) NOT NULL,
    saleDate DATETIME NOT NULL,
    createdAt DATETIME NOT NULL,
    FOREIGN KEY (saleId) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (saleItemId) REFERENCES sale_items(id) ON DELETE CASCADE
);

-- Enhanced expense tracking table
CREATE TABLE IF NOT EXISTS expense_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expenseId INT NOT NULL,
    itemDescription VARCHAR(255) NOT NULL,
    quantity DECIMAL(10, 2) DEFAULT 1,
    unitPrice DECIMAL(10, 2) NOT NULL,
    totalAmount DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (expenseId) REFERENCES expenses(id) ON DELETE CASCADE
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

-- Insert default settings
INSERT INTO settings (settingKey, settingValue, settingGroup, updatedAt) VALUES
('company_name', 'Avoak Furnitures', 'company', NOW()),
('company_email', 'avoakfabrics@gmail.com', 'company', NOW()),
('company_phone', '+1234567890', 'company', NOW()),
('company_address', '123 Business Street, City, Country', 'company', NOW()),
('currency', '₹', 'regional', NOW()),
('date_format', 'Y-m-d', 'regional', NOW()),
('low_stock_threshold', '5', 'inventory', NOW()),
('invoice_prefix', 'INV-', 'invoice', NOW()),
('purchase_prefix', 'PO-', 'invoice', NOW()),
('enable_sms', '0', 'notification', NOW()),
('enable_pdf', '1', 'invoice', NOW()),
('financial_year_start', '04-01', 'financial', NOW()),
('tax_rate', '18', 'financial', NOW()),
('enable_profit_tracking', '1', 'financial', NOW());

-- Insert default financial accounts
INSERT INTO financial_accounts (accountName, accountType, description, createdAt) VALUES
('Cash Register', 'cash', 'Primary cash register', NOW()),
('Main Bank Account', 'bank', 'Primary business bank account', NOW()),
('Online Payments', 'online', 'For UPI and other online payments', NOW());

-- Initial sample data for vendors
INSERT INTO vendors (name, contactPerson, phone, email, address, gstNumber, createdAt) VALUES
('ABC Suppliers', 'John Smith', '9876543210', 'john@abc.com', '123 Supplier Street', 'GST123456789', NOW()),
('XYZ Textiles', 'Jane Doe', '8765432109', 'jane@xyz.com', '456 Textile Road', 'GST987654321', NOW());

-- Add indexes to improve query performance
CREATE INDEX idx_products_vendorid ON products(vendorId);
CREATE INDEX idx_products_itemcode ON products(itemCode);
CREATE INDEX idx_sales_customerid ON sales(customerId);
CREATE INDEX idx_sales_createdat ON sales(createdAt);
CREATE INDEX idx_sale_items_productid ON sale_items(productId);
CREATE INDEX idx_purchases_vendorid ON purchases(vendorId);
CREATE INDEX idx_purchases_createdat ON purchases(createdAt);
CREATE INDEX idx_purchase_items_productid ON purchase_items(productId);
CREATE INDEX idx_expenses_categoryid ON expenses(categoryId);
CREATE INDEX idx_expenses_expensedate ON expenses(expenseDate);
CREATE INDEX idx_inventory_log_productid ON inventory_log(productId);
CREATE INDEX idx_inventory_log_createdat ON inventory_log(createdAt);
CREATE INDEX idx_inventory_log_reason ON inventory_log(reason);
CREATE INDEX idx_settings_group ON settings(settingGroup);

-- Indexes for new financial tables
CREATE INDEX idx_opening_balances_account ON opening_balances(financialAccountId);
CREATE INDEX idx_opening_balances_date ON opening_balances(asOfDate);
CREATE INDEX idx_transactions_account ON transactions(financialAccountId);
CREATE INDEX idx_transactions_type ON transactions(transactionType);
CREATE INDEX idx_transactions_reference ON transactions(referenceType, referenceId);
CREATE INDEX idx_transactions_date ON transactions(transactionDate);
CREATE INDEX idx_profit_log_sale ON profit_log(saleId);
CREATE INDEX idx_profit_log_product ON profit_log(productId);
CREATE INDEX idx_profit_log_date ON profit_log(saleDate);
CREATE INDEX idx_expense_details_expense ON expense_details(expenseId);

-- Create views for financial reporting
CREATE OR REPLACE VIEW vw_daily_revenue AS
SELECT 
    DATE(s.createdAt) AS saleDate,
    COUNT(s.id) AS transactionCount,
    SUM(s.totalPrice) AS totalRevenue
FROM 
    sales s
GROUP BY 
    DATE(s.createdAt)
ORDER BY 
    saleDate DESC;

CREATE OR REPLACE VIEW vw_daily_profit AS
SELECT 
    DATE(pl.saleDate) AS saleDate,
    SUM(pl.totalRevenue) AS totalRevenue,
    SUM(pl.totalCost) AS totalCost,
    SUM(pl.grossProfit) AS grossProfit,
    (SUM(pl.grossProfit) / SUM(pl.totalRevenue)) * 100 AS profitMarginPercentage
FROM 
    profit_log pl
GROUP BY 
    DATE(pl.saleDate)
ORDER BY 
    saleDate DESC;

CREATE OR REPLACE VIEW vw_product_profitability AS
SELECT 
    p.id AS productId,
    p.itemName,
    p.itemCode,
    SUM(pl.quantitySold) AS totalQuantitySold,
    SUM(pl.totalRevenue) AS totalRevenue, 
    SUM(pl.totalCost) AS totalCost,
    SUM(pl.grossProfit) AS totalProfit,
    AVG(pl.profitMargin) AS avgProfitMargin
FROM 
    products p
LEFT JOIN 
    profit_log pl ON p.id = pl.productId
GROUP BY 
    p.id, p.itemName, p.itemCode
ORDER BY 
    totalProfit DESC;