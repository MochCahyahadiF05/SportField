<?php
// Get current page filename untuk highlight active menu
$current_page = basename($_SERVER['PHP_SELF']);
require_once $_SERVER['DOCUMENT_ROOT'] . '/UAS/config/config.php';
?>

<aside id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <!-- Logo -->
        <div class="logo-section">
            <div class="logo-icon">
                <img src="<?php echo ASSETS_URL; ?>img/Logo.png" alt="SportField logo" style="width: 40px; height: 40px; object-fit: contain;">
            </div>
            <div class="logo-text">
                <h1>SportField</h1>
                <p>Admin Panel</p>
            </div>
        </div>

        <!-- Menu Items -->
        <ul class="menu-list">
            <li>
                <a href="dashboard.php" class="sidebar-item <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="booking.php" class="sidebar-item <?php echo ($current_page === 'booking.php') ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i>
                    <span>Booking</span>
                    <span class="badge">5</span>
                </a>
            </li>
            <li>
                <a href="manajemen-lapangan.php" class="sidebar-item <?php echo ($current_page === 'manajemen-lapangan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i>
                    <span>Lapangan</span>
                </a>
            </li>
            <li>
                <a href="kelola-jenis.php" class="sidebar-item <?php echo ($current_page === 'kelola-jenis.php') ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i>
                    <span>Jenis Olahraga</span>
                </a>
            </li>
            <li>
                <a href="kelola-fasilitas.php" class="sidebar-item <?php echo ($current_page === 'kelola-fasilitas.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tools"></i>
                    <span>Fasilitas</span>
                </a>
            </li>
            <li>
                <a href="pelanggan.php" class="sidebar-item <?php echo ($current_page === 'pelanggan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Pelanggan</span>
                </a>
            </li>
            <li>
                <a href="pembayaran.php" class="sidebar-item <?php echo ($current_page === 'pembayaran.php') ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card"></i>
                    <span>Pembayaran</span>
                </a>
            </li>
            <li>
                <a href="laporan.php" class="sidebar-item <?php echo ($current_page === 'laporan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
            </li>
            
        </ul>

        <!-- Logout -->
        <div class="logout-section">
            <a href="#" onclick="handleAdminLogout(event)" class="sidebar-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<script>
function handleAdminLogout(event) {
    event.preventDefault();
    
    if (confirm('Apakah Anda yakin ingin logout?')) {
        const formData = new FormData();
        formData.append('action', 'logout');

        fetch('../../page/auth/process_auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '../../index.php';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat logout');
        });
    }
}
</script>