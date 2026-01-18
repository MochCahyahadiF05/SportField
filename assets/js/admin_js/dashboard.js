// Sidebar Toggle Function
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("active");
}

// Close sidebar when clicking outside on mobile
document.addEventListener("click", function (event) {
  const sidebar = document.getElementById("sidebar");
  const menuToggle = document.querySelector(".menu-toggle");

  // Check if click is outside sidebar and not on menu toggle
  if (window.innerWidth < 768) {
    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
      sidebar.classList.remove("active");
    }
  }
});

// Handle window resize
window.addEventListener("resize", function () {
  const sidebar = document.getElementById("sidebar");

  // Remove active class on mobile when resizing to desktop
  if (window.innerWidth >= 768) {
    sidebar.classList.remove("active");
  }
});

// Active menu item highlight
document.addEventListener("DOMContentLoaded", function () {
  const menuItems = document.querySelectorAll(".sidebar-item");

  menuItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      // Don't prevent default for logout link
      if (!this.querySelector(".fa-sign-out-alt")) {
        // Remove active class from all items
        menuItems.forEach((mi) => mi.classList.remove("active"));

        // Add active class to clicked item
        this.classList.add("active");
      }
    });
  });
});

