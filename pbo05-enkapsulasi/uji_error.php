<?php
declare(strict_types=1);

// Memuat class Mahasiswa.
require_once __DIR__ . '/Mahasiswa.php';

// Menampilkan output sebagai teks di browser.
header('Content-Type: text/plain; charset=utf-8');

// Membuat objek mahasiswa dengan semester awal 4.
$m = new Mahasiswa(
    '23001',
    'Andi',
    'Sains dan Teknologi',
    'Informatika',
    'SMAN 1',
    4,
    'Membaca',
    'Islam'
);

// ==========================================
// 1. PENGUJIAN AKSES PRIVATE
// ==========================================

echo "1. Pengujian akses private" . PHP_EOL;

try {
    // Sengaja mengakses properti private secara langsung.
    echo $m->semester;
} catch (Error $e) {
    echo "Akses ditolak: " . $e->getMessage() . PHP_EOL;
}

// Cara yang benar: membaca melalui getter.
echo "Semester melalui getter: "
    . $m->getSemester() . PHP_EOL;

echo PHP_EOL;

// ==========================================
// 2. PENGUJIAN VALIDASI SEMESTER
// ==========================================

echo "2. Pengujian validasi semester" . PHP_EOL;

echo "Semester sebelum perubahan: "
    . $m->getSemester() . PHP_EOL;

try {
    // Sengaja memberikan nilai di luar rentang 1–14.
    $m->setSemester(15);
} catch (InvalidArgumentException $e) {
    echo "Input ditolak: " . $e->getMessage() . PHP_EOL;
}

echo "Semester setelah pengujian: "
    . $m->getSemester() . PHP_EOL;