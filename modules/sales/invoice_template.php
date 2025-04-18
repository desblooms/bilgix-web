<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo $sale['invoiceNumber']; ?></title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .invoice-header h1 {
            font-size: 24px;
            color: #2563EB;
        }
        
        .company-info {
            text-align: right;
        }
        
        /* Invoice Details */
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-details-left, .invoice-details-right {
            flex: 1;
        }
        
        .invoice-details-right {
            text-align: right;
        }
        
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #DCFCE7;
            color: #166534;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        /* Customer Info */
        .customer-section {
            margin-bottom: 30px;
        }
        
        .customer-section h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #4B5563;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background-color: #F3F4F6;
            text-align: left;
            padding: 12px;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .price-column {
            text-align: right;
        }
        
        /* Total */
        .total-section {
            margin-top: 20px;
            border-top: 2px solid #ddd;
            padding-top: 20px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }
        
        .total-label {
            width: 150px;
            text-align: right;
            margin-right: 20px;
        }
        
        .total-value {
            width: 150px;
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #2563EB;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            color: #6B7280;
            font-size: 14px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
            
            @page {
                margin: 0.5cm;
            }
        }
        
        /* Print Button */
        .print-button {
            background-color: #2563EB;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 20px auto;
        }
        
        .print-button:hover {
            background-color: #1D4ED8;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div>
            <h1>INVOICE</h1>
            <p><?php echo $sale['invoiceNumber']; ?></p>
        </div>
        <div class="company-info">
            <h2>Your Company Name</h2>
            <p>123 Business Street</p>
            <p>City, State ZIP</p>
            <p>Phone: (123) 456-7890</p>
        </div>
    </div>
    
    <div class="invoice-details">
        <div class="invoice-details-left">
            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($sale['createdAt'])); ?></p>
            <p><strong>Payment Method:</strong> <?php echo $sale['paymentMethod']; ?></p>
        </div>
        <div class="invoice-details-right">
            <p>
                <span class="status status-<?php echo strtolower($sale['paymentStatus']); ?>">
                    <?php echo $sale['paymentStatus']; ?>
                </span>
            </p>
        </div>
    </div>
    
    <div class="customer-section">
        <h2>Bill To:</h2>
        <?php if (!empty($sale['customerId'])): ?>
            <p><strong><?php echo $sale['customerName']; ?></strong></p>
            <?php if (!empty($sale['customerPhone'])): ?>
                <p>Phone: <?php echo $sale['customerPhone']; ?></p>
            <?php endif; ?>
            <?php if (!empty($sale['customerEmail'])): ?>
                <p>Email: <?php echo $sale['customerEmail']; ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p>Walk-in Customer</p>
        <?php endif; ?>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Code</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th class="price-column">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($saleItems as $item): ?>
            <tr>
                <td><?php echo $item['itemName']; ?></td>
                <td><?php echo $item['itemCode']; ?></td>
                <td><?php echo $item['quantity'] . ' ' . $item['unitType']; ?></td>
                <td><?php echo formatCurrency($item['price']); ?></td>
                <td class="price-column"><?php echo formatCurrency($item['total']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Total:</span>
            <span class="total-value grand-total"><?php echo formatCurrency($sale['totalPrice']); ?></span>
        </div>
    </div>
    
    <div class="invoice-footer">
        <p>Thank you for your business!</p>
    </div>
    
    <button class="print-button" onclick="window.print()">Print Invoice</button>
    
    <script>
        // Auto-print when the page loads if in print mode
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === 'true') {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        };
    </script>
</body>
</html>