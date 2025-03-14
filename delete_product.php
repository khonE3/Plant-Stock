<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // ดึงข้อมูลรูปภาพก่อนลบ
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE product_id = :product_id");
    $stmt->execute(['product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // ลบสินค้า
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = :product_id");
    $stmt->execute(['product_id' => $product_id]);

    // ลบไฟล์รูปภาพถ้ามี
    if ($product['image_path'] && file_exists($product['image_path'])) {
        unlink($product['image_path']);
    }

    header("Location: dashboard.php");
    exit();
}
?>