<?php
declare(strict_types=1);

require_once __DIR__ . '/DataMahasiswa.php';

header('Content-Type: text/plain; charset=utf-8');

function cek(bool $kondisi, string $pesan): void
{
    echo ($kondisi ? '[LULUS] ' : '[GAGAL] ')
        . $pesan . PHP_EOL;
}

function ditolak(
    string $label,
    callable $aksi,
    string $jenisException
): void {
    echo PHP_EOL . $label . PHP_EOL;

    try {
        $aksi();
        echo "[GAGAL] Operasi seharusnya ditolak."
            . PHP_EOL;
    } catch (Throwable $e) {
        cek(
            $e instanceof $jenisException,
            'Jenis error: ' . get_class($e)
        );

        echo "Pesan: " . $e->getMessage() . PHP_EOL;
    }
}

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

echo "PENGUJIAN MODEL MAHASISWA" . PHP_EOL;

// Nomor 6: akses langsung properti private.
ditolak(
    '1. Akses langsung properti semester',
    function () use ($m) {
        echo $m->semester;
    },
    Error::class
);

// Nomor 2: NIM kosong.
ditolak(
    '2. NIM kosong',
    function () {
        new Mahasiswa(
            '',
            'Budi',
            'Sains dan Teknologi',
            'Informatika',
            'SMAN 2',
            4,
            '',
            'Islam'
        );
    },
    InvalidArgumentException::class
);

// Nomor 3: semester negatif.
ditolak(
    '3. Semester -1',
    function () use ($m) {
        $m->setSemester(-1);
    },
    InvalidArgumentException::class
);

cek(
    $m->getSemester() === 4,
    'Semester tetap 4.'
);

// Nomor 4: semester melebihi batas.
ditolak(
    '4. Semester 15',
    function () use ($m) {
        $m->setSemester(15);
    },
    InvalidArgumentException::class
);

cek(
    $m->getSemester() === 4,
    'Semester tetap 4.'
);

// Nomor 5: nama kosong dan nama berisi spasi.
foreach (['', '   '] as $namaTidakValid) {
    ditolak(
        '5. Nama kosong atau spasi: '
            . var_export($namaTidakValid, true),
        function () use ($m, $namaTidakValid) {
            $m->setNama($namaTidakValid);
        },
        InvalidArgumentException::class
    );

    cek(
        $m->getNama() === 'Andi',
        'Nama tetap Andi.'
    );
}

// Nomor 11: agama di luar daftar.
ditolak(
    '6. Agama tidak terdaftar',
    function () use ($m) {
        $m->setAgama('Lainnya');
    },
    InvalidArgumentException::class
);

cek(
    $m->getAgama() === 'Islam',
    'Agama tetap Islam.'
);

// Nomor 12: mengganti objek dengan NIM berbeda.
echo PHP_EOL . '7. Objek pengganti dengan NIM berbeda'
    . PHP_EOL;

$repo = new DataMahasiswa();
$repo->tambah($m);

$pengganti = new Mahasiswa(
    '99999',
    'Citra',
    'Sains dan Teknologi',
    'Informatika',
    'SMAN 3',
    5,
    '',
    'Islam'
);

$berhasil = $repo->ubah('23001', $pengganti);

cek(
    $berhasil === false,
    'Penggantian dengan NIM berbeda ditolak.'
);

cek(
    $repo->cari('23001') === $m,
    'Objek lama tetap tersimpan.'
);

cek(
    $repo->cari('99999') === null,
    'NIM pengganti tidak ditambahkan.'
);

echo PHP_EOL . 'DATA AKHIR OBJEK' . PHP_EOL;
echo 'NIM: ' . $m->getNim() . PHP_EOL;
echo 'Nama: ' . $m->getNama() . PHP_EOL;
echo 'Semester: ' . $m->getSemester() . PHP_EOL;
echo 'Agama: ' . $m->getAgama() . PHP_EOL;