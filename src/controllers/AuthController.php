<?php
require_once '../../../config/config.php';

class AuthController {
    public function register($username, $email, $password) {
        global $pdo;
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO user (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$username, $email, $passwordHash]);
    }

    public function login($email, $password) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }
}
