// ===== Variabel Global =====
let bookedHours = [];
let selectedFile = null;

// ===== Custom Alert =====
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert custom-alert-${type}`;
    
    // Icon based on type
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check-circle"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-exclamation-circle"></i>';
    } else if (type === 'warning') {
        icon = '<i class="fas fa-exclamation-triangle"></i>';
    } else {
        icon = '<i class="fas fa-info-circle"></i>';
    }
    
    alertDiv.innerHTML = `
        <div class="custom-alert-content">
            <div class="custom-alert-icon">${icon}</div>
            <div class="custom-alert-message">${message}</div>
            <button class="custom-alert-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// ===== Load Booked Hours =====
function updateAvailableHours() {
    const bookingDate = document.getElementById('bookingDate').value;
    const lapanganId = document.getElementById('lapanganId').value;

    if (!bookingDate) {
        showAlert('Silakan pilih tanggal', 'warning');
        return;
    }

    // Validasi tanggal tidak boleh masa lalu
    const selectedDate = new Date(bookingDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        document.getElementById('dateError').textContent = 'Tanggal tidak boleh di masa lalu';
        document.getElementById('dateError').style.display = 'block';
        return;
    }

    document.getElementById('dateError').style.display = 'none';

    // Fetch booked hours
    fetch('booking-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=get_booked_hours&lapangan_id=${lapanganId}&tanggal=${bookingDate}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bookedHours = data.booked_hours || [];
            updateTimeSelects();
            updatePrice();
        }
    })
    .catch(error => console.error('Error:', error));
}

// ===== Update Time Selects =====
function updateTimeSelects() {
    const startSelect = document.getElementById('startTime');
    const endSelect = document.getElementById('endTime');

    // Reset disabled state
    startSelect.querySelectorAll('option').forEach(option => {
        if (option.value) {
            const hour = parseInt(option.value.split(':')[0]);
            option.disabled = bookedHours.includes(hour);
            if (option.disabled) {
                option.textContent = option.textContent.split(' (')[0] + ' (Sudah Dibooking)';
            } else {
                option.textContent = option.value;
            }
        }
    });

    endSelect.querySelectorAll('option').forEach(option => {
        if (option.value) {
            const hour = parseInt(option.value.split(':')[0]);
            option.disabled = bookedHours.includes(hour - 1);
            if (option.disabled) {
                option.textContent = option.textContent.split(' (')[0] + ' (Sudah Dibooking)';
            } else {
                option.textContent = option.value;
            }
        }
    });
}

// ===== Update Price =====
function updatePrice() {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const hargaPerJam = parseFloat(document.getElementById('hargaPerJam').value);
    
    if (!startTime || !endTime) {
        document.getElementById('totalPrice').innerText = 'Rp 0';
        return;
    }
    
    const startHour = parseInt(startTime.split(':')[0]);
    const endHour = parseInt(endTime.split(':')[0]);
    
    if (endHour <= startHour) {
        document.getElementById('totalPrice').innerText = 'Rp 0';
        return;
    }
    
    const duration = endHour - startHour;
    const totalPrice = duration * hargaPerJam;
    
    const formattedPrice = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(totalPrice);
    
    document.getElementById('totalPrice').innerText = formattedPrice;
}

// ===== Open Payment Modal =====
function openPaymentModal() {
    const date = document.getElementById('bookingDate').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const lapanganId = document.getElementById('lapanganId').value;
    const lapanganNama = document.getElementById('lapanganNama').value;
    const hargaPerJam = parseFloat(document.getElementById('hargaPerJam').value);
    
    // Validasi
    if (!date) {
        showAlert('Silakan pilih tanggal booking', 'warning');
        return;
    }
    
    if (!startTime) {
        showAlert('Silakan pilih jam mulai', 'warning');
        return;
    }
    
    if (!endTime) {
        showAlert('Silakan pilih jam selesai', 'warning');
        return;
    }
    
    const startHour = parseInt(startTime.split(':')[0]);
    const endHour = parseInt(endTime.split(':')[0]);
    
    if (endHour <= startHour) {
        showAlert('Jam selesai harus lebih besar dari jam mulai', 'warning');
        return;
    }

    // Validasi jam tidak bentrok
    for (let i = startHour; i < endHour; i++) {
        if (bookedHours.includes(i)) {
            showAlert('Jam ' + String(i).padStart(2, '0') + ':00 - ' + String(i+1).padStart(2, '0') + ':00 sudah di-booking. Silakan pilih jam lain', 'error');
            return;
        }
    }

    // Check user login
    fetch('booking-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=check_login`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.logged_in) {
            showAlert('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '../auth/login.php';
            }, 1500);
            return;
        }

        // Update modal dengan booking data
        const dateObj = new Date(date);
        const formattedDate = dateObj.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        document.getElementById('modalLapangan').innerText = lapanganNama;
        document.getElementById('modalDate').innerText = formattedDate;
        document.getElementById('modalTime').innerText = startTime + ' - ' + endTime;
        
        const duration = endHour - startHour;
        document.getElementById('modalDuration').innerText = duration + ' Jam';
        
        const totalPrice = duration * hargaPerJam;
        const formattedPrice = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(totalPrice);
        
        document.getElementById('modalTotal').innerText = formattedPrice;
        
        // Store booking data for submission
        window.bookingData = {
            lapangan_id: lapanganId,
            tanggal: date,
            jam_mulai: startTime,
            jam_selesai: endTime,
            total_harga: totalPrice
        };

        // Reset payment form
        document.getElementById('transfer').checked = true;
        document.getElementById('paymentProof').value = '';
        selectedFile = null;
        document.getElementById('uploadedFile').classList.remove('active');
        document.getElementById('fileName').innerText = '-';

        // Show payment details container
        document.getElementById('paymentDetails').classList.add('active');
        
        // Show correct payment method details
        showPaymentDetails();
        
        // Show modal
        const modal = document.getElementById('paymentModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan. Silakan coba lagi', 'error');
    });
}

// ===== Show Payment Details =====
function showPaymentDetails() {
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const transferDetails = document.getElementById('transferDetails');
    const qrisDetails = document.getElementById('qrisDetails');

    if (paymentMethod === 'transfer') {
        transferDetails.style.display = 'block';
        qrisDetails.style.display = 'none';
    } else {
        transferDetails.style.display = 'none';
        qrisDetails.style.display = 'block';
    }
}

// ===== Close Payment Modal =====
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('active');
    document.getElementById('paymentDetails').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// ===== Handle File Upload =====
function handleFileUpload(event) {
    const file = event.target.files[0];
    
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        if (fileSize > 5) {
            showAlert('Ukuran file maksimal 5MB', 'warning');
            event.target.value = '';
            selectedFile = null;
            return;
        }

        selectedFile = file;
        document.getElementById('fileName').innerText = fileName + ' (' + fileSize + ' MB)';
        document.getElementById('uploadedFile').classList.add('active');
    }
}

// ===== Submit Payment =====
function submitPayment() {
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;

    if (!selectedFile) {
        showAlert('Silakan upload bukti pembayaran terlebih dahulu', 'warning');
        return;
    }

    if (!window.bookingData) {
        showAlert('Data booking tidak ditemukan', 'error');
        return;
    }

    // Show loading
    const btn = event.target;
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    // Step 1: Create Booking
    const formData = new FormData();
    formData.append('action', 'create_booking');
    formData.append('lapangan_id', window.bookingData.lapangan_id);
    formData.append('tanggal', window.bookingData.tanggal);
    formData.append('jam_mulai', window.bookingData.jam_mulai);
    formData.append('jam_selesai', window.bookingData.jam_selesai);
    formData.append('total_harga', window.bookingData.total_harga);

    fetch('booking-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Step 2: Process Payment
            const paymentFormData = new FormData();
            paymentFormData.append('action', 'process_payment');
            paymentFormData.append('booking_id', data.booking_id);
            paymentFormData.append('metode', paymentMethod);
            paymentFormData.append('jumlah', window.bookingData.total_harga);
            paymentFormData.append('bukti_bayar', selectedFile);

            return fetch('booking-handler.php', {
                method: 'POST',
                body: paymentFormData
            }).then(response => response.json());
        } else {
            throw new Error(data.message || 'Gagal membuat booking');
        }
    })
    .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (data.success) {
            showAlert('Booking dan pembayaran berhasil diproses! Terima kasih.', 'success');
            closePaymentModal();
            // Reset form
            document.getElementById('bookingDate').value = '';
            document.getElementById('startTime').value = '';
            document.getElementById('endTime').value = '';
            document.getElementById('totalPrice').innerText = 'Rp 0';
            // Redirect ke halaman tracking/history
            setTimeout(() => {
                window.location.href = 'profile.php';
            }, 2000);
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.textContent = originalText;
        alert('Error: ' + error.message);
    });
}

// ===== Modal Backdrop Click =====
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('paymentModal');
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('modal-backdrop')) {
                closePaymentModal();
            }
        });
    }
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('bookingDate').setAttribute('min', today);

    // Star rating interaction
    const stars = document.querySelectorAll('#starRating .star-select');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.getElementById('selectedRating').value = rating;
            
            stars.forEach(s => {
                const starRating = s.getAttribute('data-rating');
                if (starRating <= rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
});

// ===== Open Rating Modal =====
function openRatingModal() {
    const lapanganId = document.getElementById('lapanganId').value;
    document.getElementById('ratingLapanganId').value = lapanganId;
    document.getElementById('selectedRating').value = 0;
    document.getElementById('reviewText').value = '';
    document.querySelectorAll('#starRating .star-select').forEach(s => s.classList.remove('active'));
    
    // Reset button text dan onclick
    const submitBtn = document.querySelector('#ratingModal .btn-confirm');
    submitBtn.textContent = 'Kirim Rating';
    submitBtn.onclick = () => submitRating();
    
    const modal = document.getElementById('ratingModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// ===== Close Rating Modal =====
function closeRatingModal() {
    const modal = document.getElementById('ratingModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// ===== Submit Rating =====
function submitRating() {
    const lapanganId = document.getElementById('ratingLapanganId').value;
    const rating = document.getElementById('selectedRating').value;
    const review = document.getElementById('reviewText').value;
    
    if (rating == 0) {
        showAlert('Silakan pilih rating terlebih dahulu', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'submit_rating');
    formData.append('lapangan_id', lapanganId);
    formData.append('rating', rating);
    formData.append('review', review);
    
    fetch('rating-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Rating berhasil disimpan!', 'success');
            closeRatingModal();
            // Reload halaman untuk menampilkan rating terbaru
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error: ' + (data.message || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'error');
    });
}

// ===== Delete Rating =====
function deleteRating(ratingId) {
    if (!confirm('Apakah Anda yakin ingin menghapus rating ini?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_rating');
    formData.append('rating_id', ratingId);
    
    fetch('rating-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Rating berhasil dihapus!', 'success');
            // Reload halaman
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error: ' + (data.message || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'error');
    });
}

// ===== Edit Rating =====
function editRating(ratingId, currentRating, currentReview) {
    // Set modal data
    document.getElementById('ratingLapanganId').value = ratingId;
    document.getElementById('selectedRating').value = currentRating;
    document.getElementById('reviewText').value = currentReview;
    
    // Highlight stars
    const stars = document.querySelectorAll('#starRating .star-select');
    stars.forEach(s => {
        const starRating = s.getAttribute('data-rating');
        if (starRating <= currentRating) {
            s.classList.add('active');
        } else {
            s.classList.remove('active');
        }
    });
    
    // Change submit button text
    const submitBtn = document.querySelector('#ratingModal .btn-confirm');
    submitBtn.textContent = 'Perbarui Rating';
    submitBtn.onclick = () => updateRatingSubmit();
    
    // Open modal
    const modal = document.getElementById('ratingModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Close dropdown
    document.querySelectorAll('.menu-dropdown').forEach(m => m.classList.remove('active'));
}

// ===== Update Rating Submit =====
function updateRatingSubmit() {
    const ratingId = document.getElementById('ratingLapanganId').value;
    const rating = document.getElementById('selectedRating').value;
    const review = document.getElementById('reviewText').value;
    
    if (rating == 0) {
        showAlert('Silakan pilih rating terlebih dahulu', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_rating');
    formData.append('rating_id', ratingId);
    formData.append('rating', rating);
    formData.append('review', review);
    
    fetch('rating-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Rating berhasil disimpan!', 'success');
            closeRatingModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'error');
    });
}

// ===== Toggle Review Menu =====
function toggleReviewMenu(button) {
    event.stopPropagation(); // Prevent immediate closure
    
    const dropdown = button.nextElementSibling;
    const isActive = dropdown.classList.contains('active');
    
    // Close all other dropdowns
    document.querySelectorAll('.menu-dropdown').forEach(m => {
        if (m !== dropdown) {
            m.classList.remove('active');
        }
    });
    
    // Toggle current dropdown
    if (isActive) {
        dropdown.classList.remove('active');
        document.removeEventListener('click', closeDropdownOnClickOutside);
    } else {
        dropdown.classList.add('active');
        
        // Close dropdown when clicking outside (delay to prevent immediate trigger)
        setTimeout(() => {
            document.addEventListener('click', closeDropdownOnClickOutside);
        }, 0);
    }
}

function closeDropdownOnClickOutside(e) {
    // Jika klik di luar review-menu, tutup dropdown
    if (!e.target.closest('.review-menu')) {
        document.querySelectorAll('.menu-dropdown').forEach(m => {
            m.classList.remove('active');
        });
        document.removeEventListener('click', closeDropdownOnClickOutside);
    }
}

// ===== Modal Backdrop Click =====
document.addEventListener('DOMContentLoaded', () => {
    const ratingModal = document.getElementById('ratingModal');
    if (ratingModal) {
        ratingModal.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('modal-backdrop')) {
                closeRatingModal();
            }
        });
    }
});
