// Current filter state
let currentCategory = 'all';

// Filter by Category
function filterCategory(category) {
    currentCategory = category;
    const cards = document.querySelectorAll('.field-card');
    
    // Update select value
    document.getElementById('categorySelect').value = category;
    
    // Filter cards
    let visibleCount = 0;
    cards.forEach(function(card) {
        const cardCategory = card.getAttribute('data-category');
        if (category === 'all' || cardCategory === category) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (visibleCount > 0) {
        noResults.classList.add('hidden');
    } else {
        noResults.classList.remove('hidden');
    }
}

// Search Fields
function searchFields() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const cards = document.querySelectorAll('.field-card');
    
    let visibleCount = 0;
    cards.forEach(function(card) {
        const name = card.getAttribute('data-name').toLowerCase();
        const cardCategory = card.getAttribute('data-category');
        
        const matchesSearch = filter === '' || name.includes(filter);
        const matchesCategory = currentCategory === 'all' || cardCategory === currentCategory;
        
        if (matchesSearch && matchesCategory) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (visibleCount > 0) {
        noResults.classList.add('hidden');
    } else {
        noResults.classList.remove('hidden');
    }
}

// Sort Fields
function sortFields(sortBy) {
    const grid = document.getElementById('fieldsGrid');
    const cards = Array.from(document.querySelectorAll('.field-card'));
    
    cards.sort(function(a, b) {
        switch(sortBy) {
            case 'price-low':
                return parseInt(a.getAttribute('data-price')) - parseInt(b.getAttribute('data-price'));
            case 'price-high':
                return parseInt(b.getAttribute('data-price')) - parseInt(a.getAttribute('data-price'));
            case 'rating':
                return parseFloat(b.getAttribute('data-rating')) - parseFloat(a.getAttribute('data-rating'));
            default:
                return 0;
        }
    });
    
    // Re-append cards in sorted order
    cards.forEach(function(card) {
        grid.appendChild(card);
    });
}

// Smooth Scroll for navigation links
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});