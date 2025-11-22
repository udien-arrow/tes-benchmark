<?php
// Tentukan apakah skrip dijalankan dari Command Line (CLI) atau Browser
$is_cli = (php_sapi_name() === 'cli');

// Fungsi untuk mencetak output dengan format yang sesuai
function write_line($line) {
    global $is_cli;
    echo $line . ($is_cli ? "\n" : "<br>");
}

// Fungsi untuk format header
function write_header($text) {
    global $is_cli;
    if ($is_cli) {
        echo "\n" . str_repeat("=", 40) . "\n";
        echo $text . "\n";
        echo str_repeat("=", 40) . "\n";
    } else {
        echo "<h2>" . htmlspecialchars($text) . "</h2>";
    }
}

// Set header untuk browser agar output terlihat seperti teks biasa
if (!$is_cli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>PHP Benchmark Test</title><style>body { font-family: monospace; }</style></head><body>';
}

write_header("PHP Benchmark Test");

// Ambil nilai iterasi dari URL, default 100000
$iterations = isset($_GET['iter']) ? (int)$_GET['iter'] : 100000;
write_line("Jumlah iterasi: " . number_format($iterations));
write_line("");

// Muat dan jalankan file koneksi database
require_once 'db_connection.php';

// Fungsi benchmark
function benchmark($name, $callback, $iterations) {
    $start = microtime(true);
    $mem_start = memory_get_usage();
    $callback($iterations);
    $mem_end = memory_get_usage();
    $end = microtime(true);
    
    $duration = $end - $start;
    $mem_used = $mem_end - $mem_start;

    $output = sprintf(
        "%-30s : %8.4f detik | Memori: %s",
        $name,
        $duration,
        number_format($mem_used / 1024) . " KB"
    );
    write_line($output);
}

// Tes benchmark
benchmark("Empty loop", function($n) {
    for ($i = 0; $i < $n; $i++) {}
}, $iterations);
benchmark("Math operations", function($n) {
    $x = 0;
    for ($i = 0; $i < $n; $i++) {
        $x += sqrt($i);
    }
}, $iterations);
benchmark("String concatenation (bad)", function($n) {
    $str = "";
    for ($i = 0; $i < $n; $i++) {
        $str .= "a";
    }
}, $iterations);
benchmark("String with implode (good)", function($n) {
    $arr = [];
    for ($i = 0; $i < $n; $i++) {
        $arr[] = "a";
    }
    $str = implode('', $arr);
}, $iterations);
benchmark("Array push", function($n) {
    $arr = [];
    for ($i = 0; $i < $n; $i++) {
        $arr[] = $i;
    }
}, $iterations);
benchmark("MD5 hashing", function($n) {
    for ($i = 0; $i < $n; $i++) {
        md5($i);
    }
}, $iterations);

if ($mysqli_conn) {
    write_line("Jumlah iterasi database: " . number_format($db_iterations));
    write_line("");

    benchmark("DB: Repeated Connections", function($n) use ($db_config) {
        for ($i = 0; $i < $n; $i++) {
            // Membuat koneksi baru di setiap iterasi
            $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['dbname']);
            // Menutup koneksi
            if ($conn) $conn->close();
        }
    }, $db_iterations);

    benchmark("DB: Simple Query (SELECT 1)", function($n) use ($mysqli_conn) {
        for ($i = 0; $i < $n; $i++) {
            // Menggunakan koneksi yang sudah ada
            $mysqli_conn->query("SELECT 1");
        }
    }, $db_iterations);
}

write_header("Benchmark Selesai");

if (!$is_cli) {
    echo '</body></html>';
}
?>
