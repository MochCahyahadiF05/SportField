<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

class Auth {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Register user baru
    public function register($name, $email, $password, $phone = '', $role = 'user') {
        try {
            // Validasi input
            if (empty($name) || empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Semua field harus diisi'];
            }

            // Validasi email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Format email tidak valid'];
            }

            // Check email sudah terdaftar
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email sudah terdaftar'];
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert ke database
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (name, email, password, phone, role, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
            );
            $result = $stmt->execute([$name, $email, $hashed_password, $phone, $role]);

            if ($result) {
                return ['success' => true, 'message' => 'Registrasi berhasil. Silakan login'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Login user
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, password, phone, role, created_at, updated_at FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Email atau password salah'];
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email atau password salah'];
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['created_at'] = $user['created_at'];
            $_SESSION['updated_at'] = $user['updated_at'];
            $_SESSION['logged_in'] = true;

            return ['success' => true, 'message' => 'Login berhasil'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Check user sudah login
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Check if user is admin
    public static function isAdmin() {
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    // Get current user
    public static function getUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['name'],
                'email' => $_SESSION['email'],
                'phone' => $_SESSION['phone'] ?? '',
                'role' => $_SESSION['role'],
                'created_at' => $_SESSION['created_at'] ?? '',
                'updated_at' => $_SESSION['updated_at'] ?? ''
            ];
        }
        return null;
    }

    // Logout
    public static function logout() {
        session_destroy();
        return true;
    }
}

// Create instance global
$auth = new Auth($pdo);
?>
