<?php
session_start();
require_once 'db_connect.php';
require_once 'tcpdf/tcpdf.php';

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
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Stock Management System');
$pdf->SetTitle($lang[$current_lang]['title']);
$pdf->SetHeaderData('', 0, $lang[$current_lang]['title'], '');

// ✅ ใช้ฟอนต์ที่รองรับภาษาไทย
$pdf->SetFont('freeserif', '', 12);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// สร้างตาราง HTML
$html = '<h1 style="text-align:center;">' . htmlspecialchars($lang[$current_lang]['title'], ENT_QUOTES, 'UTF-8') . '</h1>';
$html .= '<table border="1" cellpadding="5">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th width="40%">' . htmlspecialchars($lang[$current_lang]['name_th'], ENT_QUOTES, 'UTF-8') . '</th>
            <th width="40%">' . htmlspecialchars($lang[$current_lang]['name_en'], ENT_QUOTES, 'UTF-8') . '</th>
            <th width="20%">' . htmlspecialchars($lang[$current_lang]['quantity'], ENT_QUOTES, 'UTF-8') . '</th>
        </tr>
    </thead>
    <tbody>';

foreach ($products as $product) {
    $html .= '<tr>
        <td>' . htmlspecialchars($product['name_th'], ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars($product['name_en'], ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . intval($product['quantity']) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// ใส่ HTML ลงใน PDF
$pdf->writeHTML($html, true, false, true, false, '');

// ส่งออก PDF
$pdf->Output('stock_report.pdf', 'D');
exit();
?>
