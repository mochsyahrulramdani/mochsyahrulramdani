<?php

require_once __DIR__ . '/Mahasiswa.php';

$m = new Mahasiswa('003', 'Budi', 'TI');

echo $m->nama;

echo $m->getNama();