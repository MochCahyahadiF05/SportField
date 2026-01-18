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
$page_title = "Laporan";
$page_subtitle = "Analisis dan laporan penjualan";

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Pendapatan</p>
                <p class="stat-value">Rp 485M</p>
                <p class="stat-change positive">+18% vs tahun lalu</p>
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
                <p class="stat-value">3,248</p>
                <p class="stat-change positive">+42% vs tahun lalu</p>
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
                <p class="stat-value">Rp 149K</p>
                <p class="stat-change neutral">Per transaksi</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-calculator"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Tingkat Konversi</p>
                <p class="stat-value">68.5%</p>
                <p class="stat-change positive">+5.2% dari sebelumnya</p>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <div class="table-card">
        <div class="card-header">
            <h3>Laporan Pendapatan Bulanan</h3>
            <a href="#" class="view-all">Unduh Excel</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Booking</th>
                        <th>Pendapatan</th>
                        <th>Rata-rata per Booking</th>
                        <th>Pertumbuhan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Januari 2026</td>
                        <td>285</td>
                        <td>Rp 45,6M</td>
                        <td>Rp 160K</td>
                        <td><span class="status-badge success">+12%</span></td>
                    </tr>
                    <tr>
                        <td>Desember 2025</td>
                        <td>268</td>
                        <td>Rp 40,2M</td>
                        <td>Rp 150K</td>
                        <td><span class="status-badge success">+8%</span></td>
                    </tr>
                    <tr>
                        <td>November 2025</td>
                        <td>248</td>
                        <td>Rp 37,2M</td>
                        <td>Rp 150K</td>
                        <td><span class="status-badge success">+5%</span></td>
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
