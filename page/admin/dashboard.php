<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';

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
$page_title = "Dashboard";
$page_subtitle = "Selamat datang kembali, " . htmlspecialchars($currentUser['name']) . "!";

// Start output buffering for page content
ob_start();

// Database connection
global $pdo;
require_once '../../config/db.php';

// Get this month's stats
$current_month = date('Y-m');
$total_bookings_month = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$current_month'")->fetch()['total'];
$total_revenue_month = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM booking WHERE status IN ('confirmed', 'completed') AND DATE_FORMAT(tanggal, '%Y-%m') = '$current_month'")->fetch()['total'];
$new_customers = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'")->fetch()['total'];
$total_fields = $pdo->query("SELECT COUNT(*) as total FROM lapangan")->fetch()['total'];

// Get monthly revenue for last 12 months
$monthly_data = $pdo->query("
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') as bulan,
        DATE_FORMAT(tanggal, '%b %y') as bulan_format,
        SUM(CASE WHEN status IN ('confirmed', 'completed') THEN total_harga ELSE 0 END) as pendapatan
    FROM booking
    WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart
$chart_labels = [];
$chart_data = [];
foreach ($monthly_data as $data) {
    $chart_labels[] = $data['bulan_format'];
    $chart_data[] = $data['pendapatan'];
}

// Get 3 latest bookings
$latest_bookings = $pdo->query("
    SELECT 
        b.id,
        u.name as customer_name,
        l.nama as lapangan_name,
        b.tanggal,
        b.jam_mulai,
        b.status,
        b.total_harga
    FROM booking b
    JOIN users u ON b.user_id = u.id
    JOIN lapangan l ON b.lapangan_id = l.id
    ORDER BY b.tanggal DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- Card 1 -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon blue">
                <i class="fas fa-calendar"></i>
            </div>
            <span class="stat-change positive">+12%</span>
        </div>
        <h3 class="stat-label">Total Booking</h3>
        <p class="stat-value"><?php echo number_format($total_bookings_month); ?></p>
        <p class="stat-period">Bulan ini</p>
    </div>

    <!-- Card 2 -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon green">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <span class="stat-change positive">+8%</span>
        </div>
        <h3 class="stat-label">Total Pendapatan</h3>
        <p class="stat-value">Rp <?php echo number_format($total_revenue_month / 1000000, 1); ?>jt</p>
        <p class="stat-period">Bulan ini</p>
    </div>

    <!-- Card 3 -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon purple">
                <i class="fas fa-users"></i>
            </div>
            <span class="stat-change positive">+<?php echo $new_customers; ?></span>
        </div>
        <h3 class="stat-label">Pelanggan Baru</h3>
        <p class="stat-value"><?php echo $new_customers; ?></p>
        <p class="stat-period">Bulan ini</p>
    </div>

    <!-- Card 4 -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-building"></i>
            </div>
            <span class="stat-change neutral"><?php echo $total_fields; ?> Total</span>
        </div>
        <h3 class="stat-label">Lapangan Aktif</h3>
        <p class="stat-value"><?php echo $total_fields; ?></p>
        <p class="stat-period">Semua tersedia</p>
    </div>
</div>

<!-- Charts and Tables -->
<div class="content-grid">

    <!-- Recent Bookings -->
    <div class="table-card">
        <div class="card-header">
            <h3>Booking Terbaru</h3>
            <a href="booking.php" class="view-all">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-right">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest_bookings as $booking): ?>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar blue"><?php echo strtoupper(substr($booking['customer_name'], 0, 2)); ?></div>
                                <div>
                                    <p class="customer-name"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($booking['lapangan_name']); ?></td>
                        <td class="text-muted"><?php echo date('d M, H:i', strtotime($booking['tanggal'] . ' ' . $booking['jam_mulai'])); ?></td>
                        <td>
                            <?php 
                            $status_class = $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : ($booking['status'] === 'cancelled' ? 'danger' : 'success'));
                            $status_text = $booking['status'] === 'completed' ? 'Selesai' : ($booking['status'] === 'pending' ? 'Pending' : ($booking['status'] === 'cancelled' ? 'Batal' : 'Lunas'));
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </td>
                        <td class="text-right"><strong>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="chart-card">
        <h3>Pendapatan Bulanan (12 Bulan Terakhir)</h3>
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();
include '../includes/admin-layout.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Revenue Chart Data
const chartLabels = <?php echo json_encode($chart_labels); ?>;
const chartData = <?php echo json_encode($chart_data); ?>;

const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: chartData,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: '#2563eb',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                    }
                }
            }
        }
    }
});
</script>
