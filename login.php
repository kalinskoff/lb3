<?php
require 'vendor/autoload.php';
require 'auth.php';

$pdo = new PDO("mysql:host=localhost;dbname=php_KALINSKOV;charset=utf8", "root", "");
$auth = new Auth($pdo);

// Автоматическая авторизация из cookies
if (!$auth->isLoggedIn()) {
    $auth->autoLogin();
}

// Если уже авторизован - редирект на главную
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Обработка формы авторизации
if ($_POST['action'] == 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if ($auth->login($username, $password, $remember)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
}

// Обработка формы регистрации
if ($_POST['action'] == 'register') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $bgColor = $_POST['bg_color'] ?? '#ffffff';
    $textColor = $_POST['text_color'] ?? '#000000';
    
    if ($auth->register($username, $password, $bgColor, $textColor)) {
        $error = 'Регистрация успешна! Теперь войдите в систему.';
    } else {
        $error = 'Ошибка регистрации. Возможно, пользователь уже существует.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Авторизация</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 400px; margin: 0 auto; }
        .form-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Система авторизации</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2>Вход</h2>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <p><input type="text" name="username" placeholder="Имя пользователя" required></p>
                <p><input type="password" name="password" placeholder="Пароль" required></p>
                <p><label><input type="checkbox" name="remember"> Запомнить меня</label></p>
                <p><button type="submit">Войти</button></p>
            </form>
        </div>
        
        <div class="form-section">
            <h2>Регистрация</h2>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <p><input type="text" name="username" placeholder="Имя пользователя" required></p>
                <p><input type="password" name="password" placeholder="Пароль" required></p>
                <p>Цвет фона: <input type="color" name="bg_color" value="#ffffff"></p>
                <p>Цвет текста: <input type="color" name="text_color" value="#000000"></p>
                <p><button type="submit">Зарегистрироваться</button></p>
            </form>
        </div>
    </div>
</body>
</html>