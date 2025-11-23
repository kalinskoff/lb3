<?php
session_start();

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Регистрация пользователя
    public function register($username, $password, $bgColor = '#ffffff', $textColor = '#000000') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, bg_color, text_color) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$username, $hashedPassword, $bgColor, $textColor]);
    }
    
    // Авторизация
    public function login($username, $password, $remember = false) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['bg_color'] = $user['bg_color'];
            $_SESSION['text_color'] = $user['text_color'];
            
            // Сохраняем в cookies если "запомнить меня"
            if ($remember) {
                setcookie('user_id', $user['id'], time() + (30 * 24 * 60 * 60), '/'); // 30 дней
                setcookie('username', $user['username'], time() + (30 * 24 * 60 * 60), '/');
            }
            
            return true;
        }
        return false;
    }
    
    // Автоматическая авторизация из cookies
    public function autoLogin() {
        if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
            $sql = "SELECT * FROM users WHERE id = ? AND username = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$_COOKIE['user_id'], $_COOKIE['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['bg_color'] = $user['bg_color'];
                $_SESSION['text_color'] = $user['text_color'];
                return true;
            }
        }
        return false;
    }
    
    // Выход
    public function logout() {
        session_destroy();
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('username', '', time() - 3600, '/');
        header('Location: login.php');
        exit;
    }
    
    // Проверка авторизации
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Обновление настроек
    public function updateSettings($bgColor, $textColor) {
        if ($this->isLoggedIn()) {
            $sql = "UPDATE users SET bg_color = ?, text_color = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$bgColor, $textColor, $_SESSION['user_id']]);
            
            if ($result) {
                $_SESSION['bg_color'] = $bgColor;
                $_SESSION['text_color'] = $textColor;
                return true;
            }
        }
        return false;
    }
}
?>