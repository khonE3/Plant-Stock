<?php
$servername = "158.108.101.153";
$username = "std6630202252";
$password = "M3@zWq7L";
$dbname = "it_std6630202252";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'"); // บังคับ UTF-8
    $conn->exec("SET CHARACTER SET utf8mb4"); // เพิ่มการตั้งค่าเพิ่มเติม
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>