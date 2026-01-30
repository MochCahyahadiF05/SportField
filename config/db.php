<?php
    // Check if running on localhost or hosting
    $is_localhost = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
    
    if ($is_localhost) {
        // Localhost    
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $db = 'sportfield';
    } else {
        // Hosting
        $host = 'sql102.infinityfree.com';
        $user = 'if0_41030821';
        $pass = 'fob7fU84IAh';
        $db = 'if0_41030821_sportfield';
    }

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\Throwable $th) {
        die("Koneksi Gagal: " . $th->getMessage());
    }
?>