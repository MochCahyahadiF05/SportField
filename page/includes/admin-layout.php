<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'SportField'; ?> - Admin Panel</title>
    <!-- Shared Layout Styles -->
    <link rel="stylesheet" href="../../assets/css/admin_style/admin-layout.css">
    <link rel="stylesheet" href="../../assets/css/admin_style/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/admin_style/navbar.css">
    <!-- Page-Specific Styles (loaded after shared) -->
    <link rel="stylesheet" href="../../assets/css/admin_style/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin_style/fasilitas-jenisOlahraga.css">
    <script src="https://kit.fontawesome.com/80f227685e.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-wrapper">

        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="nav-content">
                <div class="nav-left">
                    <button onclick="toggleSidebar()" class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="page-title">
                        <h2><?php echo $page_title ?? 'Dashboard'; ?></h2>
                        <p><?php echo $page_subtitle ?? 'Selamat datang!'; ?></p>
                    </div>
                </div>

                <div class="nav-right">
                    <!-- Search -->
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari...">
                    </div>

                    <!-- Notifications -->
                    <button class="icon-button">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>

                    <!-- Profile -->
                    <div class="profile-section">
                        <div class="profile-avatar">AD</div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <?php echo $page_content ?? ''; ?>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin_js/dashboard.js"></script>
    <?php echo $page_scripts ?? ''; ?>
</body>

</html>