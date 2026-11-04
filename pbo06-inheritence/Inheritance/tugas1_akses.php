<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

class ContohParent
{
    private string $nim = '23001';
    protected string $kategori = 'Mahasiswa';

    public function getNim(): string
    {
        return $this->nim;
    }
}

class ContohChild extends ContohParent
{
    public function info(): string
    {
        return $this->getNim() . ' / ' . $this->kategori;
    }
}

$objek = new ContohChild();

echo "Akses dari method child:" . PHP_EOL;
echo $objek->info() . PHP_EOL;

foreach (['nim', 'kategori'] as $properti) {
    try {
        echo $objek->$properti;
    } catch (Error $e) {
        echo "Akses langsung {$properti} ditolak: "
            . $e->getMessage() . PHP_EOL;
    }
}