<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/UAS/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/UAS/config/Auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $auth->login($email, $password);
        
        // Tambahkan role ke response jika login berhasil
        if ($result['success']) {
            $user = Auth::getUser();
            $result['role'] = $user['role'];
        }
        
        echo json_encode($result);
    } elseif ($action === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $result = $auth->register($name, $email, $password, $phone);
        echo json_encode($result);
    } elseif ($action === 'logout') {
        Auth::logout();
        echo json_encode(['success' => true, 'message' => 'Logout berhasil']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Action tidak dikenali']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
