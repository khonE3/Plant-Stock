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
        'title' => 'ระบบสต็อกสินค้าต้นไม้',
        'add_product' => 'เพิ่มสินค้า',
        'report' => 'สร้างรายงาน PDF',
        'logout' => 'ออกจากระบบ',
        'search' => 'ค้นหาสินค้า...',
        'search_btn' => 'ค้นหา',
        'product_id' => 'ไอดีสินค้า',
        'image' => 'รูปภาพ', // เพิ่ม
        'name_th' => 'ชื่อ (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'จำนวน',
        'price_unit' => 'ราคา (บาท)',
        'actions' => 'การจัดการ',
        'edit' => 'แก้ไข',
        'delete' => 'ลบ',
        'confirm_delete' => 'แน่ใจหรือไม่?'
    ],
    'en' => [
        'title' => 'Plant Stock Management System',
        'add_product' => 'Add Product',
        'report' => 'Generate PDF Report',
        'logout' => 'Logout',
        'search' => 'Search products...',
        'search_btn' => 'Search',
        'product_id' => 'Product ID',
        'image' => 'Image', // เพิ่ม
        'name_th' => 'Name (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'Quantity',
        'price_unit' => 'Price (THB)',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure?'
    ]
];
$current_lang = $_SESSION['lang'];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt = $conn->prepare("SELECT * FROM products WHERE name_th LIKE :search OR name_en LIKE :search");
$stmt->execute(['search' => "%$search%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang[$current_lang]['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-b from-blue-100 to-blue-300 min-h-screen p-6">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-800"><?php echo $lang[$current_lang]['title']; ?></h1>
            <div class="flex space-x-2">
                <a href="?lang=th" class="text-blue-600 hover:text-blue-800 font-semibold px-3 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition duration-300">TH</a>
                <a href="?lang=en" class="text-blue-600 hover:text-blue-800 font-semibold px-3 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition duration-300">EN</a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-3">
                <a href="add_product.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                    <?php echo $lang[$current_lang]['add_product']; ?>
                </a>
                <a href="generate_report.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-300 shadow-md">
                    <?php echo $lang[$current_lang]['report']; ?>
                </a>
            </div>
            <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 shadow-md">
                <?php echo $lang[$current_lang]['logout']; ?>
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" class="mb-6 flex items-center space-x-3">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                   class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm bg-blue-50 text-gray-700 placeholder-gray-500" 
                   placeholder="<?php echo $lang[$current_lang]['search']; ?>">
            <button type="submit" class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                <?php echo $lang[$current_lang]['search_btn']; ?>
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-blue-500 text-white">
                        <th class="p-3 text-left"><?php echo $lang[$current_lang]['product_id']; ?></th>
                        <th class="p-3 text-left"><?php echo $lang[$current_lang]['image']; ?></th>
                        <th class="p-3 text-left"><?php echo $lang[$current_lang]['name_th']; ?></th>
                        <th class="p-3 text-left"><?php echo $lang[$current_lang]['name_en']; ?></th>
                        <th class="p-3 text-center"><?php echo $lang[$current_lang]['quantity']; ?></th>
                        <th class="p-3 text-right"><?php echo $lang[$current_lang]['price_unit']; ?></th>
                        <th class="p-3 text-center"><?php echo $lang[$current_lang]['actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr class="border-b hover:bg-blue-50 transition duration-200">
                        <td class="p-3 text-gray-800"><?php echo $product['product_id']; ?></td>
                        <td class="p-3">
                            <?php if ($product['image_path']): ?>
                                <img src="<?php echo $product['image_path']; ?>" alt="Product Image" class="w-12 h-12 object-cover rounded">
                            <?php else: ?>
                                <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-gray-800"><?php echo $product['name_th']; ?></td>
                        <td class="p-3 text-gray-800"><?php echo $product['name_en']; ?></td>
                        <td class="p-3 text-center text-gray-800"><?php echo $product['quantity']; ?></td>
                        <td class="p-3 text-right text-gray-800"><?php echo number_format($product['price_unit'], 2); ?></td>
                        <td class="p-3 text-center">
                            <a href="edit_product.php?product_id=<?php echo $product['product_id']; ?>" 
                               class="text-blue-600 hover:text-blue-800 font-semibold mr-3">
                                <?php echo $lang[$current_lang]['edit']; ?>
                            </a>
                            <a href="delete_product.php?product_id=<?php echo $product['product_id']; ?>" 
                               class="text-red-600 hover:text-red-800 font-semibold" 
                               onclick="return confirm('<?php echo $lang[$current_lang]['confirm_delete']; ?>')">
                                <?php echo $lang[$current_lang]['delete']; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>