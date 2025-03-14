<?php
$servername = "IPserver"; // IP
$username = "username"; // User
$password = "password"; // Password
$dbname = "db_name"; // Your database

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'"); 
    $conn->exec("SET CHARACTER SET utf8mb4"); 
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
