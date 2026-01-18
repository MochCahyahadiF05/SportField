<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SportField</title>
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/navbar-footer.css">
</head>

<body>
    <?php 
    require_once '../../config/config.php';
    require_once '../../config/Auth.php';

    // Check if user is logged in
    if (!Auth::isLoggedIn()) {
        header('Location: ' . BASE_URL . 'page/auth/login.php');
        exit();
    }

    $currentUser = Auth::getUser();
    $userName = htmlspecialchars($currentUser['name']);
    $userEmail = htmlspecialchars($currentUser['email']);
    $userPhone = !empty($currentUser['phone']) ? htmlspecialchars($currentUser['phone']) : 'Belum diisi';
    $userJoinDate = date('d F Y', strtotime($currentUser['created_at']));
    ?>

    <!-- Navbar -->
   <?php include '../includes/navbar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="dashboard-header">
                <h1>Dashboard Saya</h1>
                <p>Kelola profil dan riwayat booking Anda</p>
            </div>

            <div class="dashboard-layout">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="sidebar-menu">
                        <button class="menu-item active" data-tab="profile">
                            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Profil Saya</span>
                        </button>
                        <button class="menu-item" data-tab="history">
                            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Riwayat Penyewaan</span>
                        </button>
                        <button class="menu-item" data-tab="settings">
                            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Pengaturan Akun</span>
                        </button>
                    </div>
                </aside>

                <!-- Content Area -->
                <div class="content-area">
                    <!-- Profile Tab -->
                    <div id="profile" class="tab-content active">
                        <div class="card">
                            <div class="card-header">
                                <h2>Profil Saya</h2>
                                <button class="btn-edit" onclick="editProfile()">Edit Profil</button>
                            </div>
                            <div class="profile-section">
                                <div class="profile-avatar-section">
                                    <?php 
                                    // Generate avatar dengan inisial
                                    $nameParts = explode(' ', $userName);
                                    $initials = strtoupper(substr($nameParts[0], 0, 1));
                                    if (isset($nameParts[1])) {
                                        $initials .= strtoupper(substr($nameParts[1], 0, 1));
                                    }
                                    ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=16A34A&color=fff&size=120" alt="Profile" class="profile-avatar">
                                    <button class="btn-change-photo">Ganti Foto</button>
                                </div>
                                <div class="profile-info">
                                    <div class="info-row">
                                        <div class="info-item">
                                            <label>Nama Lengkap</label>
                                            <p><?php echo $userName; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <label>Role</label>
                                            <p><?php echo htmlspecialchars($currentUser['role']); ?></p>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-item">
                                            <label>Email</label>
                                            <p><?php echo $userEmail; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <label>Nomor Telepon</label>
                                            <p><?php echo $userPhone; ?></p>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-item">
                                            <label>Tanggal Bergabung</label>
                                            <p><?php echo $userJoinDate; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <label>Total Booking</label>
                                            <p class="highlight">0 Kali</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h2>Statistik Booking</h2>
                            </div>
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon">📅</div>
                                    <div class="stat-info">
                                        <h3>12</h3>
                                        <p>Total Booking</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">⏳</div>
                                    <div class="stat-info">
                                        <h3>2</h3>
                                        <p>Booking Aktif</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">✅</div>
                                    <div class="stat-info">
                                        <h3>10</h3>
                                        <p>Selesai</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">🎁</div>
                                    <div class="stat-info">
                                        <h3>450</h3>
                                        <p>Poin Reward</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div id="history" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2>Riwayat Penyewaan</h2>
                                <div class="filter-tabs">
                                    <button class="filter-btn active" data-filter="all">Semua</button>
                                    <button class="filter-btn" data-filter="active">Aktif</button>
                                    <button class="filter-btn" data-filter="completed">Selesai</button>
                                    <button class="filter-btn" data-filter="cancelled">Dibatalkan</button>
                                </div>
                            </div>
                            <div class="history-list">
                                <div class="history-item" data-status="active">
                                    <div class="history-image">
                                        <img src="https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=200" alt="Lapangan">
                                    </div>
                                    <div class="history-info">
                                        <div class="history-header">
                                            <h3>Lapangan Futsal A</h3>
                                            <span class="badge badge-active">Aktif</span>
                                        </div>
                                        <div class="history-details">
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Sabtu, 5 Januari 2025</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>14:00 - 16:00 (2 Jam)</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span>Rp 300.000</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="history-actions">
                                        <button class="btn-action btn-detail">Detail</button>
                                        <button class="btn-action btn-cancel">Batalkan</button>
                                    </div>
                                </div>

                                <div class="history-item" data-status="active">
                                    <div class="history-image">
                                        <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=200" alt="Lapangan">
                                    </div>
                                    <div class="history-info">
                                        <div class="history-header">
                                            <h3>Lapangan Basket Premium</h3>
                                            <span class="badge badge-active">Aktif</span>
                                        </div>
                                        <div class="history-details">
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Minggu, 6 Januari 2025</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>09:00 - 11:00 (2 Jam)</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span>Rp 400.000</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="history-actions">
                                        <button class="btn-action btn-detail">Detail</button>
                                        <button class="btn-action btn-cancel">Batalkan</button>
                                    </div>
                                </div>

                                <div class="history-item" data-status="completed">
                                    <div class="history-image">
                                        <img src="https://images.unsplash.com/photo-1553778263-73a83bab9b0c?q=80&w=200" alt="Lapangan">
                                    </div>
                                    <div class="history-info">
                                        <div class="history-header">
                                            <h3>Lapangan Futsal B</h3>
                                            <span class="badge badge-completed">Selesai</span>
                                        </div>
                                        <div class="history-details">
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Jumat, 27 Desember 2024</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>18:00 - 20:00 (2 Jam)</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span>Rp 300.000</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="history-actions">
                                        <button class="btn-action btn-detail">Detail</button>
                                        <button class="btn-action btn-primary">Booking Lagi</button>
                                    </div>
                                </div>

                                <div class="history-item" data-status="cancelled">
                                    <div class="history-image">
                                        <img src="https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=200" alt="Lapangan">
                                    </div>
                                    <div class="history-info">
                                        <div class="history-header">
                                            <h3>Lapangan Voli Indoor</h3>
                                            <span class="badge badge-cancelled">Dibatalkan</span>
                                        </div>
                                        <div class="history-details">
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Rabu, 25 Desember 2024</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>16:00 - 18:00 (2 Jam)</span>
                                            </div>
                                            <div class="detail-item">
                                                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span>Rp 350.000</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="history-actions">
                                        <button class="btn-action btn-detail">Detail</button>
                                        <button class="btn-action btn-primary">Booking Lagi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div id="settings" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2>Pengaturan Akun</h2>
                            </div>
                            <form class="settings-form">
                                <div class="form-section">
                                    <h3>Informasi Pribadi</h3>

                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" value="<?php echo $userName; ?>" class="form-input">
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" value="<?php echo $userEmail; ?>" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Telepon</label>
                                        <input type="tel" value="<?php echo $userPhone; ?>" class="form-input">
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h3>Ubah Password</h3>
                                    <div class="form-group">
                                        <label>Password Lama</label>
                                        <input type="password" class="form-input" placeholder="Masukkan password lama">
                                    </div>
                                    <div class="form-group">
                                        <label>Password Baru</label>
                                        <input type="password" class="form-input" placeholder="Masukkan password baru">
                                    </div>
                                    <div class="form-group">
                                        <label>Konfirmasi Password Baru</label>
                                        <input type="password" class="form-input" placeholder="Konfirmasi password baru">
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h3>Notifikasi</h3>
                                    <div class="toggle-group">
                                        <label class="toggle-item">
                                            <input type="checkbox" checked>
                                            <span class="toggle-label">Email Notifikasi Booking</span>
                                        </label>
                                        <label class="toggle-item">
                                            <input type="checkbox" checked>
                                            <span class="toggle-label">Notifikasi Promo & Diskon</span>
                                        </label>
                                        <label class="toggle-item">
                                            <input type="checkbox">
                                            <span class="toggle-label">Newsletter Bulanan</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn-secondary">Batal</button>
                                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>

                        <div class="card danger-zone">
                            <div class="card-header">
                                <h2>Peringatan!</h2>
                            </div>
                            <div class="danger-content">
                                <div class="danger-info">
                                    <h3>Hapus Akun</h3>
                                    <p>Setelah akun dihapus, semua data Anda akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                                <button class="btn-danger" onclick="deleteAccount()">Hapus Akun</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
    <script src="../../assets/js/profile.js"></script>
</body>

</html>