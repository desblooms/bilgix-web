<?php
// Adjust path for includes
$basePath = '../../';
include $basePath . 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "No sale specified for deletion!";
    $_SESSION['message_type'] = "error";
    redirect($basePath . 'modules/sales/list.php');
}

$saleId = (int)$_GET['id'];

// Get sale details
$sale = $db->select("SELECT * FROM sales WHERE id = :id", ['id' => $saleId]);

if (empty($sale)) {
    $_SESSION['message'] = "Sale not found!";
    $_SESSION['message_type'] = "error";
    redirect($basePath . 'modules/sales/list.php');
}

// Get sale items
$saleItems = $db->select("SELECT * FROM sale_items WHERE saleId = :saleId", ['saleId' => $saleId]);

// Start a transaction
$db->getConnection()->beginTransaction();

try {
    // Return items to inventory
    foreach ($saleItems as $item) {
        // Get current product details
        $product = getProduct($item['productId']);
        
        if ($product) {
            // Calculate new quantity
            $newQty = $product['qty'] + $item['quantity'];
            
            // Update product quantity
            $db->update('products', 
                       ['qty' => $newQty, 'updatedAt' => date('Y-m-d H:i:s')], 
                       'id = :id', 
                       ['id' => $product['id']]);
            
            // Log inventory change
            $logData = [
                'productId' => $product['id'],
                'adjustmentType' => 'add',
                'quantity' => $item['quantity'],
                'previousQty' => $product['qty'],
                'newQty' => $newQty,
                'reason' => 'Sale Cancelled',
                'userId' => $_SESSION['user_id'] ?? 1,
                'createdAt' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('inventory_log', $logData);
        }
    }
    
    // Delete sale items first (due to foreign key constraint)
    $db->delete('sale_items', 'saleId = :saleId', ['saleId' => $saleId]);
    
    // Delete the sale
    $db->delete('sales', 'id = :id', ['id' => $saleId]);
    
    // Commit transaction
    $db->getConnection()->commit();
    
    $_SESSION['message'] = "Sale deleted successfully and inventory restored!";
    $_SESSION['message_type'] = "success";
} catch (Exception $e) {
    // Rollback transaction on error
    $db->getConnection()->rollBack();
    
    $_SESSION['message'] = "Failed to delete sale: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}

redirect($basePath . 'modules/sales/list.php');

// Include footer (this won't be reached due to the redirect, but included for consistency)
include $basePath . 'includes/footer.php';
?>