<?php
declare(strict_types=1);

require_once __DIR__ . '/Buku.php';

define('DEMO_BUKU', true);

header('Content-Type: text/plain; charset=utf-8');

echo "A. Objek tanpa alias" . PHP_EOL;

$buku1 = new Buku('Dasar PHP', 'Andi', 2023);
echo $buku1->tampilInfo() . PHP_EOL;

echo "Menghapus variabel buku1." . PHP_EOL;
unset($buku1);

echo "Selesai menghapus buku1." . PHP_EOL;

echo PHP_EOL . "B. Objek dengan alias" . PHP_EOL;

$buku2 = new Buku('Belajar PBO', 'Budi', 2024);

// Kedua variabel menunjuk objek yang sama.
$aliasBuku = $buku2;

echo "Menghapus variabel buku2." . PHP_EOL;
unset($buku2);

echo "Objek masih dapat diakses melalui alias:" . PHP_EOL;
echo $aliasBuku->tampilInfo() . PHP_EOL;

echo "Menghapus alias terakhir." . PHP_EOL;
unset($aliasBuku);

echo "Selesai menghapus alias." . PHP_EOL;