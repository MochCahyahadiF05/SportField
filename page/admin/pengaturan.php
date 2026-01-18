<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';

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
$page_title = "Pengaturan";
$page_subtitle = "Konfigurasi sistem dan preferensi";

// Start output buffering for page content
ob_start();
?>

<div style="max-width: 800px; margin: 0 auto;">
    <!-- Settings Tabs -->
    <div class="settings-tabs" style="display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #e5e7eb;">
        <button class="settings-tab-btn active" style="padding: 12px 24px; border: none; background: none; cursor: pointer; border-bottom: 3px solid #16A34A; color: #16A34A; font-weight: 600;">
            <i class="fas fa-cog"></i> Umum
        </button>
        <button class="settings-tab-btn" style="padding: 12px 24px; border: none; background: none; cursor: pointer; color: #6b7280; font-weight: 600;">
            <i class="fas fa-bell"></i> Notifikasi
        </button>
        <button class="settings-tab-btn" style="padding: 12px 24px; border: none; background: none; cursor: pointer; color: #6b7280; font-weight: 600;">
            <i class="fas fa-lock"></i> Keamanan
        </button>
    </div>

    <!-- Settings Content -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px;">
        <!-- General Settings -->
        <div class="settings-content">
            <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 24px;">Pengaturan Umum</h3>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Nama Admin</label>
                <input type="text" value="<?php echo htmlspecialchars($currentUser['name']); ?>" style="width: 100%; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 14px;" readonly>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Email</label>
                <input type="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" style="width: 100%; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 14px;" readonly>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Telepon</label>
                <input type="tel" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" style="width: 100%; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Zona Waktu</label>
                <select style="width: 100%; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 14px; background: #f9fafb;">
                    <option>Asia/Jakarta (UTC+7)</option>
                    <option>Asia/Bangkok (UTC+7)</option>
                    <option>Asia/Singapore (UTC+8)</option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Bahasa</label>
                <select style="width: 100%; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 14px; background: #f9fafb;">
                    <option>Indonesia</option>
                    <option>English</option>
                </select>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button style="padding: 10px 24px; background: linear-gradient(to right, #16A34A, #22c55e); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    Simpan Perubahan
                </button>
                <button style="padding: 10px 24px; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();

// Include the main admin layout (includes all shared CSS)
include '../includes/admin-layout.php';
?>

<script src="../../assets/js/admin_js/dashboard.js"></script>
