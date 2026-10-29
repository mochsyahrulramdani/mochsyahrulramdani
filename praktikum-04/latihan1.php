<?php
declare(strict_types=1);

require_once __DIR__ . '/Mahasiswa.php';

header('Content-Type: text/plain; charset=utf-8');

// Objek pertama menggunakan fakultas yang diberikan.
$mhs1 = new Mahasiswa(
    nim: '001',
    nama: 'Syahrul',
    prodi: 'Informatika',
    asalSekolah: 'SMA 1',
    semester: 3,
    hobi: 'Membaca',
    agama: 'Islam',
    fakultas: 'Sains dan Teknologi'
);

// Objek kedua menggunakan nilai default fakultas.
$mhs2 = new Mahasiswa(
    nim: '002',
    nama: 'Rava',
    prodi: 'Informatika',
    asalSekolah: 'SMA 2',
    semester: 3,
    hobi: 'Olahraga',
    agama: 'Islam'
);

echo $mhs1->ringkasan() . PHP_EOL;
echo "Fakultas: {$mhs1->fakultas}" . PHP_EOL;

echo PHP_EOL;

echo $mhs2->ringkasan() . PHP_EOL;
echo "Fakultas: {$mhs2->fakultas}" . PHP_EOL;