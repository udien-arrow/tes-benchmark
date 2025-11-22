<?php
echo "=== PHP Benchmark Test ===\n";

// Ambil nilai iterasi dari URL, default 100000
$iterations = isset($_GET['iter']) ? (int)$_GET['iter'] : 100000;
echo "Jumlah iterasi: $iterations\n\n";

// Fungsi benchmark
function benchmark($name, $callback, $iterations) {
    $start = microtime(true);
    $callback($iterations);
    $end = microtime(true);
    $duration = $end - $start;
    echo sprintf("%-25s : %.4f seconds\n", $name, $duration);
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

benchmark("String concatenation", function($n) {
    $str = "";
    for ($i = 0; $i < $n; $i++) {
        $str .= "a";
    }
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

echo "\n=== Benchmark Selesai ===\n";
?>
