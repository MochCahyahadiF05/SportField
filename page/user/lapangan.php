<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/db.php';

// Get all fields
try {
    $stmt = $pdo->prepare("
        SELECT l.*, 
               GROUP_CONCAT(f.nama SEPARATOR ',') as fasilitas,
               j.nama as jenis_nama
        FROM lapangan l
        LEFT JOIN lapangan_fasilitas lf ON l.id = lf.lapangan_id
        LEFT JOIN fasilitas f ON lf.fasilitas_id = f.id
        LEFT JOIN jenis_olahraga j ON l.jenis = j.id
        WHERE l.status = 'tersedia'
        GROUP BY l.id
        ORDER BY l.nama
    ");
    $stmt->execute();
    $lapangan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $lapangan_list = [];
}

// Get all jenis olahraga for filter
try {
    $stmt = $pdo->query("SELECT id, nama FROM jenis_olahraga ORDER BY nama");
    $jenis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jenis_list = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lapangan - SportField</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/img/SportFields.png">
    <link rel="shortcut icon" href="../../assets/img/SportFields.png">
    <link rel="stylesheet" href="../../assets/css/lapangan.css">
    <link rel="stylesheet" href="../../assets/css/navbar-footer.css">
</head>
<body>
    <!-- Navigation -->
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="animate-fade-in">
                <div class="badge">
                    <span>🏟️ <?php echo count($lapangan_list); ?> Lapangan Tersedia</span>
                </div>
                <h1 class="hero-title">
                    <span class="gradient-text">Pilih Lapangan</span>
                    <br>
                    <span>Favorit Anda</span>
                </h1>
                <p class="hero-description">
                    Temukan lapangan olahraga terbaik dengan fasilitas premium dan harga terjangkau
                </p>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-container">
                <div class="filter-content">
                    <!-- Category Filter -->
                    <div class="category-filter">
                        <label>Kategori Olahraga</label>
                        <select id="categorySelect" onchange="filterCategory(this.value)" class="sort-select">
                            <option value="all">Semua Kategori</option>
                            <?php foreach ($jenis_list as $jenis): ?>
                            <option value="<?php echo strtolower(htmlspecialchars($jenis['nama'])); ?>">
                                <?php echo htmlspecialchars($jenis['nama']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Search & Sort -->
                    <div class="search-sort">
                        <div class="search-box">
                            <input type="text" id="searchInput" onkeyup="searchFields()" placeholder="Cari lapangan...">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <select onchange="sortFields(this.value)" class="sort-select">
                            <option value="default">Urutkan</option>
                            <option value="price-low">Harga: Rendah - Tinggi</option>
                            <option value="price-high">Harga: Tinggi - Rendah</option>
                            <option value="rating">Rating Tertinggi</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fields Grid -->
    <section class="fields-section">
        <div class="container">
            <div id="fieldsGrid" class="fields-grid">
                <?php foreach ($lapangan_list as $lapangan): ?>
                <div class="field-card" 
                     data-category="<?php echo strtolower(htmlspecialchars($lapangan['jenis_nama'])); ?>" 
                     data-price="<?php echo $lapangan['harga_per_jam']; ?>" 
                     data-rating="<?php echo $lapangan['average_rating']; ?>" 
                     data-name="<?php echo htmlspecialchars($lapangan['nama']); ?>">
                    <div class="card-image">
                        <?php
                        $gambar_src = '';
                        if (!empty($lapangan['gambar'])) {
                            $gambar_src = '../../' . htmlspecialchars($lapangan['gambar']);
                        } else {
                            $gambar_src = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22500%22 height=%22300%22%3E%3Crect fill=%22%23e0e0e0%22 width=%22500%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2218%22 fill=%22%23999%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EGambar Tidak Tersedia%3C/text%3E%3C/svg%3E';
                        }
                        ?>
                        <img src="<?php echo $gambar_src; ?>" alt="<?php echo htmlspecialchars($lapangan['nama']); ?>">
                    </div>
                    <div class="card-body">
                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($lapangan['nama']); ?></h3>
                            <span class="badge-type"><?php echo htmlspecialchars($lapangan['jenis_nama']); ?></span>
                        </div>
                        <p class="card-description">
                            <?php 
                            $fasilitas = explode(',', $lapangan['fasilitas']);
                            echo htmlspecialchars(implode(', ', array_slice(array_filter($fasilitas), 0, 2)));
                            ?>
                        </p>
                        <div class="card-footer">
                            <div class="price">
                                <span class="price-amount">Rp <?php echo number_format($lapangan['harga_per_jam'], 0, ',', '.'); ?></span>
                                <span class="price-unit">/jam</span>
                            </div>
                            <div class="rating">
                                <svg class="star-icon" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                <span><?php echo number_format($lapangan['average_rating'], 1); ?></span>
                            </div>
                        </div>
                        <a href="detail-lapangan.php?id=<?php echo $lapangan['id']; ?>" class="btn-booking">Booking Sekarang</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="no-results hidden">
                <div class="no-results-icon">🔍</div>
                <h3>Tidak Ada Lapangan Ditemukan</h3>
                <p>Coba ubah filter atau kata kunci pencarian Anda</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
    <script src="../../assets/js/lapangan.js"></script>
</body>
</html>
