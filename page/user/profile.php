<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SportField</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/img/SportFields.png">
    <link rel="shortcut icon" href="../../assets/img/SportFields.png">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/navbar-footer.css">
    <script src="https://kit.fontawesome.com/80f227685e.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php 
    require_once '../../config/config.php';
    require_once '../../config/auth.php';
    require_once '../../config/db.php';

    // Set timezone
    date_default_timezone_set('Asia/Jakarta');

    // Check if user is logged in
    if (!Auth::isLoggedIn()) {
        header('Location: ' . BASE_URL . 'page/auth/login.php');
        exit();
    }

    $currentUser = Auth::getUser();
    $userId = $currentUser['id'];
    $userName = htmlspecialchars($currentUser['name']);
    $userEmail = htmlspecialchars($currentUser['email']);
    $userPhone = !empty($currentUser['phone']) ? htmlspecialchars($currentUser['phone']) : 'Belum diisi';
    $userJoinDate = date('d F Y', strtotime($currentUser['created_at']));

    // Get booking statistics from database
    global $pdo;
    
    // Total booking
    $total_booking = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE user_id = $userId")->fetch()['total'];
    
    // Booking aktif (pending atau confirmed)
    $booking_aktif = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE user_id = $userId AND status IN ('pending', 'confirmed')")->fetch()['total'];
    
    // Booking selesai
    $booking_selesai = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE user_id = $userId AND status = 'completed'")->fetch()['total'];
    

    // Get user bookings from database
    $bookings = $pdo->query("
        SELECT 
            b.id,
            b.tanggal,
            b.jam_mulai,
            b.jam_selesai,
            b.total_harga,
            b.status,
            l.nama as lapangan_name,
            l.id as lapangan_id,
            l.gambar as lapangan_foto
        FROM booking b
        JOIN lapangan l ON b.lapangan_id = l.id
        WHERE b.user_id = $userId
        ORDER BY b.tanggal DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
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
                                            <p class="highlight"><?php echo $total_booking; ?> Kali</p>
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
                                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                                    <div class="stat-info">
                                        <h3><?php echo $total_booking; ?></h3>
                                        <p>Total Booking</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                                    <div class="stat-info">
                                        <h3><?php echo $booking_aktif; ?></h3>
                                        <p>Booking Aktif</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="stat-info">
                                        <h3><?php echo $booking_selesai; ?></h3>
                                        <p>Selesai</p>
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
                                <?php if (count($bookings) > 0): ?>
                                    <?php foreach ($bookings as $booking): 
                                        $status_class = $booking['status'] === 'completed' ? 'completed' : ($booking['status'] === 'cancelled' ? 'cancelled' : 'active');
                                        $status_text = $booking['status'] === 'completed' ? 'Selesai' : ($booking['status'] === 'cancelled' ? 'Dibatalkan' : 'Aktif');
                                        // Fix image path - remove leading slash if exists
                                        $foto = !empty($booking['lapangan_foto']) ? BASE_URL . $booking['lapangan_foto'] : 'https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=200';
                                        $tanggal = date('l, d F Y', strtotime($booking['tanggal']));
                                    ?>
                                    <div class="history-item" data-status="<?php echo $booking['status']; ?>">
                                        <div class="history-image">
                                            <img src="<?php echo $foto; ?>" alt="<?php echo $booking['lapangan_name']; ?>">
                                        </div>
                                        <div class="history-info">
                                            <div class="history-header">
                                                <h3><?php echo htmlspecialchars($booking['lapangan_name']); ?></h3>
                                                <span class="badge badge-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </div>
                                            <div class="history-details">
                                                <div class="detail-item">
                                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span><?php echo $tanggal; ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span><?php echo date('H:i', strtotime($booking['jam_mulai'])); ?> - <?php echo date('H:i', strtotime($booking['jam_selesai'])); ?> (<?php echo ceil((strtotime($booking['jam_selesai']) - strtotime($booking['jam_mulai'])) / 3600); ?> Jam)</span>
                                                </div>
                                                <div class="detail-item">
                                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    <span>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="history-actions">
                                            <?php if ($booking['status'] === 'completed' || $booking['status'] === 'cancelled'): ?>
                                            <button class="btn-action btn-primary" onclick="bookingLagi(<?php echo $booking['lapangan_id']; ?>)">Booking Lagi</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="padding: 40px; text-align: center; color: #999;">
                                        <p>Belum ada riwayat penyewaan</p>
                                    </div>
                                <?php endif; ?>
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
    <script>
        function bookingLagi(lapanganId) {
            window.location.href = '../../page/user/detail-lapangan.php?id=' + lapanganId;
        }
    </script>
</body>

</html>