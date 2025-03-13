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
        'title' => 'เข้าสู่ระบบ',
        'username' => 'ชื่อผู้ใช้',
        'password' => 'รหัสผ่าน',
        'login' => 'เข้าสู่ระบบ',
        'register_link' => 'ยังไม่มีบัญชี? สมัครสมาชิก',
        'error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
    ],
    'en' => [
        'title' => 'Login',
        'username' => 'Username',
        'password' => 'Password',
        'login' => 'Login',
        'register_link' => 'Don’t have an account? Register',
        'error' => 'Invalid username or password'
    ]
];
$current_lang = $_SESSION['lang'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = $lang[$current_lang]['error'];
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
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['username']; ?></label>
                <input type="text" name="username" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700"><?php echo $lang[$current_lang]['password']; ?></label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">
                <?php echo $lang[$current_lang]['login']; ?>
            </button>
        </form>
        <a href="register.php" class="block text-center mt-4 text-blue-500">
            <?php echo $lang[$current_lang]['register_link']; ?>
        </a>
    </div>
</body>
</html>