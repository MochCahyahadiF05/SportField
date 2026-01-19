<?php
/**
 * Migration Script untuk Update Pembayaran Table
 * Jalankan script ini di browser atau command line untuk update database
 */

require_once 'config/config.php';
require_once 'config/db.php';

global $pdo;

echo "Starting migration...\n\n";

try {
    // Check if refund_reason column exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM pembayaran LIKE 'refund_reason'")->fetch();
    
    if (!$checkColumn) {
        // Add refund_reason column
        $sql = "ALTER TABLE pembayaran ADD COLUMN `refund_reason` text DEFAULT NULL AFTER `bukti_bayar`";
        $pdo->exec($sql);
        echo "✅ Added refund_reason column to pembayaran table\n";
    } else {
        echo "⚠️  refund_reason column already exists\n";
    }
    
    // Check if cancelled status exists in enum
    $checkEnum = $pdo->query("SHOW COLUMNS FROM pembayaran WHERE Field='status'")->fetch();
    $enumType = $checkEnum['Type'];
    
    if ($checkEnum && strpos($enumType, 'cancelled') === false) {
        // Update the enum to include 'cancelled' and 'refunded'
        $sql = "ALTER TABLE pembayaran MODIFY COLUMN `status` enum('pending','success','failed','cancelled','refunded') DEFAULT 'pending'";
        $pdo->exec($sql);
        echo "✅ Updated status enum to include 'cancelled' and 'refunded'\n";
    } else if ($checkEnum && strpos($enumType, 'refunded') === false) {
        // Only add refunded
        $sql = "ALTER TABLE pembayaran MODIFY COLUMN `status` enum('pending','success','failed','cancelled','refunded') DEFAULT 'pending'";
        $pdo->exec($sql);
        echo "✅ Updated status enum to include 'refunded'\n";
    } else {
        echo "⚠️  'cancelled' and 'refunded' status already exist in enum\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nCurrent enum values:\n";
    echo "- pending (Menunggu verifikasi)\n";
    echo "- success (Terverifikasi)\n";
    echo "- failed (Ditolak)\n";
    echo "- cancelled (Dibatalkan)\n";
    echo "- refunded (Refund)\n";
    echo "\nChanges made:\n";
    echo "1. Added 'refund_reason' column to pembayaran table\n";
    echo "2. Updated status enum to include 'cancelled' and 'refunded' options\n";
    echo "\nYou can now use the full refund functionality in the admin panel.\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "\nTry running this SQL manually:\n";
    echo "ALTER TABLE pembayaran ADD COLUMN `refund_reason` text DEFAULT NULL AFTER `bukti_bayar`;\n";
    echo "ALTER TABLE pembayaran MODIFY COLUMN `status` enum('pending','success','failed','cancelled','refunded') DEFAULT 'pending';\n";
    exit(1);
}
?>
