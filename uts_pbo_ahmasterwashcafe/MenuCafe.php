<?php

declare(strict_types=1);

// Memanggil file class induk.
require_once __DIR__ . '/ProdukCafe.php';

/**
 * Class MenuCafe
 * Berfungsi sebagai class turunan (child class)
 * dari class ProdukCafe.
 */

class MenuCafe extends ProdukCafe
{
    // ==========================================
    // 1. ATRIBUT KHUSUS MENU
    // ==========================================

    private string $kategori;
    private string $ukuran;
    private string $levelPedas;
    private string $statusMenu;
    private bool $rekomendasi;

    // ==========================================
    // 2. CONSTRUCTOR
    // ==========================================

    public function __construct(
        string $idProduk,
        string $namaProduk,
        int $harga,
        int $stok,
        string $deskripsi,
        string $kategori,
        string $ukuran,
        string $levelPedas,
        string $statusMenu,
        bool $rekomendasi
    ) {
        // Memanggil constructor class induk.
        parent::__construct(
            $idProduk,
            $namaProduk,
            $harga,
            $stok,
            $deskripsi
        );

        // Mengisi atribut class MenuCafe.
        $this->kategori = $kategori;
        $this->ukuran = $ukuran;
        $this->levelPedas = $levelPedas;

        if (!$this->ubahStatusMenu($statusMenu)) {
            throw new InvalidArgumentException(
                'Status menu tidak valid.'
            );
        }

        $this->rekomendasi = $rekomendasi;
    }

    // ==========================================
    // 3. METHOD GETTER
    // ==========================================

    public function getKategori(): string
    {
        return $this->kategori;
    }

    public function getUkuran(): string
    {
        return $this->ukuran;
    }

    public function getLevelPedas(): string
    {
        return $this->levelPedas;
    }

    public function getStatusMenu(): string
    {
        return $this->statusMenu;
    }

    public function getRekomendasi(): bool
    {
        return $this->rekomendasi;
    }

    // ==========================================
    // 4. METHOD KHUSUS MENU
    // ==========================================

    // Memeriksa ketersediaan pesanan.
    public function cekKetersediaanPesanan(
        int $jumlah
    ): bool {
        return strtolower($this->statusMenu) === 'tersedia'
            && $this->cekStok($jumlah);
    }

    // Menghitung subtotal pesanan.
    public function hitungSubtotal(int $jumlah): int
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException(
                'Jumlah harus lebih dari nol.'
            );
        }

        return $this->hitungHarga() * $jumlah;
    }

    // Mengubah status menu.
    public function ubahStatusMenu(string $status): bool
    {
        $status = ucfirst(
            strtolower(
                trim($status)
            )
        );

        if (
            !in_array(
                $status,
                ['Tersedia', 'Tidak tersedia'],
                true
            )
        ) {
            return false;
        }

        $this->statusMenu = $status;

        return true;
    }

    // Menampilkan detail menu.
    public function tampilkanDetailMenu(): string
    {
        $statusRekomendasi = $this->rekomendasi
            ? 'Menu Rekomendasi'
            : 'Menu Biasa';

        return
            "Nama Menu: " . $this->getNamaProduk() . "\n" .
            "Kategori: " . $this->kategori . "\n" .
            "Harga: Rp" . number_format(
                $this->getHarga(),
                0,
                ',',
                '.'
            ) . "\n" .
            "Stok: " . $this->getStok() . "\n" .
            "Ukuran: " . $this->ukuran . "\n" .
            "Level Pedas: " . $this->levelPedas . "\n" .
            "Status: " . $this->statusMenu . "\n" .
            "Keterangan: " . $statusRekomendasi;
    }
}