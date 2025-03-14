<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'th';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] == 'en' ? 'en' : 'th';
}

$lang = [
    'th' => [
        'title' => 'เพิ่มสินค้า',
        'name_th' => 'ชื่อ (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'จำนวน',
        'price_unit' => 'ราคา (บาท)',
        'add' => 'เพิ่มสินค้า',
        'back' => 'กลับไปที่หน้าหลัก'
    ],
    'en' => [
        'title' => 'Add Product',
        'name_th' => 'Name (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'Quantity',
        'price_unit' => 'Price (THB)',
        'add' => 'Add Product',
        'back' => 'Back to Dashboard'
    ]
];
$current_lang = $_SESSION['lang'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name_th = $_POST['name_th'];
    $name_en = $_POST['name_en'];
    $quantity = $_POST['quantity'];
    $price_unit = $_POST['price_unit'];

    $stmt = $conn->prepare("INSERT INTO products (name_th, name_en, quantity, price_unit) VALUES (:name_th, :name_en, :quantity, :price_unit)");
    $stmt->execute([
        'name_th' => $name_th,
        'name_en' => $name_en,
        'quantity' => $quantity,
        'price_unit' => $price_unit
    ]);
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang[$current_lang]['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold"><?php echo $lang[$current_lang]['title']; ?></h1>
            <div>
                <a href="?lang=th" class="text-blue-500 mr-2">TH</a>
                <a href="?lang=en" class="text-blue-500">EN</a>
            </div>
        </div>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['name_th']; ?></label>
                <input type="text" name="name_th" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['name_en']; ?></label>
                <input type="text" name="name_en" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['quantity']; ?></label>
                <input type="number" name="quantity" class="w-full p-2 border rounded" required min="0">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['price_unit']; ?></label>
                <input type="number" name="price_unit" step="0.01" class="w-full p-2 border rounded" required min="0">
            </div>
            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['add']; ?>
            </button>
        </form>
        <a href="dashboard.php" class="block text-center mt-4 text-blue-500">
            <?php echo $lang[$current_lang]['back']; ?>
        </a>
    </div>
</body>
</html>