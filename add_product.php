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
        'image' => 'รูปภาพ', // เพิ่ม
        'add' => 'เพิ่มสินค้า',
        'back' => 'กลับไปที่หน้าหลัก'
    ],
    'en' => [
        'title' => 'Add Product',
        'name_th' => 'Name (TH)',
        'name_en' => 'Name (EN)',
        'quantity' => 'Quantity',
        'price_unit' => 'Price (THB)',
        'image' => 'Image', // เพิ่ม
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

    // จัดการอัปโหลดรูปภาพ
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบว่าเป็นไฟล์รูปภาพ
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name_th, name_en, quantity, price_unit, image_path) VALUES (:name_th, :name_en, :quantity, :price_unit, :image_path)");
    $stmt->execute([
        'name_th' => $name_th,
        'name_en' => $name_en,
        'quantity' => $quantity,
        'price_unit' => $price_unit,
        'image_path' => $image_path
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
        <form method="POST" enctype="multipart/form-data">
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
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['image']; ?></label>
                <input type="file" name="image" class="w-full p-2 border rounded" accept="image/*">
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