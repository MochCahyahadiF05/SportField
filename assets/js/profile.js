// Tab Switching
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.menu-item');
    const tabContents = document.querySelectorAll('.tab-content');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all menu items
            menuItems.forEach(mi => mi.classList.remove('active'));
            
            // Add active class to clicked menu item
            this.classList.add('active');
            
            // Hide all tab contents
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            // Show target tab content
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
});

// Filter History
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const historyItems = document.querySelectorAll('.history-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Remove active class from all filter buttons
            filterBtns.forEach(fb => fb.classList.remove('active'));
            
            // Add active class to clicked filter button
            this.classList.add('active');
            
            // Filter history items
            historyItems.forEach(item => {
                const status = item.getAttribute('data-status');
                
                if (filter === 'all') {
                    item.style.display = 'grid';
                } else if (status === filter) {
                    item.style.display = 'grid';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});

// Edit Profile
function editProfile() {
    alert('Fitur Edit Profil akan segera hadir!');
    // Here you would typically open a modal or redirect to edit page
}

// Logout
function logout() {
    const confirmLogout = confirm('Apakah Anda yakin ingin keluar?');
    
    if (confirmLogout) {
        // Here you would typically clear session/token
        alert('Logout berhasil!');
        // Redirect to login page
        // window.location.href = 'login.html';
    }
}

// Delete Account
function deleteAccount() {
    const confirmDelete = confirm(
        'PERINGATAN: Tindakan ini akan menghapus akun Anda secara permanen dan tidak dapat dibatalkan.\n\n' +
        'Apakah Anda yakin ingin melanjutkan?'
    );
    
    if (confirmDelete) {
        const doubleConfirm = confirm(
            'Konfirmasi terakhir: Semua data Anda termasuk riwayat booking akan hilang.\n\n' +
            'Apakah Anda benar-benar yakin?'
        );
        
        if (doubleConfirm) {
            // Here you would typically send delete request to server
            alert('Akun berhasil dihapus. Anda akan diarahkan ke halaman utama.');
            // window.location.href = 'index.html';
        }
    }
}

// Form Submission Handler for Settings
document.addEventListener('DOMContentLoaded', function() {
    const settingsForm = document.querySelector('.settings-form');
    
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Here you would typically send the data to your server
            console.log('Settings updated');
            
            // Show success message
            alert('Pengaturan berhasil disimpan!');
        });
    }
});

// History Item Actions
document.addEventListener('DOMContentLoaded', function() {
    // Detail buttons
    const detailBtns = document.querySelectorAll('.btn-detail');
    detailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const historyItem = this.closest('.history-item');
            const fieldName = historyItem.querySelector('h3').textContent;
            
            alert(`Menampilkan detail booking untuk: ${fieldName}`);
            // Here you would typically open a modal with booking details
        });
    });
    
    // Cancel buttons
    const cancelBtns = document.querySelectorAll('.btn-cancel');
    cancelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const confirmCancel = confirm('Apakah Anda yakin ingin membatalkan booking ini?');
            
            if (confirmCancel) {
                const historyItem = this.closest('.history-item');
                const fieldName = historyItem.querySelector('h3').textContent;
                
                // Update status to cancelled
                const badge = historyItem.querySelector('.badge');
                badge.className = 'badge badge-cancelled';
                badge.textContent = 'Dibatalkan';
                
                // Update data-status
                historyItem.setAttribute('data-status', 'cancelled');
                
                // Replace cancel button with booking button
                this.outerHTML = '<button class="btn-action btn-primary">Booking Lagi</button>';
                
                alert(`Booking untuk ${fieldName} berhasil dibatalkan`);
            }
        });
    });
    
    // Booking again functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-primary') && 
            e.target.textContent === 'Booking Lagi') {
            const historyItem = e.target.closest('.history-item');
            const fieldName = historyItem.querySelector('h3').textContent;
            
            alert(`Membuka halaman booking untuk: ${fieldName}`);
            // Here you would typically redirect to booking page
            // window.location.href = 'detail-lapangan.html';
        }
    });
});

// Change Photo
document.addEventListener('DOMContentLoaded', function() {
    const changePhotoBtn = document.querySelector('.btn-change-photo');
    
    if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', function() {
            // Create temporary file input
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    // Check file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2MB');
                        return;
                    }
                    
                    // Check file type
                    if (!file.type.startsWith('image/')) {
                        alert('File harus berupa gambar');
                        return;
                    }
                    
                    // Read and display the image
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const profileAvatar = document.querySelector('.profile-avatar');
                        const userAvatar = document.querySelector('.user-avatar');
                        
                        if (profileAvatar) {
                            profileAvatar.src = event.target.result;
                        }
                        if (userAvatar) {
                            userAvatar.src = event.target.result;
                        }
                        
                        alert('Foto profil berhasil diubah!');
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Trigger file input
            fileInput.click();
        });
    }
});