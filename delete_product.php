<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$product_id = $_GET['product_id'];
$stmt = $conn->prepare("DELETE FROM products WHERE product_id = :product_id");
$stmt->execute(['product_id' => $product_id]);
header("Location: dashboard.php");
exit();
?>