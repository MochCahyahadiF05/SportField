<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SportField</title>
    <link rel="stylesheet" href="../../assets/css/navbar-footer.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F0FDF4;
            color: #064E3B;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .main-content {
            padding-top: 120px;
            min-height: 100vh;
        }
        
        .dashboard-header {
            margin-bottom: 2rem;
        }
        
        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #16A34A;
        }
        
        .dashboard-header p {
            color: #6b7280;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #16A34A;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #064E3B;
        }
        
        .card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            background: #16A34A;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #15803d;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }
    </style>
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

    // Check if user is ADMIN - jika bukan admin, redirect ke home
    if (!Auth::isAdmin()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }

    $currentUser = Auth::getUser();
    ?>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($currentUser['name']); ?>! Kelola platform SportField dari sini.</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="stat-value">125</div>
                </div>
                <div class="stat-card">
                    <h3>Total Lapangan</h3>
                    <div class="stat-value">12</div>
                </div>
                <div class="stat-card">
                    <h3>Total Booking</h3>
                    <div class="stat-value">456</div>
                </div>
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <div class="stat-value">Rp 4.5M</div>
                </div>
            </div>

            <!-- Management Sections -->
            <div class="card">
                <h2 class="section-title">Manajemen Pengguna</h2>
                <p>Kelola data pengguna, role, dan akses sistem.</p>
                <button class="btn">Kelola Pengguna</button>
            </div>

            <div class="card">
                <h2 class="section-title">Manajemen Lapangan</h2>
                <p>Tambah, edit, atau hapus lapangan olahraga dari sistem.</p>
                <button class="btn">Kelola Lapangan</button>
            </div>

            <div class="card">
                <h2 class="section-title">Manajemen Booking</h2>
                <p>Lihat dan kelola semua booking dari pengguna.</p>
                <button class="btn">Kelola Booking</button>
            </div>

            <div class="card">
                <h2 class="section-title">Laporan & Analytics</h2>
                <p>Lihat laporan pendapatan, statistik pengguna, dan analytics lainnya.</p>
                <button class="btn">Lihat Laporan</button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>