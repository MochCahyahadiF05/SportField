<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/db.php';

class BookingHandler {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Cek jam yang sudah di-booking untuk lapangan tertentu pada tanggal tertentu
     */
    public function getBookedHours($lapangan_id, $tanggal) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT jam_mulai, jam_selesai FROM booking 
                 WHERE lapangan_id = ? AND tanggal = ? AND status != 'cancelled'"
            );
            $stmt->execute([$lapangan_id, $tanggal]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Validasi jam yang dipilih tidak bentrok dengan booking lain
     */
    public function validateTimeSlot($lapangan_id, $tanggal, $jam_mulai, $jam_selesai) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) as count FROM booking 
                 WHERE lapangan_id = ? AND tanggal = ? AND status != 'cancelled'
                 AND (
                     (jam_mulai < ? AND jam_selesai > ?) OR
                     (jam_mulai >= ? AND jam_mulai < ?) OR
                     (jam_selesai > ? AND jam_selesai <= ?)
                 )"
            );
            $stmt->execute([$lapangan_id, $tanggal, $jam_selesai, $jam_mulai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] == 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create booking baru
     */
    public function createBooking($user_id, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $total_harga) {
        try {
            // Validasi input
            if (empty($user_id) || empty($lapangan_id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
                return ['success' => false, 'message' => 'Data booking tidak lengkap'];
            }

            // Validasi jam
            if (!$this->validateTimeSlot($lapangan_id, $tanggal, $jam_mulai, $jam_selesai)) {
                return ['success' => false, 'message' => 'Jam tersebut sudah di-booking. Silakan pilih jam lain'];
            }

            // Insert booking
            $stmt = $this->pdo->prepare(
                "INSERT INTO booking (user_id, lapangan_id, tanggal, jam_mulai, jam_selesai, total_harga, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())"
            );
            $result = $stmt->execute([$user_id, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $total_harga]);

            if ($result) {
                $booking_id = $this->pdo->lastInsertId();
                return ['success' => true, 'message' => 'Booking berhasil dibuat', 'booking_id' => $booking_id];
            } else {
                return ['success' => false, 'message' => 'Gagal membuat booking'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get booking by ID
     */
    public function getBookingById($booking_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM booking WHERE id = ?");
            $stmt->execute([$booking_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Process payment
     */
    public function processPayment($booking_id, $metode, $jumlah, $bukti_bayar) {
        try {
            // Validasi payment method
            if (!in_array($metode, ['transfer', 'qris'])) {
                return ['success' => false, 'message' => 'Metode pembayaran tidak valid'];
            }

            // Insert payment
            $stmt = $this->pdo->prepare(
                "INSERT INTO pembayaran (booking_id, metode, jumlah, bukti_bayar, status, created_at)
                 VALUES (?, ?, ?, ?, 'pending', NOW())"
            );
            $result = $stmt->execute([$booking_id, $metode, $jumlah, $bukti_bayar]);

            if ($result) {
                $payment_id = $this->pdo->lastInsertId();
                return ['success' => true, 'message' => 'Pembayaran berhasil diproses', 'payment_id' => $payment_id];
            } else {
                return ['success' => false, 'message' => 'Gagal memproses pembayaran'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get booked hours as array of hours
     */
    public function getBookedHoursList($lapangan_id, $tanggal) {
        $booked = $this->getBookedHours($lapangan_id, $tanggal);
        $bookedHours = [];
        
        foreach ($booked as $booking) {
            $start = (int)substr($booking['jam_mulai'], 0, 2);
            $end = (int)substr($booking['jam_selesai'], 0, 2);
            for ($i = $start; $i < $end; $i++) {
                $bookedHours[] = $i;
            }
        }
        
        return array_unique($bookedHours);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $handler = new BookingHandler($pdo);

    switch ($action) {
        case 'check_login':
            header('Content-Type: application/json');
            echo json_encode(['logged_in' => isset($_SESSION['user_id'])]);
            break;

        case 'get_booked_hours':
            $lapangan_id = $_POST['lapangan_id'] ?? 0;
            $tanggal = $_POST['tanggal'] ?? '';
            
            if ($lapangan_id && $tanggal) {
                $booked = $handler->getBookedHoursList($lapangan_id, $tanggal);
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'booked_hours' => $booked]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
            }
            break;

        case 'create_booking':
            // Check user login
            if (!isset($_SESSION['user_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $lapangan_id = $_POST['lapangan_id'] ?? 0;
            $tanggal = $_POST['tanggal'] ?? '';
            $jam_mulai = $_POST['jam_mulai'] ?? '';
            $jam_selesai = $_POST['jam_selesai'] ?? '';
            $total_harga = $_POST['total_harga'] ?? 0;

            $result = $handler->createBooking($user_id, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $total_harga);
            header('Content-Type: application/json');
            echo json_encode($result);
            break;

        case 'process_payment':
            if (!isset($_SESSION['user_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
                exit;
            }

            $booking_id = $_POST['booking_id'] ?? 0;
            $metode = $_POST['metode'] ?? '';
            $jumlah = $_POST['jumlah'] ?? 0;
            $bukti_bayar = '';

            // Handle file upload
            if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == UPLOAD_ERR_OK) {
                $file = $_FILES['bukti_bayar'];
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $file['name'];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($filetype, $allowed)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Format file tidak didukung']);
                    exit;
                }

                if ($file['size'] > 5 * 1024 * 1024) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
                    exit;
                }

                $uploadDir = '../../assets/img/bukti-pembayaran/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFilename = time() . '_' . $filename;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFilename)) {
                    $bukti_bayar = 'assets/img/bukti-pembayaran/' . $newFilename;
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Gagal upload file']);
                    exit;
                }
            }

            $result = $handler->processPayment($booking_id, $metode, $jumlah, $bukti_bayar);
            header('Content-Type: application/json');
            echo json_encode($result);
            break;

        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenali']);
    }
}
?>
