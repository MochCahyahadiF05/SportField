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
$page_title = "Pelanggan";
$page_subtitle = "Kelola data pelanggan";

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Pelanggan</p>
                <p class="stat-value">856</p>
                <p class="stat-change positive">+42 bulan ini</p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pelanggan Aktif</p>
                <p class="stat-value">234</p>
                <p class="stat-change positive">+8% dari minggu lalu</p>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Member Premium</p>
                <p class="stat-value">120</p>
                <p class="stat-change neutral">+5 bulan ini</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-crown"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Rating Rata-rata</p>
                <p class="stat-value">4.8</p>
                <p class="stat-change positive">Dari 856 review</p>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <div class="table-card">
        <div class="card-header">
            <h3>Daftar Pelanggan</h3>
            <a href="#" class="view-all">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Bergabung</th>
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
                                </div>
                            </div>
                        </td>
                        <td>ahmad@email.com</td>
                        <td>+62-812-345-6789</td>
                        <td>15 Des 2025</td>
                        <td><span class="status-badge success">Aktif</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar purple">SR</div>
                                <div>
                                    <p class="customer-name">Siti Rahmawati</p>
                                </div>
                            </div>
                        </td>
                        <td>siti@email.com</td>
                        <td>+62-812-345-6790</td>
                        <td>20 Des 2025</td>
                        <td><span class="status-badge success">Aktif</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar green">RS</div>
                                <div>
                                    <p class="customer-name">Roni Sutrisno</p>
                                </div>
                            </div>
                        </td>
                        <td>roni@email.com</td>
                        <td>+62-812-345-6791</td>
                        <td>10 Jan 2026</td>
                        <td><span class="status-badge success">Aktif</span></td>
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
