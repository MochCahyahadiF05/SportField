// ==== DOM Variables ====
const statusText = {
    'confirmed': 'mengkonfirmasi',
    'cancelled': 'membatalkan',
    'completed': 'menyelesaikan'
};

// ==== Custom Confirm Modal ====
function showConfirmModal(title, message, onConfirm, confirmText = 'Setujui', cancelText = 'Batal') {
    const modal = document.createElement('div');
    modal.className = 'booking-modal show';
    modal.innerHTML = `
        <div class="booking-modal-content">
            <div class="booking-modal-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="booking-modal-title">${title}</h3>
            <p class="booking-modal-message">${message}</p>
            <div class="booking-modal-actions">
                <button class="booking-btn cancel-btn">${cancelText}</button>
                <button class="booking-btn confirm-btn-action">${confirmText}</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const cancelBtn = modal.querySelector('.cancel-btn');
    const confirmBtn = modal.querySelector('.confirm-btn-action');
    
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }
    
    cancelBtn.addEventListener('click', closeModal);
    confirmBtn.addEventListener('click', () => {
        closeModal();
        onConfirm();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
}

// ==== Custom Alert Modal ====
function showAlertModal(title, message, icon = 'success') {
    const modal = document.createElement('div');
    modal.className = 'booking-modal show';
    modal.innerHTML = `
        <div class="booking-modal-content">
            <div class="booking-modal-icon ${icon}">
                <i class="fas fa-${icon === 'success' ? 'check-circle' : icon === 'error' ? 'times-circle' : 'info-circle'}"></i>
            </div>
            <h3 class="booking-modal-title">${title}</h3>
            <p class="booking-modal-message">${message}</p>
            <div class="booking-modal-actions">
                <button class="booking-btn confirm-btn-action">Oke</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const confirmBtn = modal.querySelector('.confirm-btn-action');
    
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }
    
    confirmBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
}

// ==== Update booking status ====
function updateStatus(bookingId, newStatus) {
    const statusMessage = {
        'confirmed': 'mengkonfirmasi booking ini',
        'cancelled': 'membatalkan booking ini',
        'completed': 'menyelesaikan booking ini'
    };
    
    showConfirmModal(
        `${statusText[newStatus].charAt(0).toUpperCase() + statusText[newStatus].slice(1)} Booking?`,
        `Anda akan ${statusMessage[newStatus]}.`,
        () => {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('booking_id', bookingId);
            formData.append('status', newStatus);
            
            fetch('../../page/admin/booking-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlertModal('Berhasil!', 'Status booking telah diupdate.', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlertModal('Error!', data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                showAlertModal('Error!', 'Terjadi kesalahan jaringan', 'error');
            });
        },
        statusText[newStatus].charAt(0).toUpperCase() + statusText[newStatus].slice(1),
        'Batal'
    );
}

// View booking detail
function viewDetail(bookingId) {
    fetch(`../../page/admin/booking-handler.php?action=detail&booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showDetailModal(data.data);
            } else {
                showAlertModal('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            showAlertModal('Error!', 'Gagal mengambil data booking', 'error');
            console.error(error);
        });
}

// ==== Detail Modal ====
function showDetailModal(booking) {
    const modal = document.createElement('div');
    modal.className = 'booking-modal show';
    
    // Format date
    const bookingDate = new Date(booking.tanggal);
    const formattedDate = bookingDate.toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    // Format time
    const jamMulai = booking.jam_mulai.substring(0, 5);
    const jamSelesai = booking.jam_selesai.substring(0, 5);
    
    // Status colors
    const getStatusColor = (status) => {
        const colors = {
            'pending': '#fbbf24',
            'confirmed': '#10b981',
            'cancelled': '#ef4444',
            'completed': '#3b82f6'
        };
        return colors[status] || '#6b7280';
    };
    
    const getStatusText = (status) => {
        const texts = {
            'pending': 'Pending',
            'confirmed': 'Terkonfirmasi',
            'cancelled': 'Dibatalkan',
            'completed': 'Selesai'
        };
        return texts[status] || status;
    };
    
    const getPaymentStatusColor = (status) => {
        const colors = {
            'success': '#10b981',
            'pending': '#fbbf24',
            'failed': '#ef4444',
            'cancelled': '#ef4444',
            'refunded': '#3b82f6'
        };
        return colors[status] || '#6b7280';
    };
    
    const getPaymentStatusText = (status) => {
        const texts = {
            'success': 'Lunas',
            'pending': 'Pending',
            'failed': 'Ditolak',
            'cancelled': 'Dibatalkan',
            'refunded': 'Refund'
        };
        return texts[status] || status;
    };
    
    // Durasi
    const jamMulaiObj = new Date(`2000-01-01 ${booking.jam_mulai}`);
    const jamSelesaiObj = new Date(`2000-01-01 ${booking.jam_selesai}`);
    const durasiMs = jamSelesaiObj - jamMulaiObj;
    const durasiJam = durasiMs / (1000 * 60 * 60);
    
    modal.innerHTML = `
        <div class="detail-modal-overlay"></div>
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h2>Detail Booking #${booking.id}</h2>
                <button class="detail-modal-close" onclick="this.closest('.detail-modal-content').parentElement.classList.remove('show'); setTimeout(() => this.closest('.detail-modal-content').parentElement.remove(), 300);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="detail-modal-body">
                <!-- Status Section -->
                <div class="detail-section">
                    <div class="detail-status-row">
                        <div class="detail-status-item">
                            <span class="detail-label">Status Booking</span>
                            <span class="detail-status-badge" style="background: ${getStatusColor(booking.status)}; color: white;">
                                ${getStatusText(booking.status)}
                            </span>
                        </div>
                        <div class="detail-status-item">
                            <span class="detail-label">Status Pembayaran</span>
                            <span class="detail-status-badge" style="background: ${getPaymentStatusColor(booking.pembayaran_status)}; color: white;">
                                ${getPaymentStatusText(booking.pembayaran_status)}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Info -->
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-user"></i> Informasi Pelanggan
                    </h3>
                    <div class="detail-info-grid">
                        <div class="detail-info-item">
                            <span class="detail-label">Nama</span>
                            <span class="detail-value">${booking.customer_name}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">${booking.customer_email}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Telepon</span>
                            <span class="detail-value">${booking.customer_phone || '-'}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Lapangan Info -->
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-futbol"></i> Informasi Lapangan
                    </h3>
                    <div class="detail-info-grid">
                        <div class="detail-info-item">
                            <span class="detail-label">Nama Lapangan</span>
                            <span class="detail-value">${booking.lapangan_nama}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Jenis Olahraga</span>
                            <span class="detail-value">${booking.jenis_nama || '-'}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Harga per Jam</span>
                            <span class="detail-value">Rp ${Number(booking.harga_per_jam).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                    ${booking.fasilitas && booking.fasilitas.length > 0 ? `
                        <div class="detail-fasilitas">
                            <span class="detail-label">Fasilitas</span>
                            <div class="detail-fasilitas-list">
                                ${booking.fasilitas.map(f => `<span class="detail-fasilitas-badge">${f}</span>`).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
                
                <!-- Booking Schedule -->
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-calendar"></i> Jadwal Booking
                    </h3>
                    <div class="detail-info-grid">
                        <div class="detail-info-item">
                            <span class="detail-label">Tanggal</span>
                            <span class="detail-value">${formattedDate}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Jam</span>
                            <span class="detail-value">${jamMulai} - ${jamSelesai}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Durasi</span>
                            <span class="detail-value">${durasiJam} jam</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Info -->
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-credit-card"></i> Informasi Pembayaran
                    </h3>
                    <div class="detail-info-grid">
                        <div class="detail-info-item">
                            <span class="detail-label">Metode Pembayaran</span>
                            <span class="detail-value">${booking.pembayaran_metode || '-'}</span>
                        </div>
                        <div class="detail-info-item">
                            <span class="detail-label">Tanggal Pembayaran</span>
                            <span class="detail-value">${booking.pembayaran_created_at ? new Date(booking.pembayaran_created_at).toLocaleDateString('id-ID') : '-'}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Price Summary -->
                <div class="detail-section detail-price-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-file-invoice"></i> Ringkasan Harga
                    </h3>
                    <div class="detail-price-summary">
                        <div class="detail-price-row">
                            <span>Harga per Jam</span>
                            <span>Rp ${Number(booking.harga_per_jam).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="detail-price-row">
                            <span>Durasi (${durasiJam} jam)</span>
                            <span>×</span>
                        </div>
                        <div class="detail-price-row detail-total">
                            <span>Total</span>
                            <span>Rp ${Number(booking.total_harga).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Rating Section -->
                ${booking.rating ? `
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-star"></i> Rating & Ulasan
                        </h3>
                        <div class="detail-rating-box">
                            <div class="detail-rating-stars">
                                ${[...Array(5)].map((_, i) => 
                                    `<i class="fas fa-star" style="color: ${i < booking.rating.rating ? '#fbbf24' : '#e5e7eb'};"></i>`
                                ).join('')}
                                <span class="detail-rating-number">${booking.rating.rating}/5</span>
                            </div>
                            <p class="detail-rating-text">"${booking.rating.komentar}"</p>
                        </div>
                    </div>
                ` : ''}
            </div>
            
            <div class="detail-modal-footer">
                <button onclick="this.closest('.booking-modal').classList.remove('show'); setTimeout(() => this.closest('.booking-modal').remove(), 300);" class="detail-btn-close">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on overlay click
    const overlay = modal.querySelector('.detail-modal-overlay');
    overlay.addEventListener('click', () => {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#bookingTableBody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show message if no results
            if (visibleCount === 0 && searchTerm.length > 0) {
                const emptyRow = document.querySelector('#bookingTableBody tr[style*="display: none"]');
                if (!emptyRow) {
                    const tbody = document.getElementById('bookingTableBody');
                    const noResults = document.createElement('tr');
                    noResults.innerHTML = '<td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">Tidak ada hasil pencarian</td>';
                    tbody.appendChild(noResults);
                }
            }
        });
    }
});

// Enhanced animations on load
window.addEventListener('load', function() {
    const rows = document.querySelectorAll('#bookingTableBody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease-out';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
});


console.log('Booking admin JS loaded');