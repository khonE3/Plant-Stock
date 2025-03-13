<?php
session_start();
require_once 'db_connect.php';
require_once 'fpdf/fpdf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'th';
}

$lang = [
    'th' => [
        'title' => 'รายงานสต็อกสินค้าต้นไม้ (TH/EN)',
        'name_th' => 'ชื่อ (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'จำนวน'
    ],
    'en' => [
        'title' => 'Plant Stock Report (TH/EN)',
        'name_th' => 'Name (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'Quantity'
    ]
];
$current_lang = $_SESSION['lang'];

$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// สร้าง PDF
$pdf = new FPDF();
$pdf->AddPage();

// เพิ่มฟอนต์ THSarabunNew
$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->SetFont('THSarabunNew', '', 16);

// หัวข้อ
$pdf->Cell(0, 10, iconv('UTF-8', 'TIS-620', $lang[$current_lang]['title']), 0, 1, 'C');
$pdf->Ln(10);

// หัวตาราง
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->Cell(60, 10, iconv('UTF-8', 'TIS-620', $lang[$current_lang]['name_th']), 1);
$pdf->Cell(60, 10, $lang[$current_lang]['name_en'], 1); // ชื่อภาษาอังกฤษไม่ต้องแปลง
$pdf->Cell(30, 10, iconv('UTF-8', 'TIS-620', $lang[$current_lang]['quantity']), 1);
$pdf->Ln();

// ข้อมูลสินค้า
foreach ($products as $product) {
    $pdf->Cell(60, 10, iconv('UTF-8', 'TIS-620', $product['name_th']), 1); // แปลงชื่อภาษาไทย
    $pdf->Cell(60, 10, $product['name_en'], 1); // ชื่อภาษาอังกฤษไม่ต้องแปลง
    $pdf->Cell(30, 10, $product['quantity'], 1);
    $pdf->Ln();
}

// ส่งออก PDF
$pdf->Output('D', 'stock_report.pdf');
exit();
?>