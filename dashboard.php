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
        'name_th' => 'ชื่อ (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'จำนวน',
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
        'name_th' => 'Name (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'Quantity',
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
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between mb-4">
            <h1 class="text-3xl font-bold"><?php echo $lang[$current_lang]['title']; ?></h1>
            <div>
                <a href="?lang=th" class="text-blue-500 mr-2">TH</a>
                <a href="?lang=en" class="text-blue-500">EN</a>
            </div>
        </div>
        <div class="flex justify-between mb-4">
            <a href="add_product.php" class="bg-green-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['add_product']; ?>
            </a>
            <a href="generate_report.php" class="bg-purple-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['report']; ?>
            </a>
            <a href="logout.php" class="bg-red-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['logout']; ?>
            </a>
        </div>
        <form method="GET" class="mb-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                   class="p-2 border rounded" placeholder="<?php echo $lang[$current_lang]['search']; ?>">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['search_btn']; ?>
            </button>
        </form>
        <table class="w-full bg-white shadow-md rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2"><?php echo $lang[$current_lang]['name_th']; ?></th>
                    <th class="p-2"><?php echo $lang[$current_lang]['name_en']; ?></th>
                    <th class="p-2"><?php echo $lang[$current_lang]['quantity']; ?></th>
                    <th class="p-2"><?php echo $lang[$current_lang]['actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="p-2"><?php echo $product['name_th']; ?></td>
                    <td class="p-2"><?php echo $product['name_en']; ?></td>
                    <td class="p-2"><?php echo $product['quantity']; ?></td>
                    <td class="p-2">
                        <a href="edit_product.php?product_id=<?php echo $product['product_id']; ?>" class="text-blue-500">
                            <?php echo $lang[$current_lang]['edit']; ?>
                        </a>
                        <a href="delete_product.php?product_id=<?php echo $product['product_id']; ?>" 
                           class="text-red-500 ml-2" onclick="return confirm('<?php echo $lang[$current_lang]['confirm_delete']; ?>')">
                            <?php echo $lang[$current_lang]['delete']; ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>