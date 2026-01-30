<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'page/auth/login.php');
    exit();
}

// Check if user is ADMIN
if (!Auth::isAdmin()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$currentUser = Auth::getUser();

// Set page-specific variables
$page_title = "Manajemen Lapangan";
$page_subtitle = "Kelola semua lapangan olahraga";

// Query database for lapangan
require_once '../../config/db.php';
$lapangan_list = [];
$fasilitas_list = [];
$jenis_list = [];

try {
    // Get all lapangan with their facilities and jenis names
    $stmt = $pdo->query("
        SELECT l.*, j.nama as jenis_nama 
        FROM lapangan l 
        LEFT JOIN jenis_olahraga j ON l.jenis = j.id 
        ORDER BY l.id DESC
    ");
    $lapangan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($lapangan_list) . " lapangan records");
    
    // Debug: Log each ID
    foreach ($lapangan_list as $l) {
        error_log("  - ID: {$l['id']}, Nama: {$l['nama']}, Jenis: {$l['jenis']} ({$l['jenis_nama']})");
    }
    
    // Get facilities for each lapangan
    foreach ($lapangan_list as $key => $lapangan) {
        $fac_stmt = $pdo->prepare("
            SELECT f.* FROM fasilitas f
            JOIN lapangan_fasilitas lf ON f.id = lf.fasilitas_id
            WHERE lf.lapangan_id = ?
        ");
        $fac_stmt->execute([$lapangan['id']]);
        $lapangan_list[$key]['fasilitas'] = $fac_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get all fasilitas for forms
    $fac_query = $pdo->query("SELECT * FROM fasilitas ORDER BY id ASC");
    $fasilitas_list = $fac_query->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($fasilitas_list) . " fasilitas records");
    
    // Get all jenis olahraga for forms
    $jenis_query = $pdo->query("SELECT * FROM jenis_olahraga ORDER BY nama ASC");
    $jenis_list = $jenis_query->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($jenis_list) . " jenis olahraga records");
    
    // Get statistics for stats cards
    $total_lapangan = count($lapangan_list);
    
    // Lapangan aktif (status = 'tersedia')
    $aktif_stmt = $pdo->query("SELECT COUNT(*) as total FROM lapangan WHERE status = 'tersedia'");
    $lapangan_aktif = $aktif_stmt->fetch()['total'] ?? 0;
    
    // Lapangan dalam perbaikan (status = 'perbaikan')
    $perbaikan_stmt = $pdo->query("SELECT COUNT(*) as total FROM lapangan WHERE status = 'perbaikan'");
    $lapangan_perbaikan = $perbaikan_stmt->fetch()['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error loading lapangan: " . $e->getMessage());
    error_log("Error code: " . $e->getCode());
    $total_lapangan = 0;
    $lapangan_aktif = 0;
    $lapangan_perbaikan = 0;
}

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div>
                <p class="stat-label">Total Lapangan</p>
                <p class="stat-value"><?php echo $total_lapangan; ?></p>
                <p class="stat-info success">+2 bulan ini</p>
            </div>
            <div class="stat-icon blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div>
                <p class="stat-label">Lapangan Aktif</p>
                <p class="stat-value green"><?php echo $lapangan_aktif; ?></p>
                <p class="stat-info">Siap digunakan</p>
            </div>
            <div class="stat-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div>
                <p class="stat-label">Dalam Perbaikan</p>
                <p class="stat-value orange"><?php echo $lapangan_perbaikan; ?></p>
                <p class="stat-info">Estimasi 2 hari</p>
            </div>
            <div class="stat-icon orange">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-grid">
        <div class="filter-item">
            <label>Jenis Olahraga</label>
            <select id="filterJenis" onchange="applyFilter()">
                <option value="">Semua Jenis</option>
                <?php foreach ($jenis_list as $jenis): ?>
                <option value="<?php echo htmlspecialchars($jenis['id']); ?>">
                    <?php echo htmlspecialchars($jenis['nama']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
       <div class="filter-item">
            <label>Status</label>
            <select id="filterStatus" onchange="applyFilter()">
                <option value="">Semua Status</option>
                <option value="tersedia">Tersedia</option>
                <option value="maintenance">Perbaikan</option>
            </select>
        </div>
        <div class="filter-item">
            <label>Fasilitas</label>
            <select id="filterFasilitas" onchange="applyFilter()">
                <option value="">Semua Fasilitas</option>
                <?php foreach ($fasilitas_list as $fasilitas): ?>
                <option value="<?php echo $fasilitas['id']; ?>">
                    <?php echo htmlspecialchars($fasilitas['nama']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-item">
            <label>Harga (per jam)</label>
            <select id="filterHarga" onchange="applyFilter()">
                <option value="">Semua Harga</option>
                <option value="0-100000">< Rp 100.000</option>
                <option value="100000-200000">Rp 100.000 - 200.000</option>
                <option value="200000">≥ Rp 200.000</option>
            </select>
        </div>
    </div>
</div>

<!-- Header -->
<div class="section-header">
    <h3>Daftar Lapangan</h3>
    <button onclick="openAddModal()" class="btn-primary">
        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Lapangan</span>
    </button>
</div>

<!-- Lapangan Grid -->
<div class="lapangan-grid">
    <?php if (empty($lapangan_list)): ?>
    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
        <p style="color: #6b7280; font-size: 16px;">Belum ada lapangan. Silahkan tambahkan lapangan baru.</p>
    </div>
    <?php else: ?>
    <?php foreach ($lapangan_list as $lapangan): ?>
    <div class="lapangan-card">
        <div class="card-image">
            <?php if (!empty($lapangan['gambar'])): ?>
                <img src="../../<?php echo htmlspecialchars($lapangan['gambar']); ?>" alt="<?php echo htmlspecialchars($lapangan['nama']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
            <?php else: ?>
                <div style="width: 100%; height: 200px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            <?php endif; ?>
            <span class="status-badge <?php echo ($lapangan['status'] === 'tersedia') ? 'active' : 'maintenance'; ?>">
                <?php echo ($lapangan['status'] === 'tersedia') ? 'Aktif' : 'Perbaikan'; ?>
            </span>
        </div>
        <div class="card-body">
            <div class="card-header">
                <div>
                    <h4><?php echo htmlspecialchars($lapangan['nama']); ?></h4>
                    <p class="card-type"><?php echo htmlspecialchars($lapangan['jenis_nama'] ?? 'Olahraga'); ?></p>
                </div>
            </div>
            <div class="card-rating">
                <svg class="star-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="rating-value"><?php echo number_format($lapangan['average_rating'] ?? 0, 1); ?></span>
                <span class="rating-count">(<?php echo $lapangan['total_rating'] ?? 0; ?> booking)</span>
            </div>
            <div class="card-price">
                <span class="price">Rp <?php echo number_format($lapangan['harga_per_jam'], 0, ',', '.'); ?></span>
                <span class="price-unit">/jam</span>
            </div>
            <div class="card-facilities">
                <?php if (!empty($lapangan['fasilitas'])): ?>
                    <?php foreach ($lapangan['fasilitas'] as $fasilitas): ?>
                    <span class="facility-tag blue"><?php echo htmlspecialchars($fasilitas['nama']); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-actions">
                <button onclick="openDetailModal(<?php echo $lapangan['id']; ?>)" class="btn-manage">Kelola</button>
                <button onclick="openDeleteModal(<?php echo $lapangan['id']; ?>)" class="btn-delete">Hapus</button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah Lapangan -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Lapangan Baru</h3>
            <button onclick="closeAddModal()" class="close-btn">&times;</button>
        </div>
        <form id="addForm" onsubmit="handleAddLapangan(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Lapangan *</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jenis Olahraga *</label>
                        <select name="jenis" required>
                            <option value="">-- Pilih Jenis Olahraga --</option>
                            <?php foreach ($jenis_list as $jenis): ?>
                            <option value="<?php echo htmlspecialchars($jenis['id']); ?>">
                                <?php echo htmlspecialchars($jenis['nama']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga per Jam (Rp) *</label>
                        <input type="number" name="harga_per_jam" min="10000" step="1000" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="tersedia">Tersedia</option>
                        <option value="maintenance">Perbaikan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <div class="checkbox-grid">
                        <?php foreach ($fasilitas_list as $fasilitas): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" name="fasilitas[]" value="<?php echo $fasilitas['id']; ?>" id="fac_<?php echo $fasilitas['id']; ?>">
                            <label for="fac_<?php echo $fasilitas['id']; ?>" style="margin: 0; margin-left: 4px;"><?php echo htmlspecialchars($fasilitas['nama']); ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Upload Foto</label>
                    <input type="file" name="gambar" id="fileUpload" class="hidden-input" accept="image/png,image/jpeg" style="display: none;">
                    <div class="upload-area" id="uploadArea" style="cursor: pointer;">
                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <p>Drag foto di sini atau klik untuk memilih</p>
                        <span>Format: PNG, JPG (Maks: 5MB)</span>
                    </div>
                    <div id="filePreview" style="margin-top: 12px; display: none;">
                        <span id="fileName" style="font-size: 14px; color: #16A34A;"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAddModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Lapangan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail/Kelola Lapangan -->
<div id="detailModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Kelola Lapangan</h3>
            <button onclick="closeDetailModal()" class="close-btn">&times;</button>
        </div>
        <form id="editForm" onsubmit="handleEditLapangan(event)">
            <div class="modal-body">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label>Nama Lapangan *</label>
                    <input type="text" name="nama" id="editNama" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jenis Olahraga *</label>
                        <select name="jenis" id="editJenis" required>
                            <option value="">-- Pilih Jenis Olahraga --</option>
                            <?php foreach ($jenis_list as $jenis): ?>
                            <option value="<?php echo htmlspecialchars($jenis['id']); ?>">
                                <?php echo htmlspecialchars($jenis['nama']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga per Jam (Rp) *</label>
                        <input type="number" name="harga_per_jam" id="editHarga" min="10000" step="1000" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editStatus">
                        <option value="tersedia">Tersedia</option>
                        <option value="maintenance">Perbaikan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <div class="checkbox-grid" id="editFasilitas">
                        <?php foreach ($fasilitas_list as $fasilitas): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" name="fasilitas[]" value="<?php echo $fasilitas['id']; ?>" id="fac_edit_<?php echo $fasilitas['id']; ?>">
                            <label for="fac_edit_<?php echo $fasilitas['id']; ?>" style="margin: 0; margin-left: 4px;"><?php echo htmlspecialchars($fasilitas['nama']); ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Foto Lapangan</label>
                    <div id="currentImagePreviewEdit" style="margin-bottom: 16px;">
                        <img id="editCurrentImage" src="" style="max-width: 100%; border-radius: 8px; max-height: 300px; object-fit: cover;">
                    </div>
                    <input type="file" name="gambar" id="fileUploadEdit" class="hidden-input" accept="image/png,image/jpeg" style="display: none;">
                    <div class="upload-area" id="uploadAreaEdit" style="display: none; cursor: pointer;">
                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <p>Drag foto di sini atau klik untuk mengubah</p>
                        <span>Format: PNG, JPG (Maks: 5MB)</span>
                    </div>
                    <div id="filePreviewEdit" style="margin-top: 12px; display: none;">
                        <span id="fileNameEdit" style="font-size: 14px; color: #16A34A;"></span>
                    </div>
                    <button type="button" onclick="changeImageEdit()" class="btn-secondary" style="margin-top: 12px;">Ubah Foto</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeDetailModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Lapangan -->
<div id="deleteModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-body">
            <div class="delete-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3>Hapus Lapangan?</h3>
            <p class="delete-message">Anda akan menghapus lapangan <strong id="deleteLapanganName">-</strong>. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="warning-box">
                <strong>Peringatan:</strong> Semua data lapangan, booking, dan riwayat pembayaran akan dihapus secara permanen.
            </div>
            <div class="delete-actions">
                <button onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
                <button onclick="confirmDelete()" class="btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Success Create/Edit/Delete -->
<div id="successModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-body" style="text-align: center;">
            <div style="width: 64px; height: 64px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; color: #16A34A;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 id="successTitle" style="font-size: 20px; font-weight: bold; color: #111827; margin-bottom: 8px;">Berhasil!</h3>
            <p id="successMessage" style="color: #6b7280; margin-bottom: 24px; font-size: 14px;">Lapangan berhasil ditambahkan</p>
            <button onclick="closeSuccessModal()" class="btn-primary" style="width: 100%;">Lanjutkan</button>
        </div>
    </div>
</div>

<!-- Modal Error -->
<div id="errorModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-body" style="text-align: center;">
            <div style="width: 64px; height: 64px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; color: #dc2626;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 id="errorTitle" style="font-size: 20px; font-weight: bold; color: #111827; margin-bottom: 8px;">Terjadi Kesalahan</h3>
            <p id="errorMessage" style="color: #6b7280; margin-bottom: 24px; font-size: 14px;">Gagal memproses permintaan Anda</p>
            <button onclick="closeErrorModal()" class="btn-secondary" style="width: 100%;">Tutup</button>
        </div>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<!-- Page-Specific CSS (loaded AFTER layout) -->
<link rel="stylesheet" href="../../assets/css/admin_style/manajemen-lapangan.css">

<script>
const lapanganData = <?php echo json_encode($lapangan_list); ?>;
const jenisData = <?php echo json_encode($jenis_list); ?>;
let deleteId = null;

// Filter Functions
function applyFilter() {
    const filterJenis = document.getElementById('filterJenis').value;
    const filterStatus = document.getElementById('filterStatus').value;
    const filterFasilitas = document.getElementById('filterFasilitas').value;
    const filterHarga = document.getElementById('filterHarga').value;

    const cards = document.querySelectorAll('.lapangan-card');
    let visibleCount = 0;

    cards.forEach((card, index) => {
        const lapangan = lapanganData[index];
        let isVisible = true;

        // Filter by Jenis Olahraga (compare as integers since jenis is now ID)
        if (filterJenis && parseInt(lapangan.jenis) !== parseInt(filterJenis)) {
            isVisible = false;
        }

        // Filter by Status
        if (filterStatus && lapangan.status !== filterStatus) {
            isVisible = false;
        }

        // Filter by Fasilitas
        if (filterFasilitas) {
            const hasFasilitas = lapangan.fasilitas.some(f => f.id == filterFasilitas);
            if (!hasFasilitas) {
                isVisible = false;
            }
        }

        // Filter by Harga
        if (filterHarga) {
            const harga = lapangan.harga_per_jam;
            if (filterHarga === '0-100000' && harga >= 100000) {
                isVisible = false;
            } else if (filterHarga === '100000-200000' && (harga < 100000 || harga > 200000)) {
                isVisible = false;
            } else if (filterHarga === '200000' && harga < 200000) {
                isVisible = false;
            }
        }

        // Show/Hide card
        if (isVisible) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show message if no results
    const lapanganGrid = document.querySelector('.lapangan-grid');
    let noResultMsg = document.getElementById('noResultMessage');
    
    if (visibleCount === 0) {
        if (!noResultMsg) {
            noResultMsg = document.createElement('div');
            noResultMsg.id = 'noResultMessage';
            noResultMsg.style.gridColumn = '1/-1';
            noResultMsg.style.textAlign = 'center';
            noResultMsg.style.padding = '40px';
            lapanganGrid.appendChild(noResultMsg);
        }
        noResultMsg.style.display = 'block';
        noResultMsg.innerHTML = '<p style="color: #6b7280; font-size: 16px;">Tidak ada lapangan yang sesuai dengan filter Anda</p>';
    } else if (noResultMsg) {
        noResultMsg.style.display = 'none';
    }
}


function setupFileUpload(fileInputId, uploadAreaId, previewId, fileNameId) {
    const fileInput = document.getElementById(fileInputId);
    const uploadArea = document.getElementById(uploadAreaId);
    const preview = document.getElementById(previewId);
    const fileName = document.getElementById(fileNameId);

    if (!uploadArea || !fileInput) return;

    uploadArea.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) handleFileSelect(file, fileInput, uploadArea, preview, fileName);
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#16A34A';
        uploadArea.style.backgroundColor = 'rgba(22, 163, 74, 0.05)';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#d1d5db';
        uploadArea.style.backgroundColor = 'transparent';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#d1d5db';
        uploadArea.style.backgroundColor = 'transparent';
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            const file = files[0];
            if (file.type.match('image.*')) {
                handleFileSelect(file, fileInput, uploadArea, preview, fileName);
            } else {
                alert('Hanya file gambar yang diperbolehkan');
            }
        }
    });
}

function handleFileSelect(file, fileInput, uploadArea, preview, fileName) {
    if (file.size > 5 * 1024 * 1024) {
        alert('File terlalu besar (max 5MB)');
        fileInput.value = '';
        return;
    }

    if (fileName) {
        fileName.textContent = '✓ ' + file.name;
        preview.style.display = 'block';
    }
    uploadArea.style.display = 'none';
}

function changeImageEdit() {
    document.getElementById('fileUploadEdit').click();
}

// Modal functions
function openAddModal() {
    console.log("openAddModal called");
    const modal = document.getElementById('addModal');
    if (modal) {
        modal.classList.add('show');
        console.log("Modal shown");
    } else {
        console.error("Modal element not found!");
    }
    document.getElementById('addForm').reset();
    setupFileUpload('fileUpload', 'uploadArea', 'filePreview', 'fileName');
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('show');
}

function openDetailModal(id) {
    console.log("openDetailModal called with id:", id);
    console.log("lapanganData:", lapanganData);
    const lapangan = lapanganData.find(l => l.id == id);
    if (!lapangan) {
        console.error("Lapangan not found for id:", id);
        return;
    }
    
    console.log("Opening detail modal for:", lapangan.nama);
    document.getElementById('editId').value = lapangan.id;
    document.getElementById('editNama').value = lapangan.nama;
    
    // Set jenis berdasarkan ID (bukan nama)
    const jenisSelect = document.getElementById('editJenis');
    setTimeout(() => {
        jenisSelect.value = lapangan.jenis;  // lapangan.jenis adalah ID sekarang
        console.log("Selected jenis ID:", lapangan.jenis);
    }, 0);
    
    document.getElementById('editHarga').value = lapangan.harga_per_jam;
    document.getElementById('editDeskripsi').value = lapangan.deskripsi || '';
    document.getElementById('editStatus').value = lapangan.status;
    
    // Set current image
    if (lapangan.gambar) {
        document.getElementById('editCurrentImage').src = '../../' + lapangan.gambar;
        document.getElementById('currentImagePreviewEdit').style.display = 'block';
    } else {
        document.getElementById('currentImagePreviewEdit').style.display = 'none';
    }
    
    // Check fasilitas
    document.querySelectorAll('input[id^="fac_edit_"]').forEach(checkbox => {
        checkbox.checked = lapangan.fasilitas.some(f => f.id == checkbox.value);
    });
    
    const detailModal = document.getElementById('detailModal');
    if (detailModal) {
        detailModal.classList.add('show');
        console.log("Detail modal shown");
    } else {
        console.error("Detail modal element not found!");
    }
    setupFileUpload('fileUploadEdit', 'uploadAreaEdit', 'filePreviewEdit', 'fileNameEdit');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('show');
}

function openDeleteModal(id) {
    console.log("openDeleteModal called with id:", id);
    const lapangan = lapanganData.find(l => l.id == id);
    if (!lapangan) {
        console.error("Lapangan not found for id:", id);
        return;
    }
    
    deleteId = id;
    document.getElementById('deleteLapanganName').textContent = lapangan.nama;
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.add('show');
        console.log("Delete modal shown");
    } else {
        console.error("Delete modal element not found!");
    }
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    deleteId = null;
}

// Handle form submissions
function handleAddLapangan(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('addForm'));
    formData.append('action', 'create');
    
    fetch('lapangan-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Lapangan Ditambahkan', 'Lapangan berhasil ditambahkan. Halaman akan di-reload dalam 2 detik.');
            closeAddModal();
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal('Gagal Menambahkan', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Terjadi Kesalahan', 'Gagal menghubungi server. Silahkan coba lagi.');
    });
}

function handleEditLapangan(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('editForm'));
    const submitId = formData.get('id');
    console.log("Submitting edit for ID:", submitId);
    console.log("Form data:", Object.fromEntries(formData));
    formData.append('action', 'update');
    
    fetch('lapangan-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Lapangan Diperbarui', 'Perubahan lapangan berhasil disimpan. Halaman akan di-reload dalam 2 detik.');
            closeDetailModal();
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal('Gagal Memperbarui', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Terjadi Kesalahan', 'Gagal menghubungi server. Silahkan coba lagi.');
    });
}

function confirmDelete() {
    if (!deleteId) return;
    
    fetch('lapangan-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=delete&id=' + deleteId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Lapangan Dihapus', 'Lapangan berhasil dihapus. Halaman akan di-reload dalam 2 detik.');
            closeDeleteModal();
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal('Gagal Menghapus', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Terjadi Kesalahan', 'Gagal menghubungi server. Silahkan coba lagi.');
    });
}

// Success/Error Modal Functions
function showSuccessModal(title, message) {
    document.getElementById('successTitle').textContent = title;
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').classList.add('show');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.remove('show');
    location.reload();
}

function showErrorModal(title, message) {
    document.getElementById('errorTitle').textContent = title;
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').classList.add('show');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.remove('show');
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    ['addModal', 'detailModal', 'deleteModal', 'successModal', 'errorModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        }
    });
});
</script>
