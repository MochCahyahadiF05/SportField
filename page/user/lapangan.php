<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lapangan - SportField</title>
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
                    <span>🏟️ 12 Lapangan Tersedia</span>
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
                            <option value="futsal">Futsal</option>
                            <option value="badminton"> Badminton</option>
                            <option value="voli">Voli</option>
                            <option value="basket">Basket</option>
                            <option value="tenis">Tenis</option>
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
                <!-- Futsal Cards -->
                <div class="field-card" data-category="futsal" data-price="150000" data-rating="4.8" data-name="Futsal A">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=500&auto=format&fit=crop" alt="Futsal A">
                    </div>
                    <div class="card-body">
                        <div class="card-header">
                            <h3>Futsal A</h3>
                            <span class="badge-type">Indoor</span>
                        </div>
                        <p class="card-description">Rumput sintetis premium dengan pencahayaan LED</p>
                        <div class="card-footer">
                            <div class="price">
                                <span class="price-amount">150K</span>
                                <span class="price-unit">/jam</span>
                            </div>
                            <div class="rating">
                                <svg class="star-icon" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                <span>4.8</span>
                            </div>
                        </div>
                        <a href="detail_lapangan.html" class="btn-booking">Booking Sekarang</a>
                    </div>
                </div>

                <div class="field-card" data-category="futsal" data-price="160000" data-rating="4.9" data-name="Futsal B">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=500&auto=format&fit=crop" alt="Futsal B">
                    </div>
                    <div class="card-body">
                        <div class="card-header">
                            <h3>Futsal B</h3>
                            <span class="badge-type">Indoor</span>
                        </div>
                        <p class="card-description">Full AC dengan sound system premium</p>
                        <div class="card-footer">
                            <div class="price">
                                <span class="price-amount">160K</span>
                                <span class="price-unit">/jam</span>
                            </div>
                            <div class="rating">
                                <svg class="star-icon" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                <span>4.9</span>
                            </div>
                        </div>
                        <a href="detail_lapangan.html" class="btn-booking">Booking Sekarang</a>
                    </div>
                </div>

                <!-- Badminton Cards -->
                <div class="field-card" data-category="badminton" data-price="80000" data-rating="4.9" data-name="Badminton Court 1">
                    <div class="card-image">
                        <img src="https://www.shutterstock.com/image-illustration/aerial-view-green-badminton-court-600nw-2577679075.jpg" alt="Badminton Court 1">
                    </div>
                    <div class="card-body">
                        <div class="card-header">
                            <h3>Badminton 1</h3>
                            <span class="badge-type">Indoor</span>
                        </div>
                        <p class="card-description">Full AC dengan karpet kualitas internasional</p>
                        <div class="card-footer">
                            <div class="price">
                                <span class="price-amount">80K</span>
                                <span class="price-unit">/jam</span>
                            </div>
                            <div class="rating">
                                <svg class="star-icon" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                <span>4.9</span>
                            </div>
                        </div>
                        <a href="detail_lapangan.html" class="btn-booking">Booking Sekarang</a>
                    </div>
                </div>
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