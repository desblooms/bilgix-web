<?php
/**
 * Configuration file for Inventory Management Mobile App
 * 
 * This file contains all the configuration settings for the application
 * including database connection, application settings, and global constants.
 */

// Database configuration
define('DB_HOST', 'localhost');    
define('DB_USER', 'u345095192_avoakbill');          
define('DB_PASS', 'Avoak@123');             
define('DB_NAME', 'u345095192_avoakbill'); 

// Application settings
define('APP_NAME', 'Billgix');
define('APP_VERSION', '1.0.0');
define('CURRENCY', '₹');
define('COMPANY_NAME', 'Avoak Furnitures');
define('COMPANY_EMAIL', 'avoakfabrics@gmail.com');
define('COMPANY_PHONE', '+1234567890');
define('COMPANY_ADDRESS', '123 Business Street, City, Country');

// File paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('MODULES_PATH', ROOT_PATH . 'modules/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// Session settings
define('SESSION_LIFETIME', 86400); // 24 hours in seconds

// Error reporting (set to 0 for production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('UTC');

// Security settings
define('HASH_COST', 10); // For password hashing

// Low stock threshold
define('LOW_STOCK_THRESHOLD', 5);
?>