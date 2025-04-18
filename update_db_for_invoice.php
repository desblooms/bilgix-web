<?php
/**
 * Database update script to add invoicePath column to sales table
 * Run this once to update your database
 */

// Adjust path for includes
$basePath = './';
require_once $basePath . 'includes/config.php';
require_once $basePath . 'includes/db.php';

// Initialize database
$db = new Database();

// Check if the invoicePath column already exists in the sales table
$checkColumn = $db->query("SHOW COLUMNS FROM sales LIKE 'invoicePath'");
$columnExists = $checkColumn->rowCount() > 0;

if (!$columnExists) {
    // Add the invoicePath column to the sales table
    $alterTableQuery = "ALTER TABLE sales ADD COLUMN invoicePath VARCHAR(255) AFTER paymentStatus";
    $db->query($alterTableQuery);
    
    echo "✅ SUCCESS: invoicePath column has been added to the sales table.\n";
} else {
    echo "ℹ️ INFO: The invoicePath column already exists in the sales table.\n";
}

// Create the invoices directory if it doesn't exist
$invoicesDir = $basePath . 'invoices';
if (!file_exists($invoicesDir)) {
    if (mkdir($invoicesDir, 0777, true)) {
        echo "✅ SUCCESS: Created the invoices directory at: $invoicesDir\n";
    } else {
        echo "❌ ERROR: Failed to create the invoices directory at: $invoicesDir\n";
        echo "Please create it manually and ensure it has write permissions.\n";
    }
} else {
    echo "ℹ️ INFO: The invoices directory already exists.\n";
}

// Create .htaccess file to protect invoices directory but allow PDF downloads
$htaccessFile = $invoicesDir . '/.htaccess';
if (!file_exists($htaccessFile)) {
    $htaccessContent = <<<EOT
# Protect the invoices directory
Options -Indexes

# Allow direct access only to PDF files
<FilesMatch "\.pdf$">
    Order allow,deny
    Allow from all
</FilesMatch>

# Deny access to all other files
<FilesMatch "^(?!.*\.pdf$).*$">
    Order deny,allow
    Deny from all
</FilesMatch>
EOT;

    if (file_put_contents($htaccessFile, $htaccessContent)) {
        echo "✅ SUCCESS: Created the .htaccess file to protect the invoices directory.\n";
    } else {
        echo "❌ ERROR: Failed to create the .htaccess file in the invoices directory.\n";
    }
} else {
    echo "ℹ️ INFO: The .htaccess file already exists in the invoices directory.\n";
}

echo "\nDatabase and file system setup for invoice generation is complete!\n";
?>