<?php
// Base URL Configuration - Dynamic based on domain
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Get REQUEST_URI and extract the base path
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME']; // /UAS/index.php, /index.php, or /page/user/lapangan.php

// Debug: uncomment untuk test
// echo "<!-- SCRIPT_NAME: $script_name -->";
// echo "<!-- REQUEST_URI: $request_uri -->";

// Strategy: Check if this is a sub-page (dalam folder page/)
// Jika SCRIPT_NAME dimulai dengan /page/, berarti kita akses dari dalam folder page
// Maka base_path seharusnya ke parent dari page, bukan ke page itu sendiri
$base_path = '/';

if (strpos($script_name, '/page/') !== false) {
    // Script berada di /page/xxx/xxx.php
    // Base path harus ke folder parent /page/, bukan ke /page/page/
    // Cari folder sebelum /page/
    preg_match('#^(.*?)/page/#', $script_name, $matches);
    if (!empty($matches[1])) {
        $base_path = $matches[1] . '/';
    }
} else if (preg_match('#^(/[^/]+)?/#', $script_name, $matches)) {
    // Untuk file di root atau /UAS/
    $base_path = !empty($matches[1]) ? $matches[1] . '/' : '/';
}

define('BASE_URL', $protocol . '://' . $host . $base_path);
define('ASSETS_URL', BASE_URL . 'assets/');
?>
    