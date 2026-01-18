<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lapangan - SportField</title>
    <link rel="stylesheet" href="/assets/css/detail-lapangan.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="nav-logo">
                    <div class="logo-icon">
                        <span>SF</span>
                    </div>
                    <h1 class="logo-text">SportField</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index2.html">Beranda</a></li>
                    <li><a href="lapangan.html">Lapangan</a></li>
                    <li><a href="#">Kontak</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="login.html" class="btn-login">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="breadcrumb">
                <a href="#">Beranda</a>
                <span>/</span>
                <a href="#">Lapangan</a>
                <span>/</span>
                <span class="active">Futsal A</span>
            </div>

            <div class="content-grid">
                <div class="left-column">
                    <div class="gallery-section">
                        <div id="mainImage" class="main-image" style="background-image: url('https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=2070&auto=format&fit=crop');">
                            <div class="badge-indoor">Indoor</div>
                            <div class="image-overlay"></div>
                        </div>
                        <div class="thumbnail-grid">
                            <div class="thumbnail active" style="background-image: url('https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=2070&auto=format&fit=crop');" onclick="changeMainImage(this)"></div>
                            <div class="thumbnail" style="background-image: url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=2070&auto=format&fit=crop');" onclick="changeMainImage(this)"></div>
                            <div class="thumbnail" style="background-image: url('https://images.unsplash.com/photo-1553778263-73a83bab9b0c?q=80&w=2071&auto=format&fit=crop');" onclick="changeMainImage(this)"></div>
                            <div class="thumbnail" style="background-image: url('https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=2070&auto=format&fit=crop');" onclick="changeMainImage(this)"></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h1 class="field-title">Lapangan Futsal A</h1>
                                <div class="field-meta">
                                    <div class="rating">
                                        <svg class="star" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <span class="rating-number">4.8</span>
                                        <span class="review-count">(124 ulasan)</span>
                                    </div>
                                    <span>•</span>
                                    <span>📍 Bandung, Indonesia</span>
                                </div>
                            </div>
                            <div class="price-section">
                                <div class="price-label">Mulai dari</div>
                                <div class="price-amount">150K</div>
                                <div class="price-unit">per jam</div>
                            </div>
                        </div>

                        <div class="card-content">
                            <h2 class="section-title">Tentang Lapangan</h2>
                            <p class="description">
                                Lapangan futsal indoor dengan rumput sintetis premium berkualitas internasional. Dilengkapi dengan sistem pencahayaan LED yang sempurna untuk bermain kapan saja. Fasilitas modern dengan ruang ganti yang nyaman dan area penonton yang luas.
                            </p>

                            <h3 class="subsection-title">Spesifikasi</h3>
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <div class="spec-icon">📏</div>
                                    <div>
                                        <div class="spec-label">Ukuran</div>
                                        <div class="spec-value">40m x 20m</div>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">👥</div>
                                    <div>
                                        <div class="spec-label">Kapasitas</div>
                                        <div class="spec-value">10-12 pemain</div>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">🌱</div>
                                    <div>
                                        <div class="spec-label">Permukaan</div>
                                        <div class="spec-value">Rumput Sintetis</div>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">💡</div>
                                    <div>
                                        <div class="spec-label">Pencahayaan</div>
                                        <div class="spec-value">LED Premium</div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="subsection-title">Fasilitas Tersedia</h3>
                            <div class="facilities-grid">
                                <div class="facility-item">
                                    <span class="facility-icon">🚿</span>
                                    <span class="facility-label">Shower</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">👕</span>
                                    <span class="facility-label">Loker</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">🅿️</span>
                                    <span class="facility-label">Parkir</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">🏪</span>
                                    <span class="facility-label">Kantin</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">❄️</span>
                                    <span class="facility-label">AC</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">📶</span>
                                    <span class="facility-label">WiFi</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">🎥</span>
                                    <span class="facility-label">CCTV</span>
                                </div>
                                <div class="facility-item">
                                    <span class="facility-icon">🏥</span>
                                    <span class="facility-label">P3K</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h2 class="section-title">Ulasan Pelanggan</h2>
                        <div class="reviews">
                            <div class="review-item">
                                <div class="review-avatar">AH</div>
                                <div class="review-content">
                                    <div class="review-header">
                                        <div>
                                            <div class="reviewer-name">Ahmad Hidayat</div>
                                            <div class="review-date">2 hari yang lalu</div>
                                        </div>
                                        <div class="review-stars">
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        </div>
                                    </div>
                                    <p class="review-text">Lapangan sangat bagus dan terawat. Rumput sintetisnya empuk dan nyaman. Fasilitasnya lengkap, ruang gantinya bersih. Pasti akan booking lagi!</p>
                                </div>
                            </div>

                            <div class="review-item">
                                <div class="review-avatar">RP</div>
                                <div class="review-content">
                                    <div class="review-header">
                                        <div>
                                            <div class="reviewer-name">Rizki Pratama</div>
                                            <div class="review-date">1 minggu yang lalu</div>
                                        </div>
                                        <div class="review-stars">
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star filled" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <svg class="star" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        </div>
                                    </div>
                                    <p class="review-text">Lokasi strategis dan mudah dijangkau. Harga sesuai dengan kualitas yang diberikan. Recommended!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="right-column">
                    <div class="booking-card">
                        <h2 class="booking-title">Booking Sekarang</h2>
                        
                        <div class="booking-form">
                            <div class="form-group">
                                <label>Pilih Tanggal</label>
                                <input type="date" id="bookingDate" onchange="updatePrice()">
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
                                    <option value="11:00" disabled>11:00 (Sudah Dibooking)</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00" disabled>14:00 (Sudah Dibooking)</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00" disabled>20:00 (Sudah Dibooking)</option>
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
                                    <option value="12:00" disabled>12:00 (Sudah Dibooking)</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00" disabled>15:00 (Sudah Dibooking)</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                    <option value="21:00" disabled>21:00 (Sudah Dibooking)</option>
                                    <option value="22:00">22:00</option>
                                    <option value="23:00">23:00</option>
                                </select>
                            </div>
                            
                            <div class="total-section">
                                <div class="total-row">
                                    <span class="total-label">Total Harga</span>
                                    <span id="totalPrice" class="total-price">150K</span>
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
                            <span class="summary-value">Futsal A</span>
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
                                <input type="radio" id="bca" name="payment" value="bca" checked>
                                <label for="bca">Transfer Bank BCA</label>
                            </div>
                            <div class="method-option">
                                <input type="radio" id="mandiri" name="payment" value="mandiri">
                                <label for="mandiri">Transfer Bank Mandiri</label>
                            </div>
                            <div class="method-option">
                                <input type="radio" id="gopay" name="payment" value="gopay">
                                <label for="gopay">GoPay</label>
                            </div>
                            <div class="method-option">
                                <input type="radio" id="ovo" name="payment" value="ovo">
                                <label for="ovo">OVO</label>
                            </div>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-content">
                            <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="info-title">Catatan Penting</p>
                                <p class="info-text">Pembayaran harus selesai dalam 30 menit agar booking dapat dikonfirmasi.</p>
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
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span id="fileName"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button onclick="closePaymentModal()" class="btn-cancel">Batal</button>
                    <button class="btn-confirm">Lanjut Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/detail-lapangan.js"></script>
</body>
</html>