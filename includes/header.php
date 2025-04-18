<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration and functions
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom styles for mobile app feel */
        body {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            overscroll-behavior: none;
        }
        .bottom-nav {
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen pb-16">
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['message'])): ?>
    <div id="flashMessage" class="fixed top-0 left-0 right-0 z-50 p-4 <?= $_SESSION['message_type'] == 'success' ? 'bg-green-500' : 'bg-red-500' ?> text-white text-center">
        <?= $_SESSION['message'] ?>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('flashMessage').style.display = 'none';
        }, 3000);
    </script>
    <?php 
    // Clear the message after displaying
    unset($_SESSION['message']); 
    unset($_SESSION['message_type']);
    endif; 
    ?>

    <!-- Top Navigation -->
    <header class="bg-blue-600 text-white p-4 sticky top-0 z-10 shadow-md">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold"><?= APP_NAME ?></h1>
            <div>
                <button id="menuButton" class="focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Slide-out Menu -->
    <div id="sideMenu" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden">
        <div class="bg-white h-full w-64 shadow-xl transform transition-transform duration-300 -translate-x-full flex flex-col">
            <div class="p-4 bg-blue-600 text-white">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold">Menu</h2>
                    <button id="closeMenu" class="focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto">
                <ul class="py-2">
                    <li><a href="<?= $basePath ?>index.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-home w-6"></i> Dashboard</a></li>
                    <li><a href="<?= $basePath ?>modules/products/list.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-box w-6"></i> Products</a></li>
                    <li><a href="<?= $basePath ?>modules/inventory/list.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-warehouse w-6"></i> Inventory</a></li>
                    <li><a href="<?= $basePath ?>modules/customers/list.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-users w-6"></i> Customers</a></li>
                    <li><a href="<?= $basePath ?>modules/vendors/list.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-truck w-6"></i> Vendors</a></li>
                    <li><a href="<?= $basePath ?>modules/sales/list.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-shopping-cart w-6"></i> Sales</a></li>
                    <li><a href="<?= $basePath ?>modules/purchases/list.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-shopping-basket w-6"></i> Purchases</a></li>
                    <li><a href="<?= $basePath ?>modules/reports/sales.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-chart-bar w-6"></i> Sales Reports</a></li>
                    <li><a href="<?= $basePath ?>modules/reports/inventory.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-chart-line w-6"></i> Inventory Reports</a></li>
                    <li><a href="<?= $basePath ?>modules/reports/expenses.php" class="block px-4 py-3 hover:bg-gray-100"><i class="fas fa-file-invoice-dollar w-6"></i> Expense Reports</a></li>
                </ul>
            </nav>
            <div class="p-4 border-t">
                <a href="<?= $basePath ?>logout.php" class="block px-4 py-2 text-center bg-red-500 text-white rounded">Logout</a>
            </div>
        </div>
    </div>
    
    <!-- Main Content Container -->
    <main class="container mx-auto p-4">

<script>
    // Side menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('menuButton');
        const closeMenu = document.getElementById('closeMenu');
        const sideMenu = document.getElementById('sideMenu');
        const sideMenuPanel = sideMenu.querySelector('div');
        
        menuButton.addEventListener('click', function() {
            sideMenu.classList.remove('hidden');
            setTimeout(() => {
                sideMenuPanel.classList.remove('-translate-x-full');
            }, 10);
        });
        
        function closeMenuHandler() {
            sideMenuPanel.classList.add('-translate-x-full');
            setTimeout(() => {
                sideMenu.classList.add('hidden');
            }, 300);
        }
        
        closeMenu.addEventListener('click', closeMenuHandler);
        
        sideMenu.addEventListener('click', function(e) {
            if (e.target === sideMenu) {
                closeMenuHandler();
            }
        });
    });
</script>