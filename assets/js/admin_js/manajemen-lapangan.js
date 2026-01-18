// File Upload Handler
function initializeFileUpload() {
    // Initialize file upload for "Tambah Lapangan" modal
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');

    if (uploadArea && fileInput) {
        setupUploadArea(uploadArea, fileInput, filePreview, fileName);
    }

    // Initialize file upload for "Kelola Lapangan" modal
    const uploadAreaEdit = document.getElementById('uploadAreaEdit');
    const fileInputEdit = document.getElementById('fileUploadEdit');
    const filePreviewEdit = document.getElementById('filePreviewEdit');
    const fileNameEdit = document.getElementById('fileNameEdit');

    if (uploadAreaEdit && fileInputEdit) {
        setupUploadArea(uploadAreaEdit, fileInputEdit, filePreviewEdit, fileNameEdit);
    }
}

function setupUploadArea(uploadArea, fileInput, filePreview, fileName) {
    // Click to upload
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Handle file selection from input
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFile(file, filePreview, fileName);
        }
    });

    // Drag and drop events
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.style.borderColor = '#16A34A';
        uploadArea.style.backgroundColor = 'rgba(22, 163, 74, 0.05)';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.style.borderColor = '#d1d5db';
        uploadArea.style.backgroundColor = 'transparent';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.style.borderColor = '#d1d5db';
        uploadArea.style.backgroundColor = 'transparent';

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            // Validate file type
            if (file.type === 'image/png' || file.type === 'image/jpeg') {
                fileInput.files = files;
                handleFile(file, filePreview, fileName);
            } else {
                alert('Hanya file PNG atau JPG yang diperbolehkan!');
            }
        }
    });
}

function handleFile(file, filePreview, fileName) {
    // Validate file size (5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('Ukuran file tidak boleh lebih dari 5MB!');
        return;
    }

    // Show file name and preview
    fileName.textContent = '✓ ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + 'MB)';
    filePreview.style.display = 'block';
    
    // Hide upload area
    const uploadAreaId = filePreview.id === 'filePreview' ? 'uploadArea' : 'uploadAreaEdit';
    const uploadArea = document.getElementById(uploadAreaId);
    if (uploadArea) {
        uploadArea.style.display = 'none';
    }
    
    // Hide current image preview if exists
    const currentImagePreview = document.getElementById('currentImagePreviewEdit');
    if (currentImagePreview && filePreview.id === 'filePreviewEdit') {
        currentImagePreview.style.display = 'none';
    }
}

function clearFileUpload() {
    const fileInput = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');
    const uploadArea = document.getElementById('uploadArea');

    if (fileInput) fileInput.value = '';
    if (filePreview) filePreview.style.display = 'none';
    if (uploadArea) uploadArea.style.display = 'block';
}

function clearFileUploadEdit() {
    const fileInput = document.getElementById('fileUploadEdit');
    const filePreview = document.getElementById('filePreviewEdit');
    const uploadArea = document.getElementById('uploadAreaEdit');

    if (fileInput) fileInput.value = '';
    if (filePreview) filePreview.style.display = 'none';
    if (uploadArea) uploadArea.style.display = 'block';
}

function changeImageEdit() {
    const currentImagePreview = document.getElementById('currentImagePreviewEdit');
    const uploadArea = document.getElementById('uploadAreaEdit');
    const fileInput = document.getElementById('fileUploadEdit');

    currentImagePreview.style.display = 'none';
    uploadArea.style.display = 'block';
    fileInput.click();
}

function cancelChangeImageEdit() {
    const currentImagePreview = document.getElementById('currentImagePreviewEdit');
    const filePreview = document.getElementById('filePreviewEdit');
    const uploadArea = document.getElementById('uploadAreaEdit');
    const fileInput = document.getElementById('fileUploadEdit');

    fileInput.value = '';
    filePreview.style.display = 'none';
    uploadArea.style.display = 'none';
    currentImagePreview.style.display = 'block';
}

// Sidebar Toggle for Mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
}

// Modal Functions
function openAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.add('show');
}

function closeAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('show');
}

function openDetailModal(lapanganName) {
    const modal = document.getElementById('detailModal');
    modal.classList.add('show');
    // Set active tab to info
    switchTab('info');
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.remove('show');
}

function openDeleteModal(lapanganName) {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('show');
    document.getElementById('deleteLapanganName').textContent = lapanganName;
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
}

function confirmDelete() {
    closeDeleteModal();
    alert('Lapangan berhasil dihapus!');
    // Here you would make an API call to delete the data
}

// Tab Switching in Detail Modal
function switchTab(tabName) {
    const tabs = ['info', 'jadwal', 'statistik'];
    
    tabs.forEach(tab => {
        const tabBtn = document.getElementById(`tab-${tab}`);
        const tabContent = document.getElementById(`content-${tab}`);
        
        if (tab === tabName) {
            tabBtn.classList.add('active');
            tabContent.style.display = 'block';
        } else {
            tabBtn.classList.remove('active');
            tabContent.style.display = 'none';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload
    initializeFileUpload();
    
    // Close modal when clicking outside
    const modals = ['addModal', 'detailModal', 'deleteModal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        }
    });
    
    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.querySelector('.menu-toggle');
        
        if (window.innerWidth < 769 && 
            sidebar.classList.contains('show') &&
            !sidebar.contains(e.target) && 
            !toggleButton.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        
        if (window.innerWidth >= 769) {
            sidebar.classList.remove('show');
        }
    });
});