// ==========================================
// PEMBAYARAN PAGE - JAVASCRIPT
// ==========================================

// Custom Confirm Modal
function showConfirmModal(title, message, onConfirm, confirmText = 'Setujui', cancelText = 'Batal') {
    const modal = document.createElement('div');
    modal.className = 'confirm-modal show';
    modal.innerHTML = `
        <div class="confirm-modal-content">
            <div class="confirm-modal-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="confirm-modal-title">${title}</h3>
            <p class="confirm-modal-message">${message}</p>
            <div class="confirm-modal-actions">
                <button class="confirm-btn cancel-btn">${cancelText}</button>
                <button class="confirm-btn confirm-btn-action">${confirmText}</button>
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

// Custom Alert Modal
function showAlertModal(title, message, icon = 'success') {
    const modal = document.createElement('div');
    modal.className = 'confirm-modal show';
    modal.innerHTML = `
        <div class="confirm-modal-content">
            <div class="confirm-modal-icon ${icon}">
                <i class="fas fa-${icon === 'success' ? 'check-circle' : icon === 'error' ? 'times-circle' : 'info-circle'}"></i>
            </div>
            <h3 class="confirm-modal-title">${title}</h3>
            <p class="confirm-modal-message">${message}</p>
            <div class="confirm-modal-actions">
                <button class="confirm-btn confirm-btn-action">Oke</button>
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

// View proof of payment modal
function viewProof(pembayaranId, buktiPath) {
    const modal = document.getElementById('proofModal');
    const image = document.getElementById('proofImage');
    
    if (buktiPath) {
        image.src = '../../' + buktiPath;
        modal.classList.add('show');
    }
}

// Close proof modal
function closeProofModal() {
    const modal = document.getElementById('proofModal');
    modal.classList.remove('show');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('proofModal');
    if (e.target === modal) {
        closeProofModal();
    }
});

// Approve payment
function approvePayment(pembayaranId, bookingId) {
    showConfirmModal(
        'Setujui Pembayaran?',
        'Anda akan menerima pembayaran ini. Booking akan otomatis dikonfirmasi.',
        () => {
            const formData = new FormData();
            formData.append('action', 'approve_payment');
            formData.append('pembayaran_id', pembayaranId);
            formData.append('booking_id', bookingId);
            
            fetch('../../page/admin/pembayaran-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlertModal('Berhasil!', 'Pembayaran telah disetujui.', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlertModal('Error!', data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                showAlertModal('Error!', 'Terjadi kesalahan jaringan', 'error');
            });
        },
        'Setujui',
        'Batal'
    );
}

// Reject payment
function rejectPayment(pembayaranId, bookingId) {
    showConfirmModal(
        'Tolak Pembayaran?',
        'Anda akan menolak pembayaran ini. Booking akan dibatalkan.',
        () => {
            const formData = new FormData();
            formData.append('action', 'reject_payment');
            formData.append('pembayaran_id', pembayaranId);
            formData.append('booking_id', bookingId);
            
            fetch('../../page/admin/pembayaran-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlertModal('Berhasil!', 'Pembayaran telah ditolak.', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlertModal('Error!', data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                showAlertModal('Error!', 'Terjadi kesalahan jaringan', 'error');
            });
        },
        'Tolak',
        'Batal'
    );
}

// Request refund (untuk pembayaran yang sudah success)
function requestRefund(pembayaranId, bookingId) {
    // Create custom prompt modal
    const modal = document.createElement('div');
    modal.className = 'confirm-modal show';
    modal.innerHTML = `
        <div class="confirm-modal-content">
            <div class="confirm-modal-icon warning">
                <i class="fas fa-undo"></i>
            </div>
            <h3 class="confirm-modal-title">Proses Refund?</h3>
            <p class="confirm-modal-message">Masukkan alasan refund untuk pembayaran ini:</p>
            <input type="text" id="refundReasonInput" class="refund-input" placeholder="Contoh: Diminta pelanggan" autofocus>
            <div class="confirm-modal-actions">
                <button class="confirm-btn cancel-btn">Batal</button>
                <button class="confirm-btn confirm-btn-action">Proses Refund</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const reasonInput = modal.querySelector('#refundReasonInput');
    const cancelBtn = modal.querySelector('.cancel-btn');
    const confirmBtn = modal.querySelector('.confirm-btn-action');
    
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }
    
    cancelBtn.addEventListener('click', closeModal);
    confirmBtn.addEventListener('click', () => {
        const reason = reasonInput.value.trim();
        if (!reason) {
            reasonInput.focus();
            reasonInput.style.borderColor = '#ef4444';
            return;
        }
        
        closeModal();
        
        const formData = new FormData();
        formData.append('action', 'request_refund');
        formData.append('pembayaran_id', pembayaranId);
        formData.append('refund_reason', reason);
        
        fetch('../../page/admin/pembayaran-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlertModal('Berhasil!', 'Refund telah diproses.', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlertModal('Error!', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            showAlertModal('Error!', 'Terjadi kesalahan jaringan', 'error');
        });
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');
    const tableBody = document.getElementById('pembayaranTableBody');
    
    if (!tableBody) return;
    
    function filterTable() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const statusFilter = filterStatus?.value || '';
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.getAttribute('data-status');
            
            let matches = true;
            
            // Search filter
            if (searchTerm && !text.includes(searchTerm)) {
                matches = false;
            }
            
            // Status filter
            if (statusFilter && status !== statusFilter) {
                matches = false;
            }
            
            row.style.display = matches ? '' : 'none';
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', filterTable);
    }
    
    if (filterStatus) {
        filterStatus.addEventListener('change', filterTable);
    }
});

// Animate table rows on page load
window.addEventListener('load', function() {
    const rows = document.querySelectorAll('.data-table tbody tr');
    rows.forEach((row, index) => {
        row.style.animation = `fadeIn 0.5s ease forwards`;
        row.style.animationDelay = (index * 0.05) + 's';
    });
});

// Animation keyframe
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
