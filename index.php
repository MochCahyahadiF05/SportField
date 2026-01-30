<?php
require_once 'config/db.php';

// Query lapangan dengan rating tertinggi, status tersedia, max 3 + JOIN dengan jenis_olahraga untuk nama
$query = "SELECT l.*, j.nama as jenis_nama
          FROM lapangan l 
          LEFT JOIN jenis_olahraga j ON l.jenis = j.id
          WHERE l.status = 'tersedia' 
          ORDER BY l.average_rating DESC, l.total_rating DESC 
          LIMIT 3";
$stmt = $pdo->query($query);
$lapangan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportField - Sewa Lapangan Olahraga</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/SportFields.png">
    <link rel="shortcut icon" href="assets/img/SportFields.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar-footer.css">
    <script src="https://kit.fontawesome.com/80f227685e.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navigation -->
    <?php include 'page/includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="hero-grid">
                <!-- Left Content -->
                <div class="hero-content animate-fade-in">
                    <div class="badge">
                        <span>🏆 Platform #1 Penyewaan Lapangan</span>
                    </div>
                    <h1 class="hero-title">
                        <span class="gradient-text">Sewa Lapangan</span>
                        <br>
                        <span>Jadi Lebih Mudah</span>
                    </h1>
                    <p class="hero-description">
                        Booking lapangan olahraga favorit Anda hanya dalam hitungan detik. Fasilitas premium, harga terjangkau, dan pengalaman terbaik menanti Anda.
                    </p>
                    <div class="hero-buttons">
                        <a href="#lapangan" class="btn-primary">
                            <span>Mulai Booking</span>
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="page/user/lapangan.php" class="btn-secondary" style="text-decoration: none;">Lihat Lapangan</a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Pelanggan</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">12</div>
                            <div class="stat-label">Lapangan</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">4.9</div>
                            <div class="stat-label">Rating</div>
                        </div>
                    </div>
                </div>

                <!-- Right Image Slider -->
                <div class="slider-container">
                    <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=2070&auto=format&fit=crop');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Futsal Premium</h3>
                            <p>Rumput sintetis terbaik</p>
                        </div>
                    </div>
                    
                    <div class="slide" style="background-image: url('https://www.shutterstock.com/image-illustration/aerial-view-green-badminton-court-600nw-2577679075.jpg');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Badminton Indoor</h3>
                            <p>AC & Pencahayaan Premium</p>
                        </div>
                    </div>
                    
                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?q=80&w=2070&auto=format&fit=crop');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Voli Outdoor</h3>
                            <p>Pasir Berkualitas Tinggi</p>
                        </div>
                    </div>

                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?q=80&w=2070&auto=format&fit=crop');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Tenis Outdoor</h3>
                            <p>Standar nasional & nyaman</p>
                        </div>
                    </div>

                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1709587824751-dd30420f5cf3?q=80&w=1331&auto=format&fit=crop');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Padel Court</h3>
                            <p>Tren olahraga modern</p>
                        </div>
                    </div>

                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1519861531473-9200262188bf?q=80&w=2070&auto=format&fit=crop');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Basketball Court</h3>
                            <p>Sangat Nyaman Dan Menyenangkan</p>
                        </div>
                    </div>

                    <div class="slide" style="background-image: url('https://gelora-public-storage.s3-ap-southeast-1.amazonaws.com/upload/public-20240116041200.jpg');">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <h3>Mini Soccer</h3>
                            <p>Lapangan luas & nyaman</p>
                        </div>
                    </div>

                    <div class="slider-dots">
                        <button class="dot active" onclick="goToSlide(0)"></button>
                        <button class="dot" onclick="goToSlide(1)"></button>
                        <button class="dot" onclick="goToSlide(2)"></button>
                        <button class="dot" onclick="goToSlide(3)"></button>
                        <button class="dot" onclick="goToSlide(4)"></button>
                        <button class="dot" onclick="goToSlide(5)"></button>
                        <button class="dot" onclick="goToSlide(6)"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Lapangan Section -->
    <section id="lapangan" class="lapangan-section">
        <div class="container">
            <div class="section-header">
                <h2>Pilihan <span class="gradient-text">Lapangan</span></h2>
                <p>Berbagai pilihan lapangan dengan fasilitas terbaik</p>
            </div>

            <div class="cards-grid">
                <?php if (empty($lapangan_list)): ?>
                    <div style="text-align: center; padding: 40px; grid-column: 1/-1;">
                        <p>Belum ada lapangan tersedia</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($lapangan_list as $lap): ?>
                        <div class="field-card">
                            <div class="card-image">
                                <?php 
                                $image_path = !empty($lap['gambar']) ? 'assets/img/lapangan/' . basename($lap['gambar']) : '';
                                if ($image_path && file_exists($image_path)): 
                                ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($lap['nama']); ?>">
                                <?php else: ?>
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='500' height='300'%3E%3Crect width='500' height='300' fill='%23e5e7eb'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%239ca3af' font-size='18' font-family='sans-serif'%3EGambar Tidak Tersedia%3C/text%3E%3C/svg%3E" alt="Tidak ada gambar">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="card-header">
                                    <h3><?php echo htmlspecialchars($lap['nama']); ?></h3>
                                    <span class="badge-type"><?php echo htmlspecialchars($lap['jenis_nama'] ?? 'Olahraga'); ?></span>
                                </div>
                                <p class="card-description"><?php echo htmlspecialchars(substr($lap['deskripsi'], 0, 60)); ?>...</p>
                                <div class="card-footer">
                                    <div class="price">
                                        <span class="price-amount"><?php echo number_format($lap['harga_per_jam'] / 1000, 0); ?>K</span>
                                        <span class="price-unit">/jam</span>
                                    </div>
                                    <div class="rating">
                                        <svg class="star-icon" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <span><?php echo number_format($lap['average_rating'], 1); ?></span>
                                    </div>
                                </div>
                                <a href="page/user/detail-lapangan.php?id=<?php echo $lap['id']; ?>" class="btn-booking">Booking Sekarang</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimoni Section -->
    <section id="testimoni" class="testimoni-section">
        <div class="container">
            <div class="section-header">
                <h2>Testimoni <span class="gradient-text">Pelanggan</span></h2>
                <p>Kepuasan pelanggan adalah kebanggaan kami</p>
            </div>

            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="star-rating">
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                    </div>
                    <p class="testimonial-text">"Pelayanan sangat baik dan lapangan dalam kondisi prima. Sangat puas dengan pengalaman booking melalui aplikasi mereka."</p>
                    <div class="testimonial-author">
                        <h4>Budi Santoso</h4>
                        <p>Futsal Enthusiast</p>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="star-rating">
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                    </div>
                    <p class="testimonial-text">"Harga terjangkau dengan kualitas lapangan yang excellent. Akan recommend ke teman-teman saya."</p>
                    <div class="testimonial-author">
                        <h4>Siti Nurhaliza</h4>
                        <p>Badminton Player</p>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="star-rating">
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                    </div>
                    <p class="testimonial-text">"Fasilitas lengkap dan lokasi strategis. Customer service responsif dan membantu. Sangat merekomendasikan!"</p>
                    <div class="testimonial-author">
                        <h4>Ahmad Wijaya</h4>
                        <p>Team Captain</p>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="star-rating">
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                        <span class="star">⭐</span>
                    </div>
                    <p class="testimonial-text">"Best booking experience! Interface yang user-friendly dan proses yang cepat. Highly recommended untuk semua."</p>
                    <div class="testimonial-author">
                        <h4>Rini Putri</h4>
                        <p>Regular Customer</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="contact-section">
        <div class="container-small">
            <div class="section-header">
                <h2>Hubungi <span class="gradient-text">Kami</span></h2>
                <p>Ada pertanyaan? Kami siap membantu Anda</p>
            </div>

            <form class="contact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="john@example.com">
                    </div>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea rows="5" placeholder="Tulis pesan Anda disini..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Kirim Pesan</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    
    <?php include 'page/includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>