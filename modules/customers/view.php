<?php 
// Adjust path for includes
$basePath = '../../';
include $basePath . 'includes/header.php'; 

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "No customer specified!";
    $_SESSION['message_type'] = "error";
    redirect($basePath . 'modules/customers/list.php');
}

$customerId = (int)$_GET['id'];

// Get customer details
$customer = $db->select("SELECT * FROM customers WHERE id = :id", ['id' => $customerId]);

if (empty($customer)) {
    $_SESSION['message'] = "Customer not found!";
    $_SESSION['message_type'] = "error";
    redirect($basePath . 'modules/customers/list.php');
}

$customer = $customer[0];

// Get customer's purchase history
$purchases = $db->select("SELECT s.*, 
                        (SELECT COUNT(*) FROM sale_items WHERE saleId = s.id) as itemCount 
                        FROM sales s 
                        WHERE s.customerId = :customerId 
                        ORDER BY s.createdAt DESC",
                        ['customerId' => $customerId]);

// Calculate total spent and purchase count
$totalSpent = 0;
$purchaseCount = count($purchases);

foreach ($purchases as $purchase) {
    $totalSpent += $purchase['totalPrice'];
}
?>

<div class="mb-6">
    <div class="flex items-center mb-4">
        <a href="list.php" class="mr-2 text-slate-950">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Customer Details</h2>
    </div>
    
    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-bold text-gray-800"><?= $customer['name'] ?></h3>
                <?php if (!empty($customer['phone'])): ?>
                <p class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i> <?= $customer['phone'] ?></p>
                <?php endif; ?>
                <?php if (!empty($customer['email'])): ?>
                <p class="text-sm text-gray-600"><i class="fas fa-envelope mr-1"></i> <?= $customer['email'] ?></p>
                <?php endif; ?>
                <?php if (!empty($customer['address'])): ?>
                <p class="text-sm text-gray-600 mt-2"><i class="fas fa-map-marker-alt mr-1"></i> <?= $customer['address'] ?></p>
                <?php endif; ?>
            </div>
            <a href="edit.php?id=<?= $customer['id'] ?>" class="bg-blue-600 text-white py-1 px-3 rounded-lg text-sm">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>
    
    <!-- Customer Stats -->
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Total Spent</h3>
            <p class="text-2xl font-bold text-green-600"><?= formatCurrency($totalSpent) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Purchases</h3>
            <p class="text-2xl font-bold text-slate-950"><?= $purchaseCount ?></p>
        </div>
    </div>
    
    <!-- Purchase History -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-md font-medium text-gray-800">Purchase History</h3>
        </div>
        
        <?php if (count($purchases) > 0): ?>
        <ul class="divide-y">
            <?php foreach($purchases as $purchase): ?>
            <li class="p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium"><?= $purchase['invoiceNumber'] ?></p>
                        <p class="text-sm text-gray-600">
                            <?= date('M d, Y', strtotime($purchase['createdAt'])) ?> •
                            <?= $purchase['itemCount'] ?> item<?= $purchase['itemCount'] > 1 ? 's' : '' ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600"><?= formatCurrency($purchase['totalPrice']) ?></p>
                        <a href="../sales/view.php?id=<?= $purchase['id'] ?>" class="text-sm text-slate-950">View Details</a>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <div class="p-4 text-center text-gray-500">
            No purchase history found
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Action Buttons -->
    <div class="mt-4 grid grid-cols-1 gap-4">
        <a href="../sales/add.php?customer=<?= $customer['id'] ?>" class="block bg-blue-600 text-white py-2 px-4 rounded-lg text-center">
            <i class="fas fa-cart-plus mr-2"></i> Create New Sale
        </a>
    </div>
</div>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t flex justify-between items-center p-2 bottom-nav">
    <a href="../../index.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-home text-xl"></i>
        <span class="text-xs mt-1">Home</span>
    </a>
    <a href="../products/list.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-box text-xl"></i>
        <span class="text-xs mt-1">Products</span>
    </a>
    <a href="../sales/add.php" class="flex flex-col items-center p-2 text-gray-600">
        <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center -mt-6 shadow-lg">
            <i class="fas fa-plus text-xl"></i>
        </div>
        <span class="text-xs mt-1">New Sale</span>
    </a>
    <a href="../customers/list.php" class="flex flex-col items-center p-2 text-slate-950">
        <i class="fas fa-users text-xl"></i>
        <span class="text-xs mt-1">Customers</span>
    </a>
    <a href="../reports/sales.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-chart-bar text-xl"></i>
        <span class="text-xs mt-1">Reports</span>
    </a>
</nav>

<?php
// Close the main div and add the footer

// Include footer (which contains ob_end_flush())
include $basePath . 'includes/footer.php';
?>