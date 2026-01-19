<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/db.php';

// Get lapangan_id dari URL
$lapangan_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Query lapangan data
try {
    $stmt = $pdo->prepare("SELECT * FROM lapangan WHERE id = ?");
    $stmt->execute([$lapangan_id]);
    $lapangan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lapangan) {
        die("Lapangan tidak ditemukan");
    }

    // Get fasilitas
    $stmt = $pdo->prepare(
        "SELECT f.nama FROM fasilitas f 
         INNER JOIN lapangan_fasilitas lf ON f.id = lf.fasilitas_id 
         WHERE lf.lapangan_id = ?"
    );
    $stmt->execute([$lapangan_id]);
    $fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get ratings/ulasan
    $stmt = $pdo->prepare(
        "SELECT r.id, r.rating, r.review, r.created_at, r.user_id, u.name 
         FROM ratings r
         INNER JOIN users u ON r.user_id = u.id
         WHERE r.lapangan_id = ?
         ORDER BY r.created_at DESC
         LIMIT 5"
    );
    $stmt->execute([$lapangan_id]);
    $ulasan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?php echo htmlspecialchars($lapangan['nama']); ?> - SportField</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/detail-lapangan.css">
    <link rel="stylesheet" href="../../assets/css/navbar-footer.css">
</head>
<body>
    <input type="hidden" id="lapanganId" value="<?php echo $lapangan_id; ?>">
    <input type="hidden" id="lapanganNama" value="<?php echo htmlspecialchars($lapangan['nama']); ?>">
    <input type="hidden" id="hargaPerJam" value="<?php echo $lapangan['harga_per_jam']; ?>">

    <?php include '../includes/navbar.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="../../index.php">Beranda</a>
                <span>/</span>
                <a href="lapangan.php">Lapangan</a>
                <span>/</span>
                <span class="active"><?php echo htmlspecialchars($lapangan['nama']); ?></span>
            </div>

            <div class="content-grid">
                <div class="left-column">
                    <div class="gallery-section">
                        <div class="main-image">
                            <?php
                            $gambar_src = '';
                            if (!empty($lapangan['gambar'])) {
                                $gambar_src = '../../' . htmlspecialchars($lapangan['gambar']);
                            } else {
                                // Placeholder - base64 encoded gray image
                                $gambar_src = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22800%22 height=%22500%22%3E%3Crect fill=%22%23e0e0e0%22 width=%22800%22 height=%22500%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2224%22 fill=%22%23999%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EGambar Tidak Tersedia%3C/text%3E%3C/svg%3E';
                            }
                            ?>
                            <img src="<?php echo $gambar_src; ?>" alt="<?php echo htmlspecialchars($lapangan['nama']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 24px;">
                            <div class="badge-indoor"><?php echo htmlspecialchars($lapangan['jenis']); ?></div>
                            <div class="image-overlay"></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h1 class="field-title"><?php echo htmlspecialchars($lapangan['nama']); ?></h1>
                                <div class="field-meta">
                                    <div class="rating">
                                        <svg class="star" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <span class="rating-number"><?php echo number_format($lapangan['average_rating'], 1); ?></span>
                                        <span class="review-count">(<?php echo $lapangan['total_rating']; ?> ulasan)</span>
                                    </div>
                                    <span>•</span>
                                    <span>📍 Bandung, Indonesia</span>
                                </div>
                            </div>
                            <div class="price-section">
                                <div class="price-label">Mulai dari</div>
                                <div class="price-amount">Rp <?php echo number_format($lapangan['harga_per_jam'], 0, ',', '.'); ?></div>
                                <div class="price-unit">per jam</div>
                            </div>
                        </div>

                        <div class="card-content">
                            <h2 class="section-title">Tentang Lapangan</h2>
                            <p class="description">
                                <?php echo nl2br(htmlspecialchars($lapangan['deskripsi'] ?? 'Lapangan olahraga berkualitas tinggi dengan fasilitas lengkap')); ?>
                            </p>

                            <h3 class="subsection-title">Fasilitas Tersedia</h3>
                            <div class="facilities-grid">
                                <?php
                                foreach ($fasilitas as $f) {
                                    echo '
                                    <div class="facility-item">
                                        <div class="facility-label">' . htmlspecialchars($f['nama']) . '</div>
                                    </div>
                                    ';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h2 class="section-title" style="margin: 0;">Ulasan Pelanggan</h2>
                            <button onclick="openRatingModal()" class="btn-small" style="padding: 8px 16px; font-size: 14px;">+ Beri Rating</button>
                        </div>
                        <div class="reviews">
                            <?php if (count($ulasan_list) > 0): ?>
                                <?php foreach ($ulasan_list as $ulasan): ?>
                                <div class="review-item">
                                    <div class="review-avatar">
                                        <?php 
                                        $nama = htmlspecialchars($ulasan['name']);
                                        $initials = strtoupper(substr($nama, 0, 1)) . strtoupper(substr(strrchr($nama, ' '), 1, 1));
                                        echo $initials;
                                        ?>
                                    </div>
                                    <div class="review-content">
                                        <div class="review-header">
                                            <div>
                                                <div class="reviewer-name"><?php echo $nama; ?></div>
                                                <div class="review-stars">
                                                    <?php 
                                                    $rating = (int)$ulasan['rating'];
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        $filled = $i <= $rating ? 'filled' : '';
                                                        echo '<svg class="star ' . $filled . '" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="review-date">
                                                    <?php 
                                                    $date = new DateTime($ulasan['created_at']);
                                                    $now = new DateTime();
                                                    $diff = $now->diff($date);
                                                    
                                                    if ($diff->days == 0) {
                                                        echo 'Hari ini';
                                                    } elseif ($diff->days == 1) {
                                                        echo 'Kemarin';
                                                    } elseif ($diff->days < 7) {
                                                        echo $diff->days . ' hari lalu';
                                                    } elseif ($diff->days < 30) {
                                                        echo ceil($diff->days / 7) . ' minggu lalu';
                                                    } else {
                                                        echo ceil($diff->days / 30) . ' bulan lalu';
                                                    }
                                                    ?>
                                                </div>
                                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ulasan['user_id']): ?>
                                                <div class="review-menu">
                                                    <button onclick="toggleReviewMenu(this)" class="btn-menu" title="Menu">
                                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10.5 1.5H9.5V3.5H10.5V1.5ZM10.5 8.5H9.5V10.5H10.5V8.5ZM10.5 15.5H9.5V17.5H10.5V15.5Z"></path>
                                                        </svg>
                                                    </button>
                                                    <div class="menu-dropdown">
                                                        <button onclick="editRating(<?php echo $ulasan['id']; ?>, <?php echo $ulasan['rating']; ?>, '<?php echo htmlspecialchars(addslashes($ulasan['review'])); ?>')" class="menu-item">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button onclick="deleteRating(<?php echo $ulasan['id']; ?>)" class="menu-item delete">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($ulasan['review'])): ?>
                                        <p class="review-text"><?php echo htmlspecialchars($ulasan['review']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align: center; color: #999; padding: 20px;">Belum ada ulasan untuk lapangan ini. Jadilah yang pertama memberikan ulasan!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="right-column">
                    <div class="booking-card">
                        <h2 class="booking-title">Booking Sekarang</h2>
                        
                        <div class="booking-form">
                            <div class="form-group">
                                <label>Pilih Tanggal</label>
                                <input type="date" id="bookingDate" onchange="updateAvailableHours()">
                                <small id="dateError" style="color: red; display: none;"></small>
                            </div>

                            <div class="form-group">
                                <label>Pilih Jam Mulai</label>
                                <select id="startTime" onchange="updatePrice()">
                                    <option value="">-- Pilih Jam --</option>
                                    <option value="06:00">06:00</option>
                                    <option value="07:00">07:00</option>
                                    <option value="08:00">08:00</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                    <option value="21:00">21:00</option>
                                    <option value="22:00">22:00</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Pilih Jam Selesai</label>
                                <select id="endTime" onchange="updatePrice()">
                                    <option value="">-- Pilih Jam --</option>
                                    <option value="07:00">07:00</option>
                                    <option value="08:00">08:00</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                    <option value="21:00">21:00</option>
                                    <option value="22:00">22:00</option>
                                    <option value="23:00">23:00</option>
                                </select>
                            </div>
                            
                            <div class="total-section">
                                <div class="total-row">
                                    <span class="total-label">Total Harga</span>
                                    <span id="totalPrice" class="total-price">Rp 0</span>
                                </div>
                                <button onclick="openPaymentModal()" class="btn-payment">Lanjut ke Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Pembayaran -->
    <div id="paymentModal" class="modal">
        <div class="modal-backdrop"></div>
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-header">
                    <h2>Konfirmasi Pembayaran</h2>
                    <button onclick="closePaymentModal()" class="btn-close">
                        <svg class="icon-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="booking-summary">
                        <div class="summary-row">
                            <span class="summary-label">Lapangan</span>
                            <span class="summary-value" id="modalLapangan">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Tanggal</span>
                            <span id="modalDate" class="summary-value">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Jam</span>
                            <span id="modalTime" class="summary-value">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Durasi</span>
                            <span id="modalDuration" class="summary-value">-</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span id="modalTotal" class="modal-total-price">-</span>
                        </div>
                    </div>

                    <div class="payment-methods">
                        <label class="method-label">Metode Pembayaran</label>
                        <div class="method-options">
                            <div class="method-option active">
                                <input type="radio" id="transfer" name="payment" value="transfer" checked onchange="showPaymentDetails()">
                                <label for="transfer">Transfer Bank</label>
                            </div>
                            <div class="method-option">
                                <input type="radio" id="qris" name="payment" value="qris" onchange="showPaymentDetails()">
                                <label for="qris">QRIS</label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details Section -->
                    <div id="paymentDetails" class="payment-details">
                        <!-- Transfer Bank Details -->
                        <div id="transferDetails" class="payment-detail-box">
                            <h4>Rincian Transfer Bank</h4>
                            <div class="detail-item">
                                <span class="detail-label">Bank</span>
                                <span class="detail-value">BCA</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Nomor Rekening</span>
                                <span class="detail-value" style="font-weight: bold; font-size: 16px;">1234567890</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Atas Nama</span>
                                <span class="detail-value">SportField Indonesia</span>
                            </div>
                        </div>

                        <!-- QRIS Details -->
                        <div id="qrisDetails" class="payment-detail-box" style="display: none;">
                            <h4>Scan QRIS</h4>
                            <div class="qris-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=00020126360014com.midtrans.www51590010A000000000111301234560215A000000000111301234566004000011630400015F34038B8B50520400005303360540101215000705020304061406202412311634073000002120563074646000016D0117000024150014ID.CO.MIDTRANS20100217000101000000000000000034076850014br.gov.bcb.dict000161000161000161000161000150014br.gov.bcb.brcode010121134680016ID.CO.MIDTRANS01051.0520400005303360540101215000705020304061406202412315F34038B8B5052040000530336054010121500070502030406146302C4310Cc3000002120563074646000016D0117000024150014ID.CO.MIDTRANS20100217000101000000000000000034076850014br.gov.bcb.dict000161000161000161000161000150014br.gov.bcb.brcode010121134680016ID.CO.MIDTRANS01051.0520400005303360540101215000705020304061406202412315F34038B8B5052040000530336054010121500070502030406146D8E63047D5D" alt="QRIS Code" style="width: 200px; height: 200px;">
                            </div>
                            <p style="text-align: center; color: #666; margin-top: 10px;">Scan dengan aplikasi pembayaran mobile Anda</p>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-content">
                            <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong>Penting!</strong>
                                <p>Setelah pembayaran berhasil, segera upload bukti pembayaran untuk mengkonfirmasi pesanan Anda.</p>
                            </div>
                        </div>
                    </div>

                    <div class="upload-section">
                        <label class="upload-label">Upload Bukti Pembayaran</label>
                        <div class="upload-area" onclick="document.getElementById('paymentProof').click()">
                            <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <p class="upload-text">Klik untuk upload foto bukti</p>
                            <p class="upload-hint">(PNG, JPG, max 5MB)</p>
                            <input type="file" id="paymentProof" accept="image/*" onchange="handleFileUpload(event)">
                        </div>
                        <div id="uploadedFile" class="uploaded-file">
                            <div class="file-info">
                                <svg class="file-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 10H17a1 1 0 001-1v-3a1 1 0 00-1-1h-3z"></path>
                                </svg>
                                <span id="fileName">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button onclick="closePaymentModal()" class="btn-cancel">Batal</button>
                    <button onclick="submitPayment()" class="btn-confirm">Lanjut Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rating -->
    <div id="ratingModal" class="modal">
        <div class="modal-backdrop"></div>
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-header">
                    <h2>Berikan Rating & Ulasan</h2>
                    <button onclick="closeRatingModal()" class="btn-close">
                        <svg class="icon-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="rating-form">
                        <label class="form-label">Rating</label>
                        <div class="star-rating" id="starRating">
                            <svg class="star-select" data-rating="1" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <svg class="star-select" data-rating="2" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <svg class="star-select" data-rating="3" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <svg class="star-select" data-rating="4" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <svg class="star-select" data-rating="5" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        </div>
                        <input type="hidden" id="selectedRating" value="0">

                        <label class="form-label" style="margin-top: 20px;">Ulasan (Opsional)</label>
                        <textarea id="reviewText" class="review-textarea" placeholder="Bagikan pengalaman Anda dengan lapangan ini..." rows="4"></textarea>

                        <input type="hidden" id="ratingLapanganId">
                    </div>
                </div>

                <div class="modal-footer">
                    <button onclick="closeRatingModal()" class="btn-cancel">Batal</button>
                    <button onclick="submitRating()" class="btn-confirm">Kirim Rating</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/detail-lapangan.js"></script>
</body>
</html>
