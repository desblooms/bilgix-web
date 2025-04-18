<?php
// Adjust path for includes
$basePath = '../../';
include $basePath . 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "No expense specified for deletion!";
    $_SESSION['message_type'] = "error";
    redirect($basePath . 'modules/expenses/list.php');
}

$expenseId = (int)$_GET['id'];

// Check if expense exists
$expense = $db->select("SELECT * FROM expenses WHERE id = :id", ['id' => $expenseId]);

if (empty($expense)) {
    $_SESSION['message'] = "Expense not found!";
    $_SESSION['message_type'] = "error";
    redirect($basePath . 'modules/expenses/list.php');
}

// Delete the expense
$deleted = $db->delete('expenses', 'id = :id', ['id' => $expenseId]);

if ($deleted) {
    $_SESSION['message'] = "Expense deleted successfully!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Failed to delete expense!";
    $_SESSION['message_type'] = "error";
}

redirect($basePath . 'modules/expenses/list.php');

// Include footer (never reached due to redirect, but included for consistency)
include $basePath . 'includes/footer.php';
?>