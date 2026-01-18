<?php
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'sportfield';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\Throwable $th) {
        die("Koneksi Gagal: " . $th->getMessage());
    }
?>