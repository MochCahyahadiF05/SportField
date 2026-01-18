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
$page_title = "Dashboard";
$page_subtitle = "Selamat datang kembali, " . htmlspecialchars($currentUser['name']) . "!";

// Start output buffering for page content
ob_start();
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
        <p class="stat-value">1,234</p>
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
        <p class="stat-value">Rp 45.5jt</p>
        <p class="stat-period">Bulan ini</p>
    </div>

    <!-- Card 3 -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon purple">
                <i class="fas fa-users"></i>
            </div>
            <span class="stat-change positive">+24</span>
        </div>
        <h3 class="stat-label">Pelanggan Baru</h3>
        <p class="stat-value">89</p>
        <p class="stat-period">Bulan ini</p>
    </div>

    <!-- Card 4 -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-building"></i>
            </div>
            <span class="stat-change neutral">12 Total</span>
        </div>
        <h3 class="stat-label">Lapangan Aktif</h3>
        <p class="stat-value">12</p>
        <p class="stat-period">Semua tersedia</p>
    </div>
</div>

<!-- Charts and Tables -->
<div class="content-grid">

    <!-- Recent Bookings -->
    <div class="table-card">
        <div class="card-header">
            <h3>Booking Terbaru</h3>
            <a href="#" class="view-all">Lihat Semua</a>
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
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar blue">JD</div>
                                <div>
                                    <p class="customer-name">John Doe</p>
                                </div>
                            </div>
                        </td>
                        <td>Futsal A</td>
                        <td class="text-muted">27 Des, 16:00</td>
                        <td>
                            <span class="status-badge success">Lunas</span>
                        </td>
                        <td class="text-right"><strong>Rp 150K</strong></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar purple">AS</div>
                                <div>
                                    <p class="customer-name">Alice Smith</p>
                                </div>
                            </div>
                        </td>
                        <td>Basket B</td>
                        <td class="text-muted">28 Des, 18:00</td>
                        <td>
                            <span class="status-badge warning">Pending</span>
                        </td>
                        <td class="text-right"><strong>Rp 200K</strong></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar green">BW</div>
                                <div>
                                    <p class="customer-name">Bob Williams</p>
                                </div>
                            </div>
                        </td>
                        <td>Volly C</td>
                        <td class="text-muted">29 Des, 20:00</td>
                        <td>
                            <span class="status-badge danger">Batal</span>
                        </td>
                        <td class="text-right"><strong>Rp 0K</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Revenue Chart Placeholder -->
    <div class="chart-card">
        <h3>Pendapatan Bulanan</h3>
        <div class="chart-placeholder">
            <p>Grafik Pendapatan Akan Ditampilkan Di Sini</p>
        </div>
    </div>
</div>
</div>
</main>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();
include '../includes/admin-layout.php';
?>