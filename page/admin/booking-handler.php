<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';

// Check authentication
if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

global $pdo;
require_once '../../config/db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// GET - Fetch booking detail
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'detail') {
    header('Content-Type: application/json');
    try {
        $booking_id = $_GET['booking_id'] ?? null;

        if (!$booking_id) {
            throw new Exception('Booking ID tidak ditemukan');
        }

        // Fetch booking detail dengan semua informasi terkait
        $query = "SELECT b.*, 
                         u.name as customer_name, 
                         u.email as customer_email,
                         u.phone as customer_phone,
                         l.nama as lapangan_nama,
                         l.harga_per_jam,
                         j.nama as jenis_nama,
                         COALESCE(p.status, 'pending') as pembayaran_status,
                         p.metode as pembayaran_metode,
                         p.created_at as pembayaran_created_at
                  FROM booking b
                  INNER JOIN users u ON b.user_id = u.id
                  INNER JOIN lapangan l ON b.lapangan_id = l.id
                  LEFT JOIN jenis_olahraga j ON l.jenis = j.id
                  LEFT JOIN pembayaran p ON b.id = p.booking_id
                  WHERE b.id = ?";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        // Fetch fasilitas yang digunakan
        $fasilitas_query = "SELECT f.nama 
                           FROM lapangan_fasilitas lf
                           INNER JOIN fasilitas f ON lf.fasilitas_id = f.id
                           WHERE lf.lapangan_id = ?";
        $fasilitas_stmt = $pdo->prepare($fasilitas_query);
        $fasilitas_stmt->execute([$booking['lapangan_id']]);
        $fasilitas_list = $fasilitas_stmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch rating jika ada
        $rating_query = "SELECT * FROM ratings WHERE booking_id = ?";
        $rating_stmt = $pdo->prepare($rating_query);
        $rating_stmt->execute([$booking_id]);
        $rating = $rating_stmt->fetch(PDO::FETCH_ASSOC);

        $booking['fasilitas'] = $fasilitas_list;
        $booking['rating'] = $rating;

        echo json_encode([
            'success' => true,
            'data' => $booking
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

// POST - Update booking status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    try {
        $booking_id = $_POST['booking_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$booking_id || !$status) {
            throw new Exception('Data tidak lengkap');
        }

        // Validate status
        $allowed_status = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $allowed_status)) {
            throw new Exception('Status tidak valid');
        }

        // Update booking status
        $sql = "UPDATE booking SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $booking_id]);
        $booking_rows = $stmt->rowCount();

        // Jika booking dibatalkan, handle pembayaran sesuai statusnya
        $refund_rows = 0;
        $payment_status = 'unknown';
        $payment_found = false;
        $payment_action = '';
        
        if ($status === 'cancelled') {
            // Cek semua pembayaran untuk booking ini
            $stmt_check = $pdo->prepare("SELECT id, status FROM pembayaran WHERE booking_id = ?");
            $stmt_check->execute([$booking_id]);
            $pembayaran = $stmt_check->fetch();
            
            if ($pembayaran) {
                $payment_found = true;
                $pembayaran_status = $pembayaran['status'];
                $payment_status = $pembayaran_status;
                
                // Jika pembayaran sudah success/terverifikasi, ubah ke refunded
                if ($pembayaran_status === 'success') {
                    $sql_refund = "UPDATE pembayaran SET status = 'refunded', refund_reason = 'Booking dibatalkan oleh admin' WHERE booking_id = ?";
                    $stmt_refund = $pdo->prepare($sql_refund);
                    $stmt_refund->execute([$booking_id]);
                    $refund_rows = $stmt_refund->rowCount();
                    $payment_action = 'refunded';
                } 
                // Jika pembayaran masih pending, ubah ke cancelled
                else if ($pembayaran_status === 'pending') {
                    $sql_cancel = "UPDATE pembayaran SET status = 'cancelled' WHERE booking_id = ?";
                    $stmt_cancel = $pdo->prepare($sql_cancel);
                    $stmt_cancel->execute([$booking_id]);
                    $refund_rows = $stmt_cancel->rowCount();
                    $payment_action = 'cancelled';
                }
                // Jika pembayaran sudah failed/cancelled/refunded, tidak perlu update
                else {
                    $payment_action = 'no_action_needed';
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Status booking berhasil diupdate',
            'debug' => [
                'booking_id' => $booking_id,
                'new_status' => $status,
                'booking_rows_updated' => $booking_rows,
                'payment_found' => $payment_found,
                'payment_previous_status' => $payment_status,
                'payment_action' => $payment_action,
                'payment_rows_updated' => $refund_rows
            ]
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// GET - Fetch single booking detail
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'detail') {
    try {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            throw new Exception('ID tidak valid');
        }

        $sql = "SELECT b.*, 
                    u.name as customer_name, 
                    u.email as customer_email,
                    u.phone as customer_phone,
                    l.nama as lapangan_nama,
                    l.harga_per_jam
                FROM booking b
                INNER JOIN users u ON b.user_id = u.id
                INNER JOIN lapangan l ON b.lapangan_id = l.id
                WHERE b.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $booking]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Default response
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
