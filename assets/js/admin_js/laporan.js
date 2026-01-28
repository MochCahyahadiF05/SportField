// Laporan Page - PDF Export Functionality

function exportToPDF(tanggalDari, tanggalSampai, tipelaporan) {
    // Get date values from form if not provided
    if (!tanggalDari || !tanggalSampai) {
        const fromDate = document.getElementById('tanggal_dari')?.value;
        const toDate = document.getElementById('tanggal_sampai')?.value;
        tanggalDari = fromDate || new Date().toISOString().split('T')[0].substring(0, 7) + '-01';
        tanggalSampai = toDate || new Date().toISOString().split('T')[0];
    }

    if (!tipelaporan) {
        tipelaporan = document.querySelector('select[name="tipe_laporan"]')?.value || 'harian';
    }
    
    // Redirect ke laporan-export.php dengan date dan type parameters
    window.location.href = `../../page/admin/laporan-export.php?tanggal_dari=${tanggalDari}&tanggal_sampai=${tanggalSampai}&tipe_laporan=${tipelaporan}`;
}

// Enhanced animations on load
window.addEventListener('load', function() {
    const rows = document.querySelectorAll('.data-table tbody tr');
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

console.log('Laporan admin JS loaded');
