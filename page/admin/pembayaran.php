<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';

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
$page_title = "Pembayaran";
$page_subtitle = "Verifikasi pembayaran dari pelanggan";

// Database connection
global $pdo;
require_once '../../config/db.php';

// Get pembayaran statistics
$stat_pending = $pdo->query("SELECT COUNT(*) as total FROM pembayaran WHERE status='pending'")->fetch()['total'];
$stat_success = $pdo->query("SELECT COUNT(*) as total FROM pembayaran WHERE status='success'")->fetch()['total'];
$stat_failed = $pdo->query("SELECT COUNT(*) as total FROM pembayaran WHERE status='failed'")->fetch()['total'];
$stat_cancelled = $pdo->query("SELECT COUNT(*) as total FROM pembayaran WHERE status='cancelled'")->fetch()['total'];
$stat_refunded = $pdo->query("SELECT COUNT(*) as total FROM pembayaran WHERE status='refunded'")->fetch()['total'];
$stat_total = $pdo->query("SELECT COUNT(*) as total FROM pembayaran")->fetch()['total'];

// Get all pembayaran with booking and user info
$query = "SELECT p.*, 
                b.tanggal as booking_tanggal,
                b.jam_mulai,
                b.jam_selesai,
                b.total_harga,
                b.status as booking_status,
                u.name as customer_name,
                u.phone as customer_phone,
                u.email as customer_email,
                l.nama as lapangan_nama
          FROM pembayaran p
          INNER JOIN booking b ON p.booking_id = b.id
          INNER JOIN users u ON b.user_id = u.id
          INNER JOIN lapangan l ON b.lapangan_id = l.id
          ORDER BY p.created_at DESC";
$stmt = $pdo->query($query);
$pembayaran_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Pembayaran</p>
                <p class="stat-value"><?php echo number_format($stat_total); ?></p>
                <p class="stat-period">Sepanjang waktu</p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-money-bill"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pending</p>
                <p class="stat-value"><?php echo number_format($stat_pending); ?></p>
                <p class="stat-change neutral">Menunggu verifikasi</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-hourglass-start"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Terverifikasi</p>
                <p class="stat-value"><?php echo number_format($stat_success); ?></p>
                <p class="stat-change positive">Pembayaran sukses</p>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Ditolak</p>
                <p class="stat-value"><?php echo number_format($stat_failed); ?></p>
                <p class="stat-change negative">Pembayaran gagal</p>
            </div>
            <div class="stat-icon red">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Dibatalkan</p>
                <p class="stat-value"><?php echo number_format($stat_cancelled); ?></p>
                <p class="stat-change neutral">Booking dibatalkan</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Refund</p>
                <p class="stat-value"><?php echo number_format($stat_refunded); ?></p>
                <p class="stat-change neutral">Dana dikembalikan</p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-undo"></i>
            </div>
        </div>
    </div>
</div>

<!-- Pembayaran Filter -->
<div class="table-card">
    <div class="card-header">
        <h3>Daftar Pembayaran</h3>
        <div class="header-actions">
            <select id="filterStatus" class="filter-select">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="success">Terverifikasi</option>
                <option value="failed">Ditolak</option>
                <option value="cancelled">Dibatalkan</option>
                <option value="refunded">Refund</option>
            </select>
            <input type="text" id="searchInput" placeholder="Cari pembayaran..." class="search-input">
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Booking ID</th>
                    <th>Lapangan</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Alasan Refund</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="pembayaranTableBody">
                <?php if (empty($pembayaran_list)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px;">
                            <p class="text-muted">Belum ada data pembayaran</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pembayaran_list as $pembayaran): 
                        // Get initials
                        $name_parts = explode(' ', $pembayaran['customer_name']);
                        $initials = '';
                        foreach ($name_parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                        
                        // Avatar color
                        $colors = ['blue', 'purple', 'green', 'orange'];
                        $color = $colors[$pembayaran['id'] % 4];
                        
                        // Status badge
                        $status_class = '';
                        $status_text = '';
                        switch($pembayaran['status']) {
                            case 'pending':
                                $status_class = 'warning';
                                $status_text = 'Pending';
                                break;
                            case 'success':
                                $status_class = 'success';
                                $status_text = 'Terverifikasi';
                                break;
                            case 'failed':
                                $status_class = 'danger';
                                $status_text = 'Ditolak';
                                break;
                            case 'cancelled':
                                $status_class = 'danger';
                                $status_text = 'Dibatalkan';
                                break;
                            case 'refunded':
                                $status_class = 'info';
                                $status_text = 'Refund';
                                break;
                        }
                        
                        // Format date
                        $date = new DateTime($pembayaran['created_at']);
                        $formatted_date = $date->format('d M Y H:i');
                    ?>
                    <tr data-id="<?php echo $pembayaran['id']; ?>" data-status="<?php echo $pembayaran['status']; ?>">
                        <td>#<?php echo $pembayaran['id']; ?></td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar <?php echo $color; ?>"><?php echo $initials; ?></div>
                                <div>
                                    <p class="customer-name"><?php echo htmlspecialchars($pembayaran['customer_name']); ?></p>
                                    <p class="text-muted"><?php echo htmlspecialchars($pembayaran['customer_email'] ?? '-'); ?></p>
                                </div>
                            </div>
                        </td>
                        <td>#<?php echo $pembayaran['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($pembayaran['lapangan_nama']); ?></td>
                        <td>
                            <span class="metode-badge <?php echo strtolower($pembayaran['metode']); ?>">
                                <?php echo htmlspecialchars($pembayaran['metode']); ?>
                            </span>
                        </td>
                        <td><strong>Rp <?php echo number_format($pembayaran['jumlah'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>" id="status-<?php echo $pembayaran['id']; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td>
                            <small class="refund-reason">
                                <?php echo htmlspecialchars($pembayaran['refund_reason'] ?? '-'); ?>
                            </small>
                        </td>
                        <td><?php echo $formatted_date; ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($pembayaran['status'] == 'pending'): ?>
                                    <button onclick="viewProof(<?php echo $pembayaran['id']; ?>, '<?php echo htmlspecialchars($pembayaran['bukti_bayar']); ?>')" class="btn-action primary" title="Lihat Bukti">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button onclick="approvePayment(<?php echo $pembayaran['id']; ?>, <?php echo $pembayaran['booking_id']; ?>)" class="btn-action success" title="Terima">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectPayment(<?php echo $pembayaran['id']; ?>, <?php echo $pembayaran['booking_id']; ?>)" class="btn-action danger" title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php elseif ($pembayaran['status'] == 'success' && $pembayaran['booking_status'] != 'completed'): ?>
                                    <button onclick="viewProof(<?php echo $pembayaran['id']; ?>, '<?php echo htmlspecialchars($pembayaran['bukti_bayar']); ?>')" class="btn-action primary" title="Lihat Bukti">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button onclick="requestRefund(<?php echo $pembayaran['id']; ?>, <?php echo $pembayaran['booking_id']; ?>)" class="btn-action info" title="Refund">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                <?php else: ?>
                                    <button onclick="viewProof(<?php echo $pembayaran['id']; ?>, '<?php echo htmlspecialchars($pembayaran['bukti_bayar']); ?>')" class="btn-action primary" title="Lihat Bukti">
                                        <i class="fas fa-image"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk lihat bukti pembayaran -->
<div id="proofModal" class="proof-modal">
    <div class="proof-modal-content">
        <div class="proof-modal-header">
            <h3>Bukti Pembayaran</h3>
            <button class="proof-close-btn" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="proof-modal-body">
            <img id="proofImage" src="" alt="Bukti Pembayaran">
        </div>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<link rel="stylesheet" href="../../assets/css/admin_style/pembayaran.css">
<script src="../../assets/js/admin_js/pembayaran.js"></script>
