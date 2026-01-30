<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';
require_once '../../config/db.php';

// Check authentication
if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$table = $_POST['table'] ?? '';
$action = $_POST['action'] ?? '';

// Whitelist allowed tables
$allowedTables = ['jenis_olahraga', 'fasilitas'];
if (!in_array($table, $allowedTables)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid table']);
    exit();
}

try {
    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        
        if (empty($nama)) {
            throw new Exception('Nama tidak boleh kosong');
        }
        
        $stmt = $pdo->prepare("INSERT INTO $table (nama) VALUES (?)");
        $stmt->execute([$nama]);
        
        echo json_encode(['success' => true, 'message' => ucfirst($table) . ' berhasil ditambahkan']);
        
    } else if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        
        if (!$id) {
            throw new Exception('ID tidak valid');
        }
        
        if (empty($nama)) {
            throw new Exception('Nama tidak boleh kosong');
        }
        
        // Check if record exists
        $check = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $check->execute([$id]);
        if ($check->rowCount() === 0) {
            throw new Exception(ucfirst($table) . ' tidak ditemukan');
        }
        
        $stmt = $pdo->prepare("UPDATE $table SET nama = ? WHERE id = ?");
        $stmt->execute([$nama, $id]);
        
        echo json_encode(['success' => true, 'message' => ucfirst($table) . ' berhasil diperbarui']);
        
    } else if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        
        if (!$id) {
            throw new Exception('ID tidak valid');
        }
        
        // Check if record exists
        $check = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $check->execute([$id]);
        if ($check->rowCount() === 0) {
            throw new Exception(ucfirst($table) . ' tidak ditemukan');
        }
        
        // Validate deletion for jenis_olahraga - check if any lapangan uses it
        if ($table === 'jenis_olahraga') {
            $usageCheck = $pdo->prepare("SELECT COUNT(*) as count FROM lapangan WHERE jenis = ?");
            $usageCheck->execute([$id]);
            $result = $usageCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                throw new Exception('Tidak dapat menghapus jenis olahraga karena masih digunakan oleh ' . $result['count'] . ' lapangan. Silakan ubah jenis lapangan tersebut terlebih dahulu.');
            }
        }
        
        // Validate deletion for fasilitas - check if any lapangan_fasilitas uses it
        if ($table === 'fasilitas') {
            $usageCheck = $pdo->prepare("SELECT COUNT(*) as count FROM lapangan_fasilitas WHERE fasilitas_id = ?");
            $usageCheck->execute([$id]);
            $result = $usageCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                throw new Exception('Tidak dapat menghapus fasilitas karena masih digunakan oleh ' . $result['count'] . ' lapangan. Silakan hapus asosiasi fasilitas tersebut terlebih dahulu.');
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => ucfirst($table) . ' berhasil dihapus']);
        
    } else {
        throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
