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
$page_title = "Booking";
$page_subtitle = "Kelola semua booking lapangan";

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Booking</p>
                <p class="stat-value">1,234</p>
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
                <p class="stat-value">24</p>
                <p class="stat-change positive">+3 hari ini</p>
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
                <p class="stat-value">8</p>
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
                <p class="stat-value">Rp 12,5M</p>
                <p class="stat-change positive">+15% bulan ini</p>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
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
                        <th>Jam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar blue">AW</div>
                                <div>
                                    <p class="customer-name">Ahmad Wijaya</p>
                                    <p class="text-muted">+62-812-345-6789</p>
                                </div>
                            </div>
                        </td>
                        <td>Futsal A</td>
                        <td>18 Jan 2026</td>
                        <td>19:00 - 20:00</td>
                        <td><span class="status-badge success">Konfirmasi</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar purple">SR</div>
                                <div>
                                    <p class="customer-name">Siti Rahmawati</p>
                                    <p class="text-muted">+62-812-345-6790</p>
                                </div>
                            </div>
                        </td>
                        <td>Badminton B</td>
                        <td>18 Jan 2026</td>
                        <td>20:00 - 21:00</td>
                        <td><span class="status-badge warning">Pending</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar green">RS</div>
                                <div>
                                    <p class="customer-name">Roni Sutrisno</p>
                                    <p class="text-muted">+62-812-345-6791</p>
                                </div>
                            </div>
                        </td>
                        <td>Voli Beach</td>
                        <td>17 Jan 2026</td>
                        <td>18:00 - 19:30</td>
                        <td><span class="status-badge success">Selesai</span></td>
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
