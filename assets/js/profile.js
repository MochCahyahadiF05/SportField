// Toast Notification Function
function showToast(message, type = 'success', duration = 3000) {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 type === 'warning' ? 'fa-warning' : 'fa-info-circle';
    
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        </div>
        <div class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after duration
    setTimeout(() => {
        toast.classList.add('toast-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    `;
    document.body.appendChild(container);
    return container;
}

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
    showToast('Fitur Edit Profil akan segera hadir!', 'info');
    // Here you would typically open a modal or redirect to edit page
}

// Logout
function logout() {
    const confirmLogout = confirm('Apakah Anda yakin ingin keluar?');
    
    if (confirmLogout) {
        // Here you would typically clear session/token
        showToast('Logout berhasil!', 'success');
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
            showToast('Akun berhasil dihapus. Anda akan diarahkan ke halaman utama.', 'success');
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
            
            // Get form inputs
            const nameInput = settingsForm.querySelector('input[type="text"]');
            const emailInput = settingsForm.querySelector('input[type="email"]');
            const phoneInput = settingsForm.querySelector('input[type="tel"]');
            const oldPasswordInput = settingsForm.querySelector('input[placeholder="Masukkan password lama"]');
            const newPasswordInput = settingsForm.querySelector('input[placeholder="Masukkan password baru"]');
            const confirmPasswordInput = settingsForm.querySelector('input[placeholder="Konfirmasi password baru"]');
            
            // Check if updating profile or password
            if (oldPasswordInput.value || newPasswordInput.value || confirmPasswordInput.value) {
                // Change password
                updatePassword(oldPasswordInput.value, newPasswordInput.value, confirmPasswordInput.value);
            } else {
                // Update profile
                updateProfile(nameInput.value, emailInput.value, phoneInput.value);
            }
        });
    }
});

function updateProfile(name, email, phone) {
    const formData = new FormData();
    formData.append('action', 'update_profile');
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    
    fetch('../../page/user/profile-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memperbarui profil', 'error');
    });
}

function updatePassword(oldPassword, newPassword, confirmPassword) {
    const formData = new FormData();
    formData.append('action', 'change_password');
    formData.append('old_password', oldPassword);
    formData.append('new_password', newPassword);
    formData.append('confirm_password', confirmPassword);
    
    fetch('../../page/user/profile-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Clear password fields
            document.querySelector('input[placeholder="Masukkan password lama"]').value = '';
            document.querySelector('input[placeholder="Masukkan password baru"]').value = '';
            document.querySelector('input[placeholder="Konfirmasi password baru"]').value = '';
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat mengubah password', 'error');
    });
}

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
                        showToast('Ukuran file maksimal 2MB', 'warning');
                        return;
                    }
                    
                    // Check file type
                    if (!file.type.startsWith('image/')) {
                        showToast('File harus berupa gambar', 'warning');
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
                        
                        showToast('Foto profil berhasil diubah!', 'success');
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Trigger file input
            fileInput.click();
        });
    }
});