<?php
// print_invoice.php - Clean invoice for printing

// Adjust path for includes
$basePath = '../../';
include $basePath . 'includes/functions.php';
include $basePath . 'includes/db.php';
include $basePath . 'includes/config.php';

// Initialize database connection
$db = new Database();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No sale specified!");
}

$saleId = (int)$_GET['id'];

// Get sale details
$sale = $db->select("SELECT s.*, c.name as customerName, c.phone as customerPhone, 
                   c.email as customerEmail, c.address as customerAddress
                   FROM sales s 
                   LEFT JOIN customers c ON s.customerId = c.id 
                   WHERE s.id = :id", ['id' => $saleId]);

if (empty($sale)) {
    die("Sale not found!");
}

$sale = $sale[0];

// Get sale items
$saleItems = $db->select("SELECT si.*, p.itemName, p.itemCode, p.unitType, p.hsn 
                         FROM sale_items si 
                         JOIN products p ON si.productId = p.id 
                         WHERE si.saleId = :saleId", 
                         ['saleId' => $saleId]);

// Include the enhanced invoice template
include 'invoice_template.php';
?>