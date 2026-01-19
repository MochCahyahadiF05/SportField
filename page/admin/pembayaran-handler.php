<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';
require_once '../../config/db.php';

header('Content-Type: application/json');

// Check authentication
if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

global $pdo;

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'approve_payment') {
    $pembayaran_id = (int)($_POST['pembayaran_id'] ?? 0);
    $booking_id = (int)($_POST['booking_id'] ?? 0);
    
    if (!$pembayaran_id || !$booking_id) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }
    
    try {
        // Update pembayaran status to success
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'success' WHERE id = ?");
        $stmt->execute([$pembayaran_id]);
        $pembayaran_updated = $stmt->rowCount();
        
        // Update booking status to confirmed (jika masih pending)
        $stmt = $pdo->prepare("UPDATE booking SET status = 'confirmed' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$booking_id]);
        $booking_updated = $stmt->rowCount();
        
        // Return detailed response
        echo json_encode([
            'success' => true, 
            'message' => 'Pembayaran disetujui',
            'debug' => [
                'pembayaran_updated' => $pembayaran_updated,
                'booking_updated' => $booking_updated
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

if ($action === 'reject_payment') {
    $pembayaran_id = (int)($_POST['pembayaran_id'] ?? 0);
    $booking_id = (int)($_POST['booking_id'] ?? 0);
    
    if (!$pembayaran_id || !$booking_id) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }
    
    try {
        // Update pembayaran status to failed
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'failed' WHERE id = ?");
        $stmt->execute([$pembayaran_id]);
        
        // Update booking status to cancelled
        $stmt = $pdo->prepare("UPDATE booking SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        echo json_encode(['success' => true, 'message' => 'Pembayaran ditolak']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

if ($action === 'request_refund') {
    $pembayaran_id = (int)($_POST['pembayaran_id'] ?? 0);
    $refund_reason = $_POST['refund_reason'] ?? 'Diminta oleh admin';
    
    if (!$pembayaran_id) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }
    
    try {
        // Get booking_id dulu
        $stmt = $pdo->prepare("SELECT booking_id FROM pembayaran WHERE id = ?");
        $stmt->execute([$pembayaran_id]);
        $result = $stmt->fetch();
        $booking_id = $result['booking_id'] ?? null;
        
        if (!$booking_id) {
            echo json_encode(['success' => false, 'message' => 'Booking tidak ditemukan']);
            exit();
        }
        
        // Update pembayaran status to refunded
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'refunded', refund_reason = ? WHERE id = ? AND status = 'success'");
        $stmt->execute([$refund_reason, $pembayaran_id]);
        
        // Update booking status to cancelled (jika masih confirmed atau pending)
        $stmt = $pdo->prepare("UPDATE booking SET status = 'cancelled' WHERE id = ? AND status IN ('pending', 'confirmed')");
        $stmt->execute([$booking_id]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Booking tidak bisa direfund (status tidak valid)']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Refund berhasil diproses']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action tidak ditemukan']);
