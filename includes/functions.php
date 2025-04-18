<?php
require_once 'db.php';

// Format currency
function formatCurrency($amount) {
    return CURRENCY . number_format($amount, 2);
}

// Sanitize input
function sanitize($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Modified redirect function to handle headers already sent
function redirect($url) {
    // Check if headers already sent
    if (headers_sent()) {
        echo '<script>window.location.href="' . $url . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $url . '"></noscript>';
        exit;
    } else {
        header("Location: $url");
        exit;
    }
}

// Get all products
function getProducts() {
    global $db;
    return $db->select("SELECT * FROM products ORDER BY itemName ASC");
}

// Get product by ID
function getProduct($id) {
    global $db;
    $result = $db->select("SELECT * FROM products WHERE id = :id", ['id' => $id]);
    return isset($result[0]) ? $result[0] : null;
}

// Calculate total product cost
function calculateProductCost($priceUnit, $quantity, $shippingCost = 0, $gst = 0) {
    $totalPrice = $priceUnit * $quantity;
    $totalGst = ($totalPrice * $gst) / 100;
    return $totalPrice + $shippingCost + $totalGst;
}

// Get sales statistics
function getSalesStats() {
    global $db;
    $todaySales = $db->select("SELECT SUM(totalPrice) as total FROM sales WHERE DATE(createdAt) = CURDATE()");
    $monthSales = $db->select("SELECT SUM(totalPrice) as total FROM sales WHERE MONTH(createdAt) = MONTH(CURDATE()) AND YEAR(createdAt) = YEAR(CURDATE())");
    
    return [
        'today' => $todaySales[0]['total'] ?? 0,
        'month' => $monthSales[0]['total'] ?? 0
    ];
}

// Get low stock products
function getLowStockProducts($threshold = 10) {
    global $db;
    return $db->select("SELECT * FROM products WHERE qty <= :threshold", ['threshold' => $threshold]);
}

// Get payment status class for styling
function getPaymentStatusClass($status) {
    switch ($status) {
        case 'Paid':
            return 'bg-green-100 text-green-800';
        case 'Partial':
            return 'bg-yellow-100 text-yellow-800';
        case 'Unpaid':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
?>