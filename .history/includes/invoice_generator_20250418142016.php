<?php
/**
 * Invoice Generator for Sales
 * Uses FPDF library to generate PDF invoices
 */

require_once 'vendor/fpdf/fpdf.php';

class InvoiceGenerator extends FPDF
{
    private $sale;
    private $saleItems;
    private $customer;
    private $company;
    
    public function __construct($sale, $saleItems, $customer, $company)
    {
        parent::__construct();
        $this->sale = $sale;
        $this->saleItems = $saleItems;
        $this->customer = $customer;
        $this->company = $company;
    }
    
    // Page header
    public function Header()
    {
        // Logo (if available)
        // $this->Image('logo.png', 10, 6, 30);
        
        // Company details
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, $this->company['name'], 0, 1, 'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->company['address'], 0, 1, 'R');
        $this->Cell(0, 6, 'Phone: ' . $this->company['phone'], 0, 1, 'R');
        $this->Cell(0, 6, 'Email: ' . $this->company['email'], 0, 1, 'R');
        
        // Invoice title
        $this->SetY(50);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'INVOICE', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Invoice #: ' . $this->sale['invoiceNumber'], 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Date: ' . date('F d, Y', strtotime($this->sale['createdAt'])), 0, 1, 'C');
        
        // Line break
        $this->Ln(10);
    }
    
    // Page footer
    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'C');
    }
    
    // Customer info
    public function addCustomerInfo()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Bill To:', 0, 1);
        
        $this->SetFont('Arial', '', 11);
        if (!empty($this->customer)) {
            $this->Cell(0, 6, $this->customer['name'], 0, 1);
            if (!empty($this->customer['phone'])) {
                $this->Cell(0, 6, 'Phone: ' . $this->customer['phone'], 0, 1);
            }
            if (!empty($this->customer['email'])) {
                $this->Cell(0, 6, 'Email: ' . $this->customer['email'], 0, 1);
            }
            if (!empty($this->customer['address'])) {
                $this->Cell(0, 6, 'Address: ' . $this->customer['address'], 0, 1);
            }
        } else {
            $this->Cell(0, 6, 'Walk-in Customer', 0, 1);
        }
        
        $this->Ln(10);
    }
    
    // Items table
    public function addItemsTable()
    {
        // Table header
        $this->SetFillColor(240, 240, 240);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 8, '#', 1, 0, 'C', true);
        $this->Cell(70, 8, 'Item', 1, 0, 'L', true);
        $this->Cell(25, 8, 'Quantity', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Unit Price', 1, 0, 'R', true);
        $this->Cell(45, 8, 'Total', 1, 1, 'R', true);
        
        // Table items
        $this->SetFont('Arial', '', 10);
        $i = 1;
        $currency = isset($GLOBALS['CURRENCY']) ? $GLOBALS['CURRENCY'] : '₹';
        
        foreach ($this->saleItems as $item) {
            $this->Cell(10, 8, $i, 1, 0, 'C');
            $this->Cell(70, 8, $item['itemName'] . ' (' . $item['itemCode'] . ')', 1, 0, 'L');
            $this->Cell(25, 8, $item['quantity'] . ' ' . $item['unitType'], 1, 0, 'C');
            $this->Cell(35, 8, $currency . number_format($item['price'], 2), 1, 0, 'R');
            $this->Cell(45, 8, $currency . number_format($item['total'], 2), 1, 1, 'R');
            $i++;
        }
        
        // Total row
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(140, 10, 'Total', 0, 0, 'R');
        $this->Cell(45, 10, $currency . number_format($this->sale['totalPrice'], 2), 1, 1, 'R');
        
        // Payment info
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Payment Method: ' . $this->sale['paymentMethod'], 0, 1);
        $this->Cell(0, 6, 'Payment Status: ' . $this->sale['paymentStatus'], 0, 1);
    }
    
    // Notes
    public function addNotes($notes = '')
    {
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, 'Notes:', 0, 1);
        $this->SetFont('Arial', '', 10);
        
        $notes = !empty($notes) ? $notes : 'Thank you for your business!';
        $this->MultiCell(0, 6, $notes, 0, 'L');
    }
    
    // Generate PDF
    public function generatePDF($filepath)
    {
        $this->AliasNbPages();
        $this->AddPage();
        $this->addCustomerInfo();
        $this->addItemsTable();
        $this->addNotes();
        
        // Check if invoices directory exists, if not create it
        $dir = dirname($filepath);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $this->Output('F', $filepath);
        return $filepath;
    }
}

/**
 * Function to generate an invoice for a sale
 * 
 * @param int $saleId The sale ID
 * @param object $db Database connection
 * @return string Path to the generated invoice file
 */
function generateInvoice($saleId, $db) {
    // Get sale details
    $sale = $db->select("SELECT s.*, c.name as customerName, c.phone as customerPhone, c.email as customerEmail, c.address as customerAddress 
                       FROM sales s 
                       LEFT JOIN customers c ON s.customerId = c.id 
                       WHERE s.id = :id", ['id' => $saleId]);
    
    if (empty($sale)) {
        return false;
    }
    
    $sale = $sale[0];
    
    // Get sale items
    $saleItems = $db->select("SELECT si.*, p.itemName, p.itemCode, p.unitType 
                            FROM sale_items si 
                            JOIN products p ON si.productId = p.id 
                            WHERE si.saleId = :saleId", 
                            ['saleId' => $saleId]);
    
    // Customer info
    $customer = null;
    if (!empty($sale['customerId'])) {
        $customer = [
            'name' => $sale['customerName'],
            'phone' => $sale['customerPhone'],
            'email' => $sale['customerEmail'],
            'address' => $sale['customerAddress']
        ];
    }
    
    // Company info
    $company = [
        'name' => COMPANY_NAME,
        'address' => COMPANY_ADDRESS,
        'phone' => COMPANY_PHONE,
        'email' => COMPANY_EMAIL
    ];
    
    // Create invoice file name and path
    $invoiceNumber = $sale['invoiceNumber'];
    $invoiceDate = date('Ymd', strtotime($sale['createdAt']));
    $filename = 'INV_' . $invoiceNumber . '_' . $invoiceDate . '.pdf';
    $filepath = 'invoices/' . $filename;
    
    // Generate PDF
    $invoice = new InvoiceGenerator($sale, $saleItems, $customer, $company);
    $generatedFile = $invoice->generatePDF($filepath);
    
    return $generatedFile;
}