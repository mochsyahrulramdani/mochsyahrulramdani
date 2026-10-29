<?php
declare(strict_types=1);

require_once __DIR__ . '/Latihan.php';

header('Content-Type: text/plain; charset=utf-8');

$objek = new Latihan();

// Memicu __set().
$objek->hobi = 'Membaca';

// Memicu __get().
$hobi = $objek->hobi;
echo "Hobi: {$hobi}" . PHP_EOL;

// Memicu __isset().
$tersedia = isset($objek->hobi);
echo "Hobi tersedia: "
    . ($tersedia ? 'Ya' : 'Tidak') . PHP_EOL;

// Memicu __unset().
unset($objek->hobi);

// Memeriksa hasil penghapusan.
$tersedia = isset($objek->hobi);
echo "Hobi setelah dihapus: "
    . ($tersedia ? 'Ya' : 'Tidak') . PHP_EOL;