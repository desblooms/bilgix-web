<?php
// Define basePath for the root directory
$basePath = './';

// Include header
include $basePath . 'includes/header.php'; 

// Check if finance_handler.php exists before including it
if (file_exists($basePath . 'includes/finance_handler.php')) {
    include_once $basePath . 'includes/finance_handler.php';
    // Get financial summary
    $financialSummary = getFinancialSummary();
} else {
    // Fallback if finance system isn't installed yet
    $financialSummary = [
        'opening_balance' => 0,
        'current_balance' => 0,
        'total_revenue' => 0,
        'total_expenses' => 0,
        'total_profit' => 0,
        'today_sales' => getSalesStats()['today'],
        'today_expenses' => 0,
        'today_profit' => getSalesStats()['today'],
        'month_sales' => getSalesStats()['month'],
        'month_expenses' => 0,
        'month_profit' => getSalesStats()['month'],
        'financial_year_start' => date('Y-m-d'),
        'financial_year_end' => date('Y-m-d', strtotime('+1 year')),
        'last_updated' => date('Y-m-d H:i:s')
    ];
}
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
    
    <!-- Financial Overview Cards -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Current Balance</h3>
            <p class="text-2xl font-bold text-green-600"><?= formatCurrency($financialSummary['current_balance']) ?></p>
            <p class="text-xs text-gray-500 mt-1">Opening: <?= formatCurrency($financialSummary['opening_balance']) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Total Profit</h3>
            <p class="text-2xl font-bold <?= $financialSummary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' ?>"><?= formatCurrency($financialSummary['total_profit']) ?></p>
            <div class="flex text-xs text-gray-500 mt-1 justify-between">
                <span>Revenue: <?= formatCurrency($financialSummary['total_revenue']) ?></span>
                <span>Expenses: <?= formatCurrency($financialSummary['total_expenses']) ?></span>
            </div>
        </div>
    </div>
    
    <!-- Sales Overview Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Today's Sales</h3>
            <p class="text-xl font-bold text-slate-950"><?= formatCurrency($financialSummary['today_sales']) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Today's Expenses</h3>
            <p class="text-xl font-bold text-red-600"><?= formatCurrency($financialSummary['today_expenses']) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm text-gray-600 mb-1">Today's Profit</h3>
            <p class="text-xl font-bold <?= $financialSummary['today_profit'] >= 0 ? 'text-green-600' : 'text-red-600' ?>"><?= formatCurrency($financialSummary['today_profit']) ?></p>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium p-4 border-b">Quick Actions</h3>
        <div class="grid grid-cols-3 gap-2 p-4">
            <a href="modules/sales/add.php" class="flex flex-col items-center justify-center p-3 bg-blue-50 rounded-lg">
                <i class="fas fa-cart-plus text-slate-950 text-xl mb-2"></i>
                <span class="text-xs text-center">New Sale</span>
            </a>
            <a href="modules/products/add.php" class="flex flex-col items-center justify-center p-3 bg-green-50 rounded-lg">
                <i class="fas fa-plus-circle text-green-600 text-xl mb-2"></i>
                <span class="text-xs text-center">Add Product</span>
            </a>
            <a href="modules/reports/sales.php" class="flex flex-col items-center justify-center p-3 bg-purple-50 rounded-lg">
                <i class="fas fa-chart-line text-purple-600 text-xl mb-2"></i>
                <span class="text-xs text-center">Reports</span>
            </a>
        </div>
    </div>
    
    <!-- Low Stock Alert -->
    <?php $lowStockProducts = getLowStockProducts(); ?>
    <?php if (count($lowStockProducts) > 0): ?>
    <div class="bg-white rounded-lg shadow mb-6">
        <h3 class="text-lg font-medium p-4 border-b text-red-600">
            <i class="fas fa-exclamation-triangle mr-2"></i> Low Stock Alert
        </h3>
        <ul class="divide-y">
            <?php foreach($lowStockProducts as $product): ?>
            <li class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-medium"><?= $product['itemName'] ?></p>
                    <p class="text-sm text-gray-600">Code: <?= $product['itemCode'] ?></p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-red-600"><?= $product['qty'] ?> left</p>
                    <a href="modules/products/edit.php?id=<?= $product['id'] ?>" class="text-xs text-slate-950">Update</a>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <!-- Recent Sales -->
    <div class="bg-white rounded-lg shadow">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-medium">Recent Sales</h3>
            <a href="modules/sales/list.php" class="text-sm text-slate-950">View All</a>
        </div>
        <?php
        $recentSales = $db->select("SELECT s.*, c.name as customerName FROM sales s 
                                    LEFT JOIN customers c ON s.customerId = c.id 
                                    ORDER BY s.createdAt DESC LIMIT 5");
        ?>
        <?php if (count($recentSales) > 0): ?>
            <ul class="divide-y">
                <?php foreach($recentSales as $sale): ?>
                <li class="p-4">
                    <div class="flex justify-between items-center mb-1">
                        <p class="font-medium"><?= $sale['customerName'] ?? 'Walk-in Customer' ?></p>
                        <p class="text-green-600 font-bold"><?= formatCurrency($sale['totalPrice']) ?></p>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <p><?= date('M d, Y H:i', strtotime($sale['createdAt'])) ?></p>
                        <p>Invoice #<?= $sale['invoiceNumber'] ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="p-4 text-gray-500 text-center">No recent sales found</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t flex justify-between items-center p-2 bottom-nav">
    <a href="index.php" class="flex flex-col items-center p-2 text-slate-950">
        <i class="fas fa-home text-xl"></i>
        <span class="text-xs mt-1">Home</span>
    </a>
    <a href="modules/products/list.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-box text-xl"></i>
        <span class="text-xs mt-1">Products</span>
    </a>
    <a href="modules/sales/add.php" class="flex flex-col items-center p-2 text-gray-600">
        <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center -mt-6 shadow-lg">
            <i class="fas fa-plus text-xl"></i>
        </div>
        <span class="text-xs mt-1">New Sale</span>
    </a>
    <a href="modules/customers/list.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-users text-xl"></i>
        <span class="text-xs mt-1">Customers</span>
    </a>
    <a href="modules/reports/sales.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-chart-bar text-xl"></i>
        <span class="text-xs mt-1">Reports</span>
    </a>
</nav>

<script>
    // Side menu functionality
    const menuButton = document.getElementById('menuButton');
    const closeMenu = document.getElementById('closeMenu');
    const sideMenu = document.getElementById('sideMenu');
    
    menuButton.addEventListener('click', () => {
        sideMenu.classList.remove('hidden');
        sideMenu.querySelector('div').classList.remove('-translate-x-full');
    });
    
    closeMenu.addEventListener('click', () => {
        sideMenu.querySelector('div').classList.add('-translate-x-full');
        setTimeout(() => {
            sideMenu.classList.add('hidden');
        }, 300);
    });
    
    sideMenu.addEventListener('click', (e) => {
        if (e.target === sideMenu) {
            sideMenu.querySelector('div').classList.add('-translate-x-full');
            setTimeout(() => {
                sideMenu.classList.add('hidden');
            }, 300);
        }
    });
</script>

<?php
// Include footer
include $basePath . 'includes/footer.php';
?>