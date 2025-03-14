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
        'title' => 'Water Stock Report (TH/EN)',
        'name_th' => 'ชื่อ (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'จำนวน',
        'product_id' => 'ไอดีสินค้า',
        'price_unit' => 'ราคา (บาท)'
    ],
    'en' => [
        'title' => 'Water Stock Report (TH/EN)',
        'name_th' => 'Name (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'Quantity',
        'product_id' => 'Product ID',
        'price_unit' => 'Price (THB)'
    ]
];

$current_lang = $_SESSION['lang'];

$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Stock Management System');
$pdf->SetTitle($lang[$current_lang]['title']);
$pdf->SetHeaderData('', 0, $lang[$current_lang]['title'], '');

$pdf->SetFont('freeserif', '', 10);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$html = '<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid black; padding: 5px; text-align: center; }
    th { background-color: #f2f2f2; font-weight: bold; }
</style>';

$html .= '<h1 style="text-align:center;">' . htmlspecialchars($lang[$current_lang]['title'], ENT_QUOTES, 'UTF-8') . '</h1>';
$html .= '<table>
    <thead>
        <tr>
            <th width="20%">' . htmlspecialchars($lang[$current_lang]['product_id'], ENT_QUOTES, 'UTF-8') . '</th>
            <th width="25%">' . htmlspecialchars($lang[$current_lang]['name_th'], ENT_QUOTES, 'UTF-8') . '</th>
            <th width="25%">' . htmlspecialchars($lang[$current_lang]['name_en'], ENT_QUOTES, 'UTF-8') . '</th>
            <th width="15%">' . htmlspecialchars($lang[$current_lang]['quantity'], ENT_QUOTES, 'UTF-8') . '</th>
            <th width="15%">' . htmlspecialchars($lang[$current_lang]['price_unit'], ENT_QUOTES, 'UTF-8') . '</th>
        </tr>
    </thead>
    <tbody>';

foreach ($products as $product) {
    $quantity = intval($product['quantity']);
    $color = ($quantity < 5) ? 'color:red; font-weight:bold;' : '';
    $restock = ($quantity < 5) ? ' <span style="color:red;">(Restock)</span>' : '';

    $html .= '<tr>
        <td width="20%" style="text-align: center;">' . $product['product_id'] . '</td>
        <td width="25%" style="text-align:left;">' . htmlspecialchars($product['name_th'], ENT_QUOTES, 'UTF-8') . '</td>
        <td width="25%" style="text-align:left;">' . htmlspecialchars($product['name_en'], ENT_QUOTES, 'UTF-8') . '</td>
        <td width="15%" style="text-align:center; ' . $color . '">' . $quantity . $restock . '</td>
        <td width="15%" style="text-align: right;">' . number_format($product['price_unit'], 2) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('stock_report.pdf', 'D');
exit();
?>