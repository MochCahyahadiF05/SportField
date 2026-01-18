// Change Main Image
function changeMainImage(element) {
    const mainImage = document.getElementById('mainImage');
    mainImage.style.backgroundImage = element.style.backgroundImage;
    
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    // Add active class to clicked thumbnail
    element.classList.add('active');
}

// Update Price based on duration
function updatePrice() {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    
    if (!startTime || !endTime) {
        document.getElementById('totalPrice').innerText = '0';
        return;
    }
    
    const startHour = parseInt(startTime.split(':')[0]);
    const endHour = parseInt(endTime.split(':')[0]);
    
    if (endHour <= startHour) {
        document.getElementById('totalPrice').innerText = '0';
        return;
    }
    
    const duration = endHour - startHour;
    const pricePerHour = 150000; // 150K
    const totalPrice = duration * pricePerHour;
    
    // Format price to Indonesian Rupiah
    const formattedPrice = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(totalPrice);
    
    document.getElementById('totalPrice').innerText = formattedPrice;
}

// Open Payment Modal
function openPaymentModal() {
    // Validate form
    const date = document.getElementById('bookingDate').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    
    if (!date) {
        alert('Silakan pilih tanggal booking');
        return;
    }
    
    if (!startTime) {
        alert('Silakan pilih jam mulai');
        return;
    }
    
    if (!endTime) {
        alert('Silakan pilih jam selesai');
        return;
    }
    
    const startHour = parseInt(startTime.split(':')[0]);
    const endHour = parseInt(endTime.split(':')[0]);
    
    if (endHour <= startHour) {
        alert('Jam selesai harus lebih besar dari jam mulai');
        return;
    }
    
    // Update modal with booking data
    const dateObj = new Date(date);
    const formattedDate = dateObj.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    document.getElementById('modalDate').innerText = formattedDate;
    document.getElementById('modalTime').innerText = startTime + ' - ' + endTime;
    
    const duration = endHour - startHour;
    document.getElementById('modalDuration').innerText = duration + ' Jam';
    
    const pricePerHour = 150000;
    const totalPrice = duration * pricePerHour;
    const formattedPrice = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(totalPrice);
    
    document.getElementById('modalTotal').innerText = formattedPrice;
    
    // Show modal
    const modal = document.getElementById('paymentModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close Payment Modal
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Handle File Upload
function handleFileUpload(event) {
    const file = event.target.files[0];
    
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        if (fileSize > 5) {
            alert('Ukuran file maksimal 5MB');
            event.target.value = '';
            return;
        }
        
        document.getElementById('fileName').innerText = fileName + ' (' + fileSize + ' MB)';
        document.getElementById('uploadedFile').classList.add('active');
    }
}

// Close modal when clicking backdrop
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('paymentModal');
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('modal-backdrop')) {
                closePaymentModal();
            }
        });
    }
    
    // Update payment method styling
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active class from all options
            document.querySelectorAll('.method-option').forEach(option => {
                option.classList.remove('active');
            });
            
            // Add active class to selected option
            this.closest('.method-option').classList.add('active');
        });
    });
});