<?php
// generate_pdf_invoice.php - Generate PDF Invoice using TCPDF
// Adjust path for includes
$basePath = '../../';
require_once $basePath . 'includes/functions.php';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'includes/config.php';

// Check if TCPDF library exists, if not, provide instructions to download
if (!file_exists($basePath . 'vendor/tcpdf/tcpdf.php')) {
    die('TCPDF library not found. Please download TCPDF from https://github.com/tecnickcom/TCPDF and extract it to vendor/tcpdf/ folder.');
}

// Include TCPDF library
require_once($basePath . 'vendor/tcpdf/tcpdf.php');

// Initialize database connection
$db = new Database();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No sale specified!");
}

$saleId = (int)$_GET['id'];

// Get sale details
$sale = $db->select("SELECT s.*, c.name as customerName, c.phone as customerPhone, 
                    c.email as customerEmail, c.address as customerAddress
                    FROM sales s 
                    LEFT JOIN customers c ON s.customerId = c.id 
                    WHERE s.id = :id", ['id' => $saleId]);

if (empty($sale)) {
    die("Sale not found!");
}

$sale = $sale[0];

// Get sale items
$saleItems = $db->select("SELECT si.*, p.itemName, p.itemCode, p.unitType, p.hsn 
                         FROM sale_items si 
                         JOIN products p ON si.productId = p.id 
                         WHERE si.saleId = :saleId", 
                         ['saleId' => $saleId]);

// Create custom PDF class
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo
        $image_file = '../../assets/images/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Set font
        $this->SetFont('helvetica', 'B', 16);
        
        // Company info
        $this->SetY(10);
        $this->SetX(50);
        $this->Cell(0, 10, COMPANY_NAME, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        
        $this->SetFont('helvetica', '', 9);
        $this->SetY(15);
        $this->SetX(50);
        $this->Cell(0, 10, COMPANY_ADDRESS, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        
        $this->SetY(20);
        $this->SetX(50);
        $this->Cell(0, 10, 'Phone: ' . COMPANY_PHONE . ' | Email: ' . COMPANY_EMAIL, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        
        // Title
        $this->SetFont('helvetica', 'B', 18);
        $this->SetY(30);
        $this->Cell(0, 10, 'INVOICE', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        // Line break
        $this->Ln(20);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        
        // Footer text
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 10, 'Thank you for your business!', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(APP_NAME);
$pdf->SetAuthor(COMPANY_NAME);
$pdf->SetTitle('Invoice #' . $sale['invoiceNumber']);
$pdf->SetSubject('Invoice #' . $sale['invoiceNumber']);
$pdf->SetKeywords('Invoice, Sale, ' . COMPANY_NAME);

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(15, 50, 15);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 11);

// Invoice info
$pdf->SetY(40);
$pdf->SetX(15);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(90, 10, 'Invoice #: ' . $sale['invoiceNumber'], 0, 0, 'L');
$pdf->Cell(90, 10, 'Date: ' . date('F d, Y', strtotime($sale['createdAt'])), 0, 1, 'R');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 5, 'Payment Method: ' . $sale['paymentMethod'], 0, 0, 'L');
$pdf->Cell(90, 5, 'Payment Status: ' . $sale['paymentStatus'], 0, 1, 'R');

// Customer info
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(180, 8, 'Bill To:', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 10);
if (!empty($sale['customerId'])) {
    $pdf->Cell(180, 5, $sale['customerName'], 0, 1, 'L');
    
    if (!empty($sale['customerPhone'])) {
        $pdf->Cell(180, 5, 'Phone: ' . $sale['customerPhone'], 0, 1, 'L');
    }
    
    if (!empty($sale['customerEmail'])) {
        $pdf->Cell(180, 5, 'Email: ' . $sale['customerEmail'], 0, 1, 'L');
    }
    
    if (!empty($sale['customerAddress'])) {
        $pdf->Cell(180, 5, 'Address: ' . $sale['customerAddress'], 0, 1, 'L');
    }
} else {
    $pdf->Cell(180, 5, 'Walk-in Customer', 0, 1, 'L');
}

// Items table
$pdf->Ln(10);

// Table header
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(10, 8, '#', 1, 0, 'C', 1);
$pdf->Cell(65, 8, 'Item', 1, 0, 'L', 1);
$pdf->Cell(25, 8, 'Code/HSN', 1, 0, 'C', 1);
$pdf->Cell(20, 8, 'Qty', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Unit Price', 1, 0, 'R', 1);
$pdf->Cell(30, 8, 'Total', 1, 1, 'R', 1);

// Table rows
$pdf->SetFont('helvetica', '', 9);
$totalAmount = 0;

foreach ($saleItems as $key => $item) {
    $totalAmount += $item['total'];
    
    $pdf->Cell(10, 7, $key + 1, 1, 0, 'C');
    $pdf->Cell(65, 7, $item['itemName'], 1, 0, 'L');
    $pdf->Cell(25, 7, $item['itemCode'] . (!empty($item['hsn']) ? '/' . $item['hsn'] : ''), 1, 0, 'C');
    $pdf->Cell(20, 7, $item['quantity'] . ' ' . $item['unitType'], 1, 0, 'C');
    $pdf->Cell(30, 7, formatCurrency($item['price']), 1, 0, 'R');
    $pdf->Cell(30, 7, formatCurrency($item['total']), 1, 1, 'R');
}

// Total row
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(150, 8, 'Total:', 1, 0, 'R', 1);
$pdf->Cell(30, 8, formatCurrency($totalAmount), 1, 1, 'R', 1);

// Terms and Conditions
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(180, 8, 'Terms & Conditions:', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(180, 5, "1. Goods once sold will not be taken back or exchanged.\n2. Payment to be made on delivery.\n3. Subject to local jurisdiction.", 0, 'L', 0, 1);

// Signatures
$pdf->Ln(15);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 5, 'For ' . COMPANY_NAME, 0, 0, 'L');
$pdf->Cell(90, 5, 'Customer Signature', 0, 1, 'R');

$pdf->Ln(15);
$pdf->Cell(90, 5, 'Authorized Signatory', 0, 0, 'L');
$pdf->Cell(90, 5, '', 0, 1, 'R');

// ---------------------------------------------------------

// Close and output PDF document
$pdf_filename = 'Invoice_' . $sale['invoiceNumber'] . '.pdf';

// Check if direct output or download
$action = isset($_GET['action']) ? $_GET['action'] : 'download';

if ($action === 'view') {
    // Output PDF to browser for viewing
    $pdf->Output($pdf_filename, 'I');
} else {
    // Default: offer as download
    $pdf->Output($pdf_filename, 'D');
}
?>