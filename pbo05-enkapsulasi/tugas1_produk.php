<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

class Produk
{
    public string $nama;
    protected string $kategori;
    private int $harga;

    public function __construct(
        string $nama,
        string $kategori,
        int $harga
    ) {
        $this->nama = $nama;
        $this->kategori = $kategori;
        $this->harga = $harga;
    }

    public function getHarga(): int
    {
        return $this->harga;
    }
}

class ProdukDigital extends Produk
{
    public function getKategori(): string
    {
        return $this->kategori;
    }
}

$produk = new ProdukDigital(
    'Modul PHP',
    'Buku Digital',
    50000
);

echo "Akses public:" . PHP_EOL;
echo $produk->nama . PHP_EOL;

echo PHP_EOL . "Akses protected dari luar class:" . PHP_EOL;

try {
    echo $produk->kategori;
} catch (Error $e) {
    echo "Ditolak: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Akses private dari luar class:" . PHP_EOL;

try {
    echo $produk->harga;
} catch (Error $e) {
    echo "Ditolak: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Akses protected melalui subclass:" . PHP_EOL;
echo $produk->getKategori() . PHP_EOL;

echo PHP_EOL . "Akses harga melalui getter:" . PHP_EOL;
echo $produk->getHarga() . PHP_EOL;