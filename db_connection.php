<?php
// File: db_connection.php

// --- Konfigurasi dan Koneksi Database (MySQLi) ---
$db_config = [
    'host' => '127.0.0.1', // atau 'localhost'
    'dbname' => 'test',      // Ganti dengan nama database Anda yang ada
    'user' => 'root',      // Ganti dengan user database Anda
    'pass' => ''          // Ganti dengan password Anda
];

// Iterasi untuk database lebih sedikit karena lebih lambat
$db_iterations = isset($_GET['db_iter']) ? (int)$_GET['db_iter'] : 1000;
$mysqli_conn = null;

// Nonaktifkan pelaporan error sementara agar tidak menampilkan warning jika koneksi gagal
mysqli_report(MYSQLI_REPORT_OFF);

$mysqli_conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['dbname']);

if ($mysqli_conn->connect_error) {
    write_line("!!! KONEKSI DATABASE GAGAL (mysqli): " . htmlspecialchars($mysqli_conn->connect_error));
    write_line("!!! Tes benchmark database akan dilewati.");
    $mysqli_conn = null; // Pastikan null jika gagal
} else {
    $mysqli_conn->set_charset("utf8mb4");
    write_line("Koneksi database (mysqli) berhasil.");
}
write_line("");