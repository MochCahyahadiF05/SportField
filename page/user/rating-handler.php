<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/db.php';
header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'submit_rating':
            submitRating();
            break;
        case 'update_rating':
            updateRatingData();
            break;
        case 'delete_rating':
            deleteRating();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenali']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function submitRating() {
    global $pdo, $user_id;
    
    $lapangan_id = (int)$_POST['lapangan_id'] ?? 0;
    $rating = (int)$_POST['rating'] ?? 0;
    $review = $_POST['review'] ?? '';
    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;
    
    // Validasi
    if ($lapangan_id <= 0) {
        throw new Exception('Lapangan tidak valid');
    }
    
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating harus antara 1-5');
    }
    
    // Check if user already rated this field
    $stmt = $pdo->prepare("SELECT id FROM ratings WHERE user_id = ? AND lapangan_id = ?");
    $stmt->execute([$user_id, $lapangan_id]);
    
    if ($stmt->fetch()) {
        // Update existing rating
        $stmt = $pdo->prepare(
            "UPDATE ratings SET rating = ?, review = ?, updated_at = NOW() 
             WHERE user_id = ? AND lapangan_id = ?"
        );
        $stmt->execute([$rating, $review, $user_id, $lapangan_id]);
    } else {
        // Insert new rating
        $stmt = $pdo->prepare(
            "INSERT INTO ratings (user_id, lapangan_id, booking_id, rating, review, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $stmt->execute([$user_id, $lapangan_id, $booking_id, $rating, $review]);
    }
    
    // Update lapangan average rating
    updateLapanganRating($lapangan_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Rating berhasil disimpan'
    ]);
}

function updateRatingData() {
    global $pdo, $user_id;
    
    $rating_id = (int)$_POST['rating_id'] ?? 0;
    $rating = (int)$_POST['rating'] ?? 0;
    $review = $_POST['review'] ?? '';
    
    // Validasi
    if ($rating_id <= 0) {
        throw new Exception('Rating ID tidak valid');
    }
    
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating harus antara 1-5');
    }
    
    // Check if rating belongs to current user
    $stmt = $pdo->prepare("SELECT lapangan_id FROM ratings WHERE id = ? AND user_id = ?");
    $stmt->execute([$rating_id, $user_id]);
    $result = $stmt->fetch();
    
    if (!$result) {
        throw new Exception('Rating tidak ditemukan atau Anda tidak memiliki akses');
    }
    
    $lapangan_id = $result['lapangan_id'];
    
    // Update rating
    $stmt = $pdo->prepare(
        "UPDATE ratings SET rating = ?, review = ?, updated_at = NOW() WHERE id = ? AND user_id = ?"
    );
    $stmt->execute([$rating, $review, $rating_id, $user_id]);
    
    // Update lapangan average rating
    updateLapanganRating($lapangan_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Rating berhasil diperbarui'
    ]);
}

function deleteRating() {
    global $pdo, $user_id;
    
    $rating_id = (int)$_POST['rating_id'] ?? 0;
    
    if ($rating_id <= 0) {
        throw new Exception('Rating ID tidak valid');
    }
    
    // Check if rating belongs to current user
    $stmt = $pdo->prepare("SELECT lapangan_id FROM ratings WHERE id = ? AND user_id = ?");
    $stmt->execute([$rating_id, $user_id]);
    $result = $stmt->fetch();
    
    if (!$result) {
        throw new Exception('Rating tidak ditemukan atau Anda tidak memiliki akses');
    }
    
    $lapangan_id = $result['lapangan_id'];
    
    // Delete rating
    $stmt = $pdo->prepare("DELETE FROM ratings WHERE id = ?");
    $stmt->execute([$rating_id]);
    
    // Update lapangan average rating
    updateLapanganRating($lapangan_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Rating berhasil dihapus'
    ]);
}

function updateLapanganRating($lapangan_id) {
    global $pdo;
    
    $stmt = $pdo->prepare(
        "SELECT AVG(rating) as avg_rating, COUNT(*) as total_rating 
         FROM ratings WHERE lapangan_id = ?"
    );
    $stmt->execute([$lapangan_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare(
        "UPDATE lapangan SET average_rating = ?, total_rating = ? WHERE id = ?"
    );
    $stmt->execute([
        $result['avg_rating'] ? round($result['avg_rating'], 1) : 0,
        $result['total_rating'] ? $result['total_rating'] : 0,
        $lapangan_id
    ]);
}
?>
