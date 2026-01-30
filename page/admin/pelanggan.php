<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'page/auth/login.php');
    exit();
}

// Check if user is ADMIN
if (!Auth::isAdmin()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$currentUser = Auth::getUser();

// Set page-specific variables
$page_title = "Pelanggan";
$page_subtitle = "Kelola data pelanggan";

// Database connection
global $pdo;
require_once '../../config/db.php';

// Get customer statistics
$stat_total = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch()['total'];

// Get booking count per customer for "Aktif" status - HANYA ROLE USER
$stat_aktif = $pdo->query("SELECT COUNT(DISTINCT b.user_id) as total FROM booking b JOIN users u ON b.user_id = u.id WHERE u.role='user' AND b.status IN ('confirmed', 'completed')")->fetch()['total'];

// Get average rating
$stat_rating = $pdo->query("SELECT COALESCE(ROUND(AVG(rating), 1), 0) as avg_rating FROM ratings")->fetch()['avg_rating'];

// Get total reviews
$stat_reviews = $pdo->query("SELECT COUNT(*) as total FROM ratings")->fetch()['total'];

// Get all customers
$query = "SELECT u.*, 
                 COUNT(DISTINCT b.id) as total_bookings,
                 COALESCE(ROUND(AVG(r.rating), 1), 0) as avg_rating
          FROM users u
          LEFT JOIN booking b ON u.id = b.user_id
          LEFT JOIN ratings r ON b.id = r.booking_id
          WHERE u.role = 'user'
          GROUP BY u.id
          ORDER BY u.created_at DESC";

$stmt = $pdo->query($query);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Start output buffering for page content
ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Pelanggan</p>
                <p class="stat-value"><?php echo number_format($stat_total); ?></p>
                <p class="stat-period">Terdaftar</p>
            </div>
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Pelanggan Aktif</p>
                <p class="stat-value"><?php echo number_format($stat_aktif); ?></p>
                <p class="stat-change positive">Sudah melakukan booking</p>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Review</p>
                <p class="stat-value"><?php echo number_format($stat_reviews); ?></p>
                <p class="stat-change neutral">Dari booking selesai</p>
            </div>
            <div class="stat-icon orange">
                <i class="fas fa-comment"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-label">Rating Rata-rata</p>
                <p class="stat-value"><?php echo $stat_rating; ?></p>
                <p class="stat-change positive">Dari semua review</p>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="card-header">
        <h3>Daftar Pelanggan</h3>
        <div class="header-actions">
            <input type="text" id="searchInput" placeholder="Cari pelanggan..." class="search-input">
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Total Booking</th>
                    <th>Bergabung</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="customerTableBody">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <p class="text-muted">Belum ada data pelanggan</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer):
                        // Get initials
                        $name_parts = explode(' ', $customer['name']);
                        $initials = '';
                        foreach ($name_parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
                        
                        // Avatar color
                        $colors = ['blue', 'purple', 'green', 'orange'];
                        $color = $colors[$customer['id'] % 4];
                        
                        // Format date
                        $created_date = new DateTime($customer['created_at']);
                        $formatted_date = $created_date->format('d M Y');
                        
                        // Status based on booking
                        $is_active = $customer['total_bookings'] > 0 ? 'Aktif' : 'Inaktif';
                        $status_class = $customer['total_bookings'] > 0 ? 'success' : 'warning';
                    ?>
                    <tr data-id="<?php echo $customer['id']; ?>">
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar <?php echo $color; ?>"><?php echo $initials; ?></div>
                                <div>
                                    <p class="customer-name"><?php echo htmlspecialchars($customer['name']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                        <td>
                            <span class="booking-badge"><?php echo $customer['total_bookings']; ?> booking</span>
                        </td>
                        <td><?php echo $formatted_date; ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $is_active; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<link rel="stylesheet" href="../../assets/css/admin_style/pelanggan.css">
<script src="../../assets/js/admin_js/pelanggan.js"></script>
