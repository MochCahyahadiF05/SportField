// Image Slider
document.addEventListener("DOMContentLoaded", function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");

    function showSlide(index) {
        // Remove active class from all slides
        slides.forEach(function(slide) {
            slide.classList.remove("active");
        });

        // Remove active class from all dots
        dots.forEach(function(dot) {
            dot.classList.remove("active");
        });

        // Add active class to current slide and dot
        slides[index].classList.add("active");
        dots[index].classList.add("active");

        currentSlide = index;
    }

    // Function to go to specific slide (called from HTML onclick)
    window.goToSlide = function(index) {
        showSlide(index);
    };

    // Auto-advance slider every 5 seconds
    setInterval(function() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }, 5000);

    // Initialize first slide
    showSlide(0);
});

// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
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

// Form submission handler (optional)
const contactForm = document.querySelector('.contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Terima kasih! Pesan Anda telah dikirim.');
        contactForm.reset();
    });
}