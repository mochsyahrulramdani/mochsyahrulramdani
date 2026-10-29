<?php
declare(strict_types=1);

require_once __DIR__ . '/Buku.php';

header('Content-Type: text/plain; charset=utf-8');

// Data buku merupakan contoh untuk latihan.
$buku1 = new Buku('Dasar PHP', 'Andi', 2023);
$buku2 = new Buku('Belajar PBO', 'Budi', 2024);
$buku3 = new Buku('Pemrograman Web', 'Citra', 2025);

echo $buku1->tampilInfo() . PHP_EOL;
echo $buku2->tampilInfo() . PHP_EOL;
echo $buku3->tampilInfo() . PHP_EOL;