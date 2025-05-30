<?php 
// Adjust path for includes
$basePath = '../../';
include $basePath . 'includes/header.php'; 

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $contactPerson = sanitize($_POST['contactPerson']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $gstNumber = sanitize($_POST['gstNumber']);
    
    // Validation
    $errors = [];
    if (empty($name)) $errors[] = "Vendor name is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    
    // If no errors, insert into database
    if (empty($errors)) {
        $data = [
            'name' => $name,
            'contactPerson' => $contactPerson,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'gstNumber' => $gstNumber,
            'createdAt' => date('Y-m-d H:i:s')
        ];
        
        $vendorId = $db->insert('vendors', $data);
        
        if ($vendorId) {
            // Redirect to vendor list with success message
            $_SESSION['message'] = "Vendor added successfully!";
            $_SESSION['message_type'] = "success";
            redirect($basePath . 'modules/vendors/list.php');
        } else {
            $errors[] = "Failed to add vendor. Please try again.";
        }
    }
}
?>

<div class="mb-6">
    <div class="flex items-center mb-4">
        <a href="list.php" class="mr-2 text-slate-950">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Add New Vendor</h2>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
        <ul class="list-disc list-inside">
            <?php foreach($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" class="bg-white rounded-lg shadow p-4">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Vendor Name *</label>
            <input type="text" id="name" name="name" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900" required value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
        </div>
        
        <div class="mb-4">
            <label for="contactPerson" class="block text-gray-700 font-medium mb-2">Contact Person</label>
            <input type="text" id="contactPerson" name="contactPerson" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900" value="<?= isset($_POST['contactPerson']) ? $_POST['contactPerson'] : '' ?>">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="phone" class="block text-gray-700 font-medium mb-2">Phone *</label>
                <input type="tel" id="phone" name="phone" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900" required value="<?= isset($_POST['phone']) ? $_POST['phone'] : '' ?>">
            </div>
            
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
            </div>
        </div>
        
        <div class="mb-4">
            <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
            <textarea id="address" name="address" rows="3" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900"><?= isset($_POST['address']) ? $_POST['address'] : '' ?></textarea>
        </div>
        
        <div class="mb-4">
            <label for="gstNumber" class="block text-gray-700 font-medium mb-2">GST Number</label>
            <input type="text" id="gstNumber" name="gstNumber" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-900" value="<?= isset($_POST['gstNumber']) ? $_POST['gstNumber'] : '' ?>">
        </div>
        
        <div class="mt-6">
            <button type="submit" class="w-full bg-red-900 text-white py-2 px-4 rounded-lg hover:bg-red-900 transition">
                <i class="fas fa-save mr-2"></i> Save Vendor
            </button>
        </div>
    </form>
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
        <div class="bg-red-900 text-white rounded-full w-12 h-12 flex items-center justify-center -mt-6 shadow-lg">
            <i class="fas fa-plus text-xl"></i>
        </div>
        <span class="text-xs mt-1">New Sale</span>
    </a>
    <a href="../customers/list.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-users text-xl"></i>
        <span class="text-xs mt-1">Customers</span>
    </a>
    <a href="../reports/index.php" class="flex flex-col items-center p-2 text-gray-600">
        <i class="fas fa-chart-bar text-xl"></i>
        <span class="text-xs mt-1">Reports</span>
    </a>
</nav>

<?php
// Close the main div and add the footer

// Include footer (which contains ob_end_flush())
include $basePath . 'includes/footer.php';
?>