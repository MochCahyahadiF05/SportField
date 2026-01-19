<?php
/**
 * Konfigurasi Sistem Booking SportField
 * File ini berisi setting-setting yang bisa di-customize
 */

// ===== PAYMENT CONFIGURATION =====
const PAYMENT_METHODS = [
    'transfer' => [
        'name' => 'Transfer Bank',
        'icon' => '🏦',
        'banks' => [
            'bca' => [
                'name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'SportField Indonesia',
                'branch' => 'Jakarta'
            ]
        ]
    ],
    'qris' => [
        'name' => 'QRIS',
        'icon' => '📱',
        'description' => 'Scan QRIS dengan aplikasi pembayaran Anda',
        'qris_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=00020126360014com.midtrans.www' // Generate custom untuk production
    ]
];

// ===== FILE UPLOAD CONFIGURATION =====
const FILE_UPLOAD = [
    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
    'max_size_mb' => 5,
    'upload_path' => 'assets/img/bukti-pembayaran/',
    'generate_filename' => true // Gunakan timestamp + original name
];

// ===== BOOKING CONFIGURATION =====
const BOOKING = [
    'min_hours_advance' => 1, // Minimal booking 1 jam sebelumnya (optional)
    'max_duration_hours' => 4, // Maksimal durasi booking 4 jam
    'price_per_hour_base' => 100000, // Base harga per jam (bisa berbeda per lapangan)
    'currency' => 'IDR',
    'business_hours_start' => 6,
    'business_hours_end' => 23
];

// ===== EMAIL CONFIGURATION (untuk future)
const EMAIL = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'noreply@sportfield.local',
    'smtp_password' => 'password-here',
    'from_address' => 'noreply@sportfield.local',
    'from_name' => 'SportField',
    // Recipient
    'admin_email' => 'admin@sportfield.local',
    'send_on_booking' => true,
    'send_on_payment_success' => true,
    'send_on_payment_fail' => true
];

// ===== SMS CONFIGURATION (untuk future)
const SMS = [
    'provider' => 'twilio', // or 'nexmo', 'aws-sns'
    'api_key' => 'your-api-key',
    'api_secret' => 'your-api-secret',
    'from_number' => '+1234567890',
    'enabled' => false
];

// ===== DATABASE CONFIGURATION =====
const DATABASE = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'name' => 'sportfield',
    'charset' => 'utf8mb4'
];

// ===== SECURITY CONFIGURATION =====
const SECURITY = [
    'session_timeout' => 1800, // 30 minutes
    'password_min_length' => 8,
    'enable_https_only' => false, // Set to true in production
    'csrf_token_enabled' => true,
    'max_file_upload_attempts' => 3,
    'rate_limit_enabled' => false, // Set to true in production
    'rate_limit_requests_per_minute' => 60
];

// ===== API CONFIGURATION =====
const API = [
    'payment_gateway' => 'manual', // 'manual', 'midtrans', 'xendit', etc
    'payment_gateway_api_key' => 'your-api-key',
    'enable_webhooks' => false,
    'webhook_secret' => 'your-webhook-secret'
];

// ===== ADMIN CONFIGURATION =====
const ADMIN = [
    'auto_approve_payment' => false, // Require manual approval
    'approval_timeout_hours' => 24, // Auto-cancel jika tidak approve dalam 24 jam
    'send_admin_notification' => true,
    'require_payment_verification' => true
];

// ===== LOGGING CONFIGURATION =====
const LOGGING = [
    'enabled' => true,
    'level' => 'INFO', // 'DEBUG', 'INFO', 'WARNING', 'ERROR'
    'log_path' => 'logs/',
    'max_log_size_mb' => 10,
    'retention_days' => 30
];

// ===== FEATURE FLAGS =====
const FEATURES = [
    'booking_enabled' => true,
    'payment_enabled' => true,
    'admin_approval_required' => true,
    'user_rating_enabled' => false, // Future feature
    'referral_program_enabled' => false, // Future feature
    'loyalty_points_enabled' => false, // Future feature
    'promotional_codes_enabled' => false // Future feature
];

// ===== ENVIRONMENT =====
const ENVIRONMENT = [
    'mode' => 'development', // 'development', 'production'
    'debug' => true, // Set to false in production
    'display_errors' => true, // Set to false in production
    'log_errors' => true,
    'timezone' => 'Asia/Jakarta'
];

// ===== HELPER FUNCTIONS =====

/**
 * Get payment method details
 */
function getPaymentMethodDetails($method) {
    return PAYMENT_METHODS[$method] ?? null;
}

/**
 * Get bank details for transfer
 */
function getBankDetails($bank_code) {
    return PAYMENT_METHODS['transfer']['banks'][$bank_code] ?? null;
}

/**
 * Validate file upload
 */
function validateFileUpload($file) {
    $config = FILE_UPLOAD;
    
    // Check file type
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $config['allowed_types'])) {
        return ['valid' => false, 'message' => 'Tipe file tidak didukung'];
    }
    
    // Check file size
    $size_mb = $file['size'] / 1024 / 1024;
    if ($size_mb > $config['max_size_mb']) {
        return ['valid' => false, 'message' => 'Ukuran file terlalu besar'];
    }
    
    return ['valid' => true];
}

/**
 * Generate filename untuk uploaded file
 */
function generateUploadFilename($original_filename) {
    $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
    return time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($original_filename, '.' . $ext)) . '.' . $ext;
}

/**
 * Format price ke IDR currency
 */
function formatPrice($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get booking hours
 */
function getBusinessHours() {
    $start = BOOKING['business_hours_start'];
    $end = BOOKING['business_hours_end'];
    $hours = [];
    
    for ($i = $start; $i < $end; $i++) {
        $hours[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
    }
    
    return $hours;
}

// ===== ENVIRONMENT SETUP =====
date_default_timezone_set(ENVIRONMENT['timezone']);

if (ENVIRONMENT['mode'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', ENVIRONMENT['display_errors'] ? 1 : 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

?>
