// Pelanggan Page - Search & Filter Functionality

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customerTableBody tr');
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
                if (!document.getElementById('noResults')) {
                    const tbody = document.getElementById('customerTableBody');
                    const noResults = document.createElement('tr');
                    noResults.id = 'noResults';
                    noResults.innerHTML = '<td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">Tidak ada hasil pencarian</td>';
                    tbody.appendChild(noResults);
                }
            } else {
                const noResults = document.getElementById('noResults');
                if (noResults) {
                    noResults.remove();
                }
            }
        });
    }
});

// Enhanced animations on load
window.addEventListener('load', function() {
    const rows = document.querySelectorAll('#customerTableBody tr');
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

console.log('Pelanggan admin JS loaded');
