<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';

// Check authentication
if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

global $pdo;
require_once '../../config/db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Upload gambar
function uploadGambar($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed_types = ['image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Tipe file tidak diperbolehkan. Hanya JPG dan PNG.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }

    $upload_dir = __DIR__ . '/../../assets/img/lapangan/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Gagal mengupload file');
    }

    return 'assets/img/lapangan/' . $filename;
}

// GET - Fetch all lapangan
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
    try {
        $sql = "SELECT * FROM lapangan ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $lapangan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get fasilitas for each lapangan
        foreach ($lapangan_list as &$lapangan) {
            $fasilitas_sql = "SELECT f.id, f.nama FROM fasilitas f
                            JOIN lapangan_fasilitas lf ON f.id = lf.fasilitas_id
                            WHERE lf.lapangan_id = ?";
            $fasilitas_stmt = $pdo->prepare($fasilitas_sql);
            $fasilitas_stmt->execute([$lapangan['id']]);
            $lapangan['fasilitas'] = $fasilitas_stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $lapangan_list]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// POST - Create lapangan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    try {
        $nama = $_POST['nama'] ?? null;
        $jenis = $_POST['jenis'] ?? null;
        $harga_per_jam = $_POST['harga_per_jam'] ?? null;
        $deskripsi = $_POST['deskripsi'] ?? null;
        $status = $_POST['status'] ?? 'tersedia';
        $fasilitas = $_POST['fasilitas'] ?? [];

        if (!$nama || !$jenis || !$harga_per_jam) {
            throw new Exception('Data tidak lengkap');
        }

        // Handle gambar
        $gambar = null;
        if (isset($_FILES['gambar'])) {
            $gambar = uploadGambar($_FILES['gambar']);
        }

        $sql = "INSERT INTO lapangan (nama, jenis, harga_per_jam, deskripsi, gambar, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $jenis, $harga_per_jam, $deskripsi, $gambar, $status]);
        
        $lapangan_id = $pdo->lastInsertId();

        // Insert fasilitas
        if (!empty($fasilitas)) {
            $fasilitas_sql = "INSERT INTO lapangan_fasilitas (lapangan_id, fasilitas_id) VALUES (?, ?)";
            $fasilitas_stmt = $pdo->prepare($fasilitas_sql);
            foreach ($fasilitas as $fasilitas_id) {
                $fasilitas_stmt->execute([$lapangan_id, $fasilitas_id]);
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Lapangan berhasil ditambahkan', 'id' => $lapangan_id]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// POST - Update lapangan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    try {
        $id = $_POST['id'] ?? null;
        $nama = $_POST['nama'] ?? null;
        $jenis = $_POST['jenis'] ?? null;
        $harga_per_jam = $_POST['harga_per_jam'] ?? null;
        $deskripsi = $_POST['deskripsi'] ?? null;
        $status = $_POST['status'] ?? null;
        $fasilitas = $_POST['fasilitas'] ?? [];

        error_log("UPDATE request: id=$id, nama=$nama");

        if (!$id || !$nama || !$jenis || !$harga_per_jam) {
            throw new Exception('Data tidak lengkap');
        }

        // Get current gambar
        $current_sql = "SELECT gambar FROM lapangan WHERE id = ?";
        $current_stmt = $pdo->prepare($current_sql);
        $current_stmt->execute([$id]);
        $current = $current_stmt->fetch(PDO::FETCH_ASSOC);
        $gambar = $current['gambar'];

        // Handle gambar update
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            // Delete old gambar
            if ($current['gambar']) {
                $old_path = __DIR__ . '/../../' . $current['gambar'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            $gambar = uploadGambar($_FILES['gambar']);
        }

        $sql = "UPDATE lapangan SET nama=?, jenis=?, harga_per_jam=?, deskripsi=?, gambar=?, status=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $jenis, $harga_per_jam, $deskripsi, $gambar, $status, $id]);
        
        // Check if lapangan exists (rowCount could be 0 if no changes made)
        $check_sql = "SELECT id FROM lapangan WHERE id = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$id]);
        if ($check_stmt->rowCount() === 0) {
            throw new Exception('Lapangan tidak ditemukan');
        }

        // Update fasilitas
        $delete_sql = "DELETE FROM lapangan_fasilitas WHERE lapangan_id=?";
        $delete_stmt = $pdo->prepare($delete_sql);
        $delete_stmt->execute([$id]);

        if (!empty($fasilitas)) {
            $fasilitas_sql = "INSERT INTO lapangan_fasilitas (lapangan_id, fasilitas_id) VALUES (?, ?)";
            $fasilitas_stmt = $pdo->prepare($fasilitas_sql);
            foreach ($fasilitas as $fasilitas_id) {
                $fasilitas_stmt->execute([$id, $fasilitas_id]);
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Lapangan berhasil diperbarui']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// POST - Delete lapangan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    try {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            throw new Exception('ID tidak valid');
        }

        // Get gambar
        $sql = "SELECT gambar FROM lapangan WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $lapangan = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete gambar file
        if ($lapangan['gambar']) {
            $path = __DIR__ . '/../../' . $lapangan['gambar'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Delete lapangan
        $delete_sql = "DELETE FROM lapangan WHERE id=?";
        $delete_stmt = $pdo->prepare($delete_sql);
        $delete_stmt->execute([$id]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Lapangan berhasil dihapus']);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Default response
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
