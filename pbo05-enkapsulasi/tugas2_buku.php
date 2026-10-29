<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

class Buku
{
    private string $kodeBuku;
    private string $judul;
    private int $stok;

    public function __construct(
        string $kodeBuku,
        string $judul,
        int $stok
    ) {
        $kodeBuku = trim($kodeBuku);

        if ($kodeBuku === '') {
            throw new InvalidArgumentException(
                'Kode buku wajib diisi.'
            );
        }

        if ($stok < 0) {
            throw new InvalidArgumentException(
                'Stok awal tidak boleh negatif.'
            );
        }

        $this->kodeBuku = $kodeBuku;
        $this->setJudul($judul);
        $this->stok = $stok;
    }

    public function getKodeBuku(): string
    {
        return $this->kodeBuku;
    }

    public function getJudul(): string
    {
        return $this->judul;
    }

    public function getStok(): int
    {
        return $this->stok;
    }

    public function setJudul(string $judul): void
    {
        $judul = trim($judul);

        if ($judul === '') {
            throw new InvalidArgumentException(
                'Judul wajib diisi.'
            );
        }

        $this->judul = $judul;
    }

    public function tambahStok(int $jumlah): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException(
                'Jumlah tambahan stok harus positif.'
            );
        }

        $this->stok += $jumlah;
    }

    public function kurangiStok(int $jumlah): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException(
                'Jumlah pengurangan stok harus positif.'
            );
        }

        if ($jumlah > $this->stok) {
            throw new InvalidArgumentException(
                'Stok tidak mencukupi.'
            );
        }

        $this->stok -= $jumlah;
    }
}

function uji(string $keterangan, callable $aksi): void
{
    echo $keterangan . PHP_EOL;

    try {
        $aksi();
        echo "Hasil: diterima." . PHP_EOL;
    } catch (InvalidArgumentException $e) {
        echo "Hasil: ditolak. "
            . $e->getMessage() . PHP_EOL;
    }

    echo PHP_EOL;
}

$buku = new Buku('BK001', 'Belajar PHP', 10);

echo "Kode buku: " . $buku->getKodeBuku() . PHP_EOL;
echo "Judul: " . $buku->getJudul() . PHP_EOL;
echo "Stok awal: " . $buku->getStok() . PHP_EOL;
echo PHP_EOL;

// Pengujian input valid.
uji('1. Menambah stok sebanyak 5', function () use ($buku) {
    $buku->tambahStok(5);
    echo "Stok sekarang: " . $buku->getStok() . PHP_EOL;
});

uji('2. Mengurangi stok sebanyak 3', function () use ($buku) {
    $buku->kurangiStok(3);
    echo "Stok sekarang: " . $buku->getStok() . PHP_EOL;
});

uji('3. Mengubah judul', function () use ($buku) {
    $buku->setJudul('Pemrograman PHP');
    echo "Judul: " . $buku->getJudul() . PHP_EOL;
});

// Pengujian input tidak valid.
uji('4. Judul hanya berisi spasi', function () use ($buku) {
    $buku->setJudul('   ');
});

uji('5. Penambahan stok negatif', function () use ($buku) {
    $buku->tambahStok(-2);
});

uji('6. Pengurangan melebihi stok', function () use ($buku) {
    $buku->kurangiStok(20);
});

uji('7. Stok awal negatif', function () {
    new Buku('BK002', 'Dasar PBO', -1);
});

echo "Data akhir:" . PHP_EOL;
echo "Judul: " . $buku->getJudul() . PHP_EOL;
echo "Stok: " . $buku->getStok() . PHP_EOL;