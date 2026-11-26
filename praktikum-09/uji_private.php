<?php

require_once __DIR__ . '/Mahasiswa.php';

$m = new MahasiswaReguler('003', 'Budi', 'TI');

echo $m->nama;