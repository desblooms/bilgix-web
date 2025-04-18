<?php
// print_invoice.php - Clean invoice for printing

// Adjust path for includes
$basePath = '../../';
include $basePath . 'includes/functions.php';
include $basePath . 'includes/db.php';

// Initialize database connection (assuming your DB class exists in the included files)
$db = new DB();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No sale specified!");
}

$saleId = (int)$_GET['id'];

// Get sale details
$sale = $db->select("SELECT s.*, c.name as customerName, c.phone as customerPhone, c.email as customerEmail 
                    FROM sales s 
                    LEFT JOIN customers c ON s.customerId = c.id 
                    WHERE s.id = :id", ['id' => $saleId]);

if (empty($sale)) {
    die("Sale not found!");
}

$sale = $sale[0];

// Get sale items
$saleItems = $db->select("SELECT si.*, p.itemName, p.itemCode, p.unitType 
                         FROM sale_items si 
                         JOIN products p ON si.productId = p.id 
                         WHERE si.saleId = :saleId", 
                         ['saleId' => $saleId]);

// Helper function for payment status class if not defined in functions.php
if (!function_exists('getPaymentStatusClass')) {
    function getPaymentStatusClass($status) {
        switch (strtolower($status)) {
            case 'paid':
                return 'bg-green-100 text-green-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}

// Helper function to format currency if not defined in functions.php
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return '$' . number_format($amount, 2);
    }
}

// Include the clean invoice template
include 'invoice_template.php';
?>