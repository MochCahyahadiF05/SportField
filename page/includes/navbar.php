<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/UAS/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/UAS/config/Auth.php';

$isLoggedIn = Auth::isLoggedIn();
$currentUser = Auth::getUser();
?>
<nav class="navbar">
    <div class="container">
        <div class="nav-content">
            <div class="logo-section">
                <div class="logo-icon">
                    <!-- <span>SF</span> -->
                    <img src="<?php echo ASSETS_URL; ?>img/Logo.png" alt="SportField logo">
                </div>
                <h1 class="logo-text">SportField</h1>
            </div>

            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'lapangan.php') === false && strpos($_SERVER['REQUEST_URI'], 'about.php') === false && strpos($_SERVER['REQUEST_URI'], 'profile.php') === false && strpos($_SERVER['REQUEST_URI'], 'login.php') === false && strpos($_SERVER['REQUEST_URI'], 'register.php') === false) ? 'active' : ''; ?>">Beranda</a></li>
                <li><a href="<?php echo BASE_URL; ?>page/user/lapangan.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'lapangan.php') !== false) ? 'active' : ''; ?>">Lapangan</a></li>
                <li><a href="<?php echo BASE_URL; ?>page/user/about.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'about.php') !== false) ? 'active' : ''; ?>">Tentang Kami</a></li>
            </ul>

            <div class="nav-actions">
                <?php if ($isLoggedIn): ?>
                    <div class="user-menu">
                        <div class="user-profile-dropdown">
                            <button id="profileDropdownBtn" class="user-profile-btn">
                                <div class="avatar">
                                    <?php 
                                        // Ambil inisial dari nama user
                                        $nameParts = explode(' ', $currentUser['name']);
                                        $initials = strtoupper(substr($nameParts[0], 0, 1));
                                        if (isset($nameParts[1])) {
                                            $initials .= strtoupper(substr($nameParts[1], 0, 1));
                                        }
                                        echo $initials;
                                    ?>
                                </div>
                                <span class="user-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                                <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <div id="profileDropdown" class="dropdown-menu">
                                <a href="<?php echo BASE_URL; ?>page/user/profile.php" class="dropdown-item">
                                    <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil Saya
                                </a>
                                <button id="logoutBtn" class="dropdown-item dropdown-item-logout">
                                    <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>page/auth/login.php" class="btn-login">Login</a>
                    <a href="<?php echo BASE_URL; ?>page/auth/register.php" class="btn-register">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if ($isLoggedIn): ?>
<script>
    // Toggle dropdown
    const profileDropdownBtn = document.getElementById('profileDropdownBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    profileDropdownBtn.addEventListener('click', function() {
        profileDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.user-profile-dropdown')) {
            profileDropdown.classList.remove('active');
        }
    });

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', async function() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            const formData = new FormData();
            formData.append('action', 'logout');

            try {
                const response = await fetch('<?php echo BASE_URL; ?>page/auth/process_auth.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = '<?php echo BASE_URL; ?>';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat logout');
            }
        }
    });
</script>
<?php endif; ?>