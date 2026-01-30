<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';

// Set timezone ke Indonesia
date_default_timezone_set('Asia/Jakarta');

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
$page_title = "Laporan";
$page_subtitle = "Analisis dan laporan penjualan";

// Start output buffering for page content
ob_start();

// Database connection
global $pdo;
require_once '../../config/db.php';

// Get total revenue
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM booking WHERE status IN ('confirmed', 'completed')")->fetch()['total'];

// Get total bookings
$total_bookings = $pdo->query("SELECT COUNT(*) as total FROM booking")->fetch()['total'];

// Get average revenue per booking
$avg_revenue = $total_bookings > 0 ? $total_revenue / $total_bookings : 0;

// Get completion rate (completed vs total)
$completed_bookings = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE status = 'completed'")->fetch()['total'];
$completion_rate = $total_bookings > 0 ? ($completed_bookings / $total_bookings * 100) : 0;

// Get monthly revenue report
$monthly_report_query = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as bulan,
        DATE_FORMAT(created_at, '%M %Y') as bulan_format,
        COUNT(*) as jumlah_booking,
        SUM(total_harga) as pendapatan
    FROM booking 
    WHERE status IN ('confirmed', 'completed')
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY bulan DESC
    LIMIT 12
";

$stmt = $pdo->query($monthly_report_query);
$monthly_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle date filter from GET/POST
$tanggal_dari = $_GET['tanggal_dari'] ?? date('Y-m-01'); // Default awal bulan
$tanggal_sampai = $_GET['tanggal_sampai'] ?? date('Y-m-d'); // Default hari ini

// Handle report type filter
$tipe_laporan = $_GET['tipe_laporan'] ?? 'harian'; // Default harian

// Get filtered revenue by date range (only completed and confirmed)
$filtered_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM booking WHERE status IN ('confirmed', 'completed') AND DATE(tanggal) >= '$tanggal_dari' AND DATE(tanggal) <= '$tanggal_sampai'")->fetch()['total'];

// Get filtered bookings count (only completed and confirmed)
$filtered_bookings = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE status IN ('confirmed', 'completed') AND DATE(tanggal) >= '$tanggal_dari' AND DATE(tanggal) <= '$tanggal_sampai'")->fetch()['total'];

// Get detailed report by date with filtering (only completed and confirmed for consistency)
$detailed_report_query = "
    SELECT 
        tanggal,
        DATE_FORMAT(tanggal, '%d %M %Y') as tanggal_format,
        COUNT(*) as jumlah_booking,
        SUM(total_harga) as pendapatan
    FROM booking
    WHERE status IN ('confirmed', 'completed')
    AND DATE(tanggal) >= '$tanggal_dari' 
    AND DATE(tanggal) <= '$tanggal_sampai'
    GROUP BY tanggal
    ORDER BY tanggal DESC
";

// Get monthly report
$monthly_report_query = "
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') as bulan,
        DATE_FORMAT(tanggal, '%M %Y') as bulan_format,
        COUNT(*) as jumlah_booking,
        SUM(total_harga) as pendapatan
    FROM booking
    WHERE status IN ('confirmed', 'completed')
    AND DATE(tanggal) >= '$tanggal_dari' 
    AND DATE(tanggal) <= '$tanggal_sampai'
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan DESC
";

$stmt = $pdo->query($detailed_report_query);
$detailed_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query($monthly_report_query);
$monthly_report = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Date Filter & Report Type -->
<div class="filter-card">
    <div class="filter-title">
        <i class="fas fa-calendar-alt"></i> Filter Laporan
    </div>
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Tipe Laporan</label>
            <select name="tipe_laporan" class="form-select">
                <option value="harian" <?php echo $tipe_laporan === 'harian' ? 'selected' : ''; ?>>Laporan Harian</option>
                <option value="bulanan" <?php echo $tipe_laporan === 'bulanan' ? 'selected' : ''; ?>>Laporan Bulanan</option>
            </select>
        </div>
        <div class="form-group">
            <label>Dari Tanggal</label>
            <input type="date" name="tanggal_dari" value="<?php echo $tanggal_dari; ?>" required>
        </div>
        <div class="form-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="tanggal_sampai" value="<?php echo $tanggal_sampai; ?>" required>
        </div>
        <button type="submit" class="btn-filter">
            <i class="fas fa-search"></i> Cari
        </button>
        <a href="laporan.php" class="btn-reset">
            <i class="fas fa-redo"></i> Reset
        </a>
    </form>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Pendapatan</p>
                <p class="stat-value">Rp <?php echo number_format($filtered_revenue / 1000000, 1); ?> Jt</p>
                <p class="stat-period"><?php echo date('d M Y', strtotime($tanggal_dari)) . ' - ' . date('d M Y', strtotime($tanggal_sampai)); ?></p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Booking</p>
                <p class="stat-value"><?php echo number_format($filtered_bookings); ?></p>
                <p class="stat-change neutral">Periode terpilih</p>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Rata-rata Pendapatan</p>
                <p class="stat-value">Rp <?php echo number_format($filtered_bookings > 0 ? $filtered_revenue / $filtered_bookings : 0, 0, ',', '.'); ?></p>
                <p class="stat-change neutral">Per booking</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-calculator"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="card-header">
        <h3>Laporan Pendapatan <?php echo $tipe_laporan === 'harian' ? 'Harian' : 'Bulanan'; ?></h3>
        <div class="header-actions">
            <button onclick="exportToPDF('<?php echo $tanggal_dari; ?>', '<?php echo $tanggal_sampai; ?>', '<?php echo $tipe_laporan; ?>')" class="btn-export">
                <i class="fas fa-file-pdf"></i> Unduh PDF
            </button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="laporanTable">
            <thead>
                <tr>
                    <th><?php echo $tipe_laporan === 'harian' ? 'Tanggal' : 'Bulan'; ?></th>
                    <th>Jumlah Booking</th>
                    <th>Pendapatan</th>
                    <th>Rata-rata per Booking</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($tipe_laporan === 'harian') {
                    if (empty($detailed_report)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px;">
                                <p class="text-muted">Tidak ada data untuk periode yang dipilih</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($detailed_report as $report):
                            $avg = $report['jumlah_booking'] > 0 ? $report['pendapatan'] / $report['jumlah_booking'] : 0;
                        ?>
                        <tr data-tanggal="<?php echo $report['tanggal']; ?>">
                            <td><?php echo $report['tanggal_format']; ?></td>
                            <td><?php echo number_format($report['jumlah_booking']); ?></td>
                            <td>Rp <?php echo number_format($report['pendapatan'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($avg, 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php } else {
                    if (empty($monthly_report)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px;">
                                <p class="text-muted">Tidak ada data untuk periode yang dipilih</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($monthly_report as $report):
                            $avg = $report['jumlah_booking'] > 0 ? $report['pendapatan'] / $report['jumlah_booking'] : 0;
                        ?>
                        <tr data-bulan="<?php echo $report['bulan']; ?>">
                            <td><?php echo $report['bulan_format']; ?></td>
                            <td><?php echo number_format($report['jumlah_booking']); ?></td>
                            <td>Rp <?php echo number_format($report['pendapatan'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($avg, 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<link rel="stylesheet" href="../../assets/css/admin_style/laporan.css">
<script src="../../assets/js/admin_js/laporan.js"></script>
