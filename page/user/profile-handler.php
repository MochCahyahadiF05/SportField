<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';
require_once '../../config/db.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

header('Content-Type: application/json');
global $pdo;

$action = $_POST['action'] ?? null;
$userId = Auth::getUser()['id'];

if ($action === 'update_profile') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Validation
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Nama tidak boleh kosong']);
        exit();
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email tidak valid']);
        exit();
    }
    
    // Check if email already exists (exclude current user)
    $emailCheck = $pdo->query("SELECT id FROM users WHERE email = '$email' AND id != $userId")->fetch();
    if ($emailCheck) {
        echo json_encode(['success' => false, 'message' => 'Email sudah digunakan user lain']);
        exit();
    }
    
    // Update profile
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $userId]);
        
        // Update session
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        
        echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil: ' . $e->getMessage()]);
    }
}

elseif ($action === 'change_password') {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'Semua field password harus diisi']);
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Password baru tidak cocok']);
        exit();
    }
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
        exit();
    }
    
    // Get current password from database
    $user = $pdo->query("SELECT password FROM users WHERE id = $userId")->fetch();
    
    // Verify old password
    if (!password_verify($oldPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Password lama tidak sesuai']);
        exit();
    }
    
    // Update password
    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Password berhasil diubah']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengubah password: ' . $e->getMessage()]);
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Action tidak dikenali']);
}
?>