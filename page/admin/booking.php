<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'page/auth/login.php');
    exit();
}

// Check if user is ADMIN
if (!Auth::isAdmin()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$currentUser = Auth::getUser();

// Set page-specific variables
$page_title = "Booking";
$page_subtitle = "Kelola semua booking lapangan";

// Database connection
global $pdo;
require_once '../../config/db.php';

// Get booking statistics
$stat_total = $pdo->query("SELECT COUNT(*) as total FROM booking")->fetch()['total'];
$stat_pending = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE status='pending'")->fetch()['total'];
$stat_confirmed = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE status='confirmed'")->fetch()['total'];
$stat_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM booking WHERE status IN ('confirmed', 'completed')")->fetch()['total'];

// Get all bookings with user, lapangan, and pembayaran info
$query = "SELECT b.*, 
                u.name as customer_name, 
                u.phone as customer_phone,
                l.nama as lapangan_nama,
                COALESCE(p.status, 'pending') as pembayaran_status,
                p.metode as pembayaran_metode
          FROM booking b
          INNER JOIN users u ON b.user_id = u.id
          INNER JOIN lapangan l ON b.lapangan_id = l.id
          LEFT JOIN pembayaran p ON b.id = p.booking_id
          ORDER BY b.created_at DESC";
$stmt = $pdo->query($query);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Booking</p>
                <p class="stat-value"><?php echo number_format($stat_total); ?></p>
                <p class="stat-period">Sepanjang waktu</p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Booking Aktif</p>
                <p class="stat-value"><?php echo number_format($stat_confirmed); ?></p>
                <p class="stat-change positive">Terkonfirmasi</p>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-hourglass-start"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pending</p>
                <p class="stat-value"><?php echo number_format($stat_pending); ?></p>
                <p class="stat-change neutral">Menunggu konfirmasi</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pendapatan</p>
                <p class="stat-value">Rp <?php echo number_format($stat_revenue / 1000000, 1); ?> Jt</p>
                <p class="stat-change positive">Total terkonfirmasi</p>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table Card Full Width -->
<div class="table-card">
    <div class="card-header">
        <h3>Semua Booking</h3>
        <div class="header-actions">
            <input type="text" id="searchInput" placeholder="Cari booking..." class="search-input">
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="bookingTableBody">
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <p class="text-muted">Belum ada data booking</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): 
                        // Get initials
                        $name_parts = explode(' ', $booking['customer_name']);
                        $initials = '';
                        foreach ($name_parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                        
                        // Avatar color
                        $colors = ['blue', 'purple', 'green', 'orange'];
                        $color = $colors[$booking['id'] % 4];
                        
                        // Status badge
                        $status_class = '';
                        $status_text = '';
                        switch($booking['status']) {
                            case 'pending':
                                $status_class = 'warning';
                                $status_text = 'Pending';
                                break;
                            case 'confirmed':
                                $status_class = 'success';
                                $status_text = 'Konfirmasi';
                                break;
                            case 'cancelled':
                                $status_class = 'danger';
                                $status_text = 'Dibatalkan';
                                break;
                            case 'completed':
                                $status_class = 'info';
                                $status_text = 'Selesai';
                                break;
                        }
                        
                        // Format date
                        $date = new DateTime($booking['tanggal']);
                        $formatted_date = $date->format('d M Y');
                        
                        // Format time
                        $jam_mulai = substr($booking['jam_mulai'], 0, 5);
                        $jam_selesai = substr($booking['jam_selesai'], 0, 5);
                    ?>
                    <tr data-id="<?php echo $booking['id']; ?>">
                        <td>#<?php echo $booking['id']; ?></td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar <?php echo $color; ?>"><?php echo $initials; ?></div>
                                <div>
                                    <p class="customer-name"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
                                    <p class="text-muted"><?php echo htmlspecialchars($booking['customer_phone'] ?? '-'); ?></p>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($booking['lapangan_nama']); ?></td>
                        <td><?php echo $formatted_date; ?></td>
                        <td><?php echo $jam_mulai . ' - ' . $jam_selesai; ?></td>
                        <td>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge <?php 
                                $pembayaran_class = '';
                                $pembayaran_text = '';
                                switch($booking['pembayaran_status']) {
                                    case 'success':
                                        $pembayaran_class = 'success';
                                        $pembayaran_text = 'Lunas';
                                        break;
                                    case 'failed':
                                        $pembayaran_class = 'danger';
                                        $pembayaran_text = 'Ditolak';
                                        break;
                                    case 'cancelled':
                                        $pembayaran_class = 'danger';
                                        $pembayaran_text = 'Dibatalkan';
                                        break;
                                    case 'refunded':
                                        $pembayaran_class = 'info';
                                        $pembayaran_text = 'Refund';
                                        break;
                                    default:
                                        $pembayaran_class = 'warning';
                                        $pembayaran_text = 'Pending';
                                }
                                echo $pembayaran_class;
                            ?>" id="payment-<?php echo $booking['id']; ?>">
                                <?php echo $pembayaran_text; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>" id="status-<?php echo $booking['id']; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'confirmed')" 
                                            class="btn-action success" 
                                            title="Konfirmasi"
                                            <?php echo ($booking['pembayaran_status'] !== 'success') ? 'disabled' : ''; ?>>
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'cancelled')" class="btn-action danger" title="Batalkan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php elseif ($booking['status'] == 'confirmed'): ?>
                                    <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'completed')" class="btn-action info" title="Selesai">
                                        <i class="fas fa-flag-checkered"></i>
                                    </button>
                                    <button onclick="updateStatus(<?php echo $booking['id']; ?>, 'cancelled')" class="btn-action danger" title="Batalkan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                                <button onclick="viewDetail(<?php echo $booking['id']; ?>)" class="btn-action primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Add page-specific scripts
$page_scripts = '<script src="../../assets/js/admin_js/booking.js"></script>';

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<link rel="stylesheet" href="../../assets/css/admin_style/booking.css">
<script src="../../assets/js/admin_js/booking.js"></script>
