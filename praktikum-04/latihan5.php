<?php
declare(strict_types=1);

require_once __DIR__ . '/Mahasiswa.php';

header('Content-Type: text/plain; charset=utf-8');

$mhs = new Mahasiswa(
    nim: '001',
    nama: 'Syahrul',
    prodi: 'Informatika',
    asalSekolah: 'SMA 1',
    semester: 3,
    hobi: 'Membaca',
    agama: 'Islam'
);

echo "A. Method yang diizinkan" . PHP_EOL;
echo $mhs->profilSingkat() . PHP_EOL;

echo PHP_EOL . "B. Method yang tidak diizinkan" . PHP_EOL;

try {
    $mhs->tampilAlamat();
} catch (BadMethodCallException $e) {
    echo "Jenis exception: " . get_class($e) . PHP_EOL;
    echo "Pesan: " . $e->getMessage() . PHP_EOL;
}

echo "Program tetap berjalan." . PHP_EOL; 