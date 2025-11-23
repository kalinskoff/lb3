<?php
require 'vendor/autoload.php';
require 'auth.php';

session_start();
$pdo = new PDO("mysql:host=localhost;dbname=php_KALINSKOV;charset=utf8", "root", "");
$auth = new Auth($pdo);

// Автоматическая авторизация из cookies
if (!$auth->isLoggedIn()) {
    $auth->autoLogin();
}

// Если не авторизован - редирект на страницу входа
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Обработка выхода
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Обработка обновления настроек
if ($_POST['action'] == 'update_settings') {
    $bgColor = $_POST['bg_color'] ?? '#ffffff';
    $textColor = $_POST['text_color'] ?? '#000000';
    $auth->updateSettings($bgColor, $textColor);
}

// Применяем настройки из сессии
$bgColor = $_SESSION['bg_color'] ?? '#ffffff';
$textColor = $_SESSION['text_color'] ?? '#000000';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Главная страница</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            background-color: <?= $bgColor ?>;
            color: <?= $textColor ?>;
        }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ccc; }
        .user-panel { background: #f5f5f5; padding: 15px; margin-bottom: 20px; }
        .settings-form { background: #f9f9f9; padding: 15px; }
    </style>
</head>
<body>
    <div class="user-panel">
        <h2>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <p><a href="?logout=1">Выйти</a></p>
    </div>
    
    <h1>Лабораторная работа по PHP</h1>
    
    <div class="settings-form">
        <h3>Настройки внешнего вида</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_settings">
            <p>Цвет фона: <input type="color" name="bg_color" value="<?= $bgColor ?>"></p>
            <p>Цвет текста: <input type="color" name="text_color" value="<?= $textColor ?>"></p>
            <p><button type="submit">Применить настройки</button></p>
        </form>
    </div>
    
    <!-- Остальной ваш существующий код -->
    <div class="section">
        <h2>Проект с автозагрузкой Composer</h2>
        <p>Структура проекта настроена с использованием Composer autoload.</p>
    </div>
    
    <!-- Ваши существующие разделы -->
    <div class="section">
        <h2>Работа с базой данных</h2>
        <p><a href="get.php">Просмотр данных</a></p>
        <p><a href="add.php">Добавление записей</a></p>
        <p><a href="delete.php">Удаление записей</a></p>
    </div>
</body>
</html>