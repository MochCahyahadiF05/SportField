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
    // Redirect to detail page or open modal
    alert('Detail booking #' + bookingId + ' (fitur akan ditambahkan)');
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