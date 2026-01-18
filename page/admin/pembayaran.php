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
$page_subtitle = "Kelola transaksi dan pembayaran";

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Transaksi</p>
                <p class="stat-value">1,856</p>
                <p class="stat-change positive">+156 bulan ini</p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Transaksi Sukses</p>
                <p class="stat-value">1,820</p>
                <p class="stat-change positive">98.1% success rate</p>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pending</p>
                <p class="stat-value">28</p>
                <p class="stat-change warning">Menunggu konfirmasi</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pendapatan Bulan Ini</p>
                <p class="stat-value">Rp 125,5M</p>
                <p class="stat-change positive">+22% vs bulan lalu</p>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <div class="table-card">
        <div class="card-header">
            <h3>Transaksi Terbaru</h3>
            <a href="#" class="view-all">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#TRX-001234</td>
                        <td>Ahmad Wijaya</td>
                        <td>Rp 150.000</td>
                        <td>Transfer Bank</td>
                        <td>18 Jan 2026</td>
                        <td><span class="status-badge success">Sukses</span></td>
                    </tr>
                    <tr>
                        <td>#TRX-001235</td>
                        <td>Siti Rahmawati</td>
                        <td>Rp 240.000</td>
                        <td>E-Wallet</td>
                        <td>18 Jan 2026</td>
                        <td><span class="status-badge success">Sukses</span></td>
                    </tr>
                    <tr>
                        <td>#TRX-001236</td>
                        <td>Roni Sutrisno</td>
                        <td>Rp 380.000</td>
                        <td>Kartu Kredit</td>
                        <td>18 Jan 2026</td>
                        <td><span class="status-badge warning">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<script src="../../assets/js/admin_js/dashboard.js"></script>
