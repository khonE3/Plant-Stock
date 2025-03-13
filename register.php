<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
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
        'title' => 'สมัครสมาชิก',
        'username' => 'ชื่อผู้ใช้',
        'password' => 'รหัสผ่าน',
        'confirm_password' => 'ยืนยันรหัสผ่าน',
        'register' => 'สมัครสมาชิก',
        'login_link' => 'มีบัญชีอยู่แล้ว? เข้าสู่ระบบ',
        'error_username_taken' => 'ชื่อผู้ใช้นี้ถูกใช้แล้ว',
        'error_password_mismatch' => 'รหัสผ่านไม่ตรงกัน',
        'success' => 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'
    ],
    'en' => [
        'title' => 'Register',
        'username' => 'Username',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'register' => 'Register',
        'login_link' => 'Already have an account? Login',
        'error_username_taken' => 'This username is already taken',
        'error_password_mismatch' => 'Passwords do not match',
        'success' => 'Registration successful! Please login'
    ]
];
$current_lang = $_SESSION['lang'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ารหัสผ่านตรงกัน
    if ($password !== $confirm_password) {
        $error = $lang[$current_lang]['error_password_mismatch'];
    } else {
        // ตรวจสอบว่าชื่อผู้ใช้ถูกใช้แล้วหรือไม่
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $error = $lang[$current_lang]['error_username_taken'];
        } else {
            // เข้ารหัสรหัสผ่านและบันทึกข้อมูล
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute(['username' => $username, 'password' => $hashed_password]);
            $success = $lang[$current_lang]['success'];
        }
    }
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
            <a href="?lang=th" class="text-blue-500">TH</a>
            <a href="?lang=en" class="text-blue-500">EN</a>
        </div>
        <h1 class="text-2xl font-bold mb-4 text-center"><?php echo $lang[$current_lang]['title']; ?></h1>
        <?php if (isset($error)) echo "<p class='text-red-500'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='text-green-500'>$success</p>"; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['username']; ?></label>
                <input type="text" name="username" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['password']; ?></label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['confirm_password']; ?></label>
                <input type="password" name="confirm_password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['register']; ?>
            </button>
        </form>
        <a href="login.php" class="block text-center mt-4 text-blue-500">
            <?php echo $lang[$current_lang]['login_link']; ?>
        </a>
    </div>
</body>
</html>