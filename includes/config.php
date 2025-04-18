<?php
/**
 * Configuration file for Inventory Management Mobile App
 * 
 * This file contains all the configuration settings for the application
 * including database connection, application settings, and global constants.
 */

// Database configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');    
if (!defined('DB_USER')) define('DB_USER', 'u345095192_bilgixavoak');          
if (!defined('DB_PASS')) define('DB_PASS', 'Avoak@123');             
if (!defined('DB_NAME')) define('DB_NAME', 'u345095192_bilgixavoakdb'); 

// Application settings
if (!defined('APP_NAME')) define('APP_NAME', 'Billgix');
if (!defined('APP_VERSION')) define('APP_VERSION', '1.0.0');
if (!defined('CURRENCY')) define('CURRENCY', '₹');
if (!defined('COMPANY_NAME')) define('COMPANY_NAME', 'Avoak Furnitures');
if (!defined('COMPANY_EMAIL')) define('COMPANY_EMAIL', 'avoakfabrics@gmail.com');
if (!defined('COMPANY_PHONE')) define('COMPANY_PHONE', '+1234567890');
if (!defined('COMPANY_ADDRESS')) define('COMPANY_ADDRESS', '123 Business Street, City, Country');

// File paths
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__) . '/');
if (!defined('INCLUDES_PATH')) define('INCLUDES_PATH', ROOT_PATH . 'includes/');
if (!defined('MODULES_PATH')) define('MODULES_PATH', ROOT_PATH . 'modules/');
if (!defined('ASSETS_PATH')) define('ASSETS_PATH', ROOT_PATH . 'assets/');

// Session settings
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 86400); // 24 hours in seconds

// Error reporting (set to 0 for production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('UTC');

// Security settings
if (!defined('HASH_COST')) define('HASH_COST', 10); // For password hashing

// Low stock threshold
if (!defined('LOW_STOCK_THRESHOLD')) define('LOW_STOCK_THRESHOLD', 5);
?>