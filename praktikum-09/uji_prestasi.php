<?php

require_once __DIR__ . '/Mahasiswa.php';

$m = buatMahasiswa([
    'nim' => '005',
    'nama' => 'Dina',
    'prodi' => 'Informatika',
    'jenis' => 'prestasi'
]);

echo $m->tampil('lengkap') . '<br>';
echo $m->jenis() . '<br>';
echo tampilUKT($m);