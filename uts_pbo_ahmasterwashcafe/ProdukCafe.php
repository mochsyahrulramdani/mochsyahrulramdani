<?php

declare(strict_types=1);

/**
 * Class ProdukCafe
 * Berfungsi sebagai class induk (parent class)
 * untuk menyimpan data dasar produk cafe.
 */

class ProdukCafe
{
    // ==========================================
    // 1. ATRIBUT (ENCAPSULATION)
    // ==========================================

    private string $idProduk;
    private string $namaProduk;
    private int $harga;
    private int $stok;
    private string $deskripsi;

    // ==========================================
    // 2. CONSTRUCTOR
    // ==========================================

    public function __construct(
        string $idProduk,
        string $namaProduk,
        int $harga,
        int $stok,
        string $deskripsi
    ) {
        if (
            trim($idProduk) === '' ||
            trim($namaProduk) === ''
        ) {
            throw new InvalidArgumentException(
                'ID dan nama produk wajib diisi.'
            );
        }

        if (
            $harga < 1 ||
            $harga > 1000000 ||
            $stok < 0 ||
            $stok > 100000
        ) {
            throw new InvalidArgumentException(
                'Harga atau stok produk tidak valid.'
            );
        }

        $this->idProduk = trim($idProduk);
        $this->namaProduk = trim($namaProduk);
        $this->harga = $harga;
        $this->stok = $stok;
        $this->deskripsi = $deskripsi;
    }

    // ==========================================
    // 3. METHOD GETTER
    // ==========================================

    public function getIdProduk(): string
    {
        return $this->idProduk;
    }

    public function getNamaProduk(): string
    {
        return $this->namaProduk;
    }

    public function getHarga(): int
    {
        return $this->harga;
    }

    public function getStok(): int
    {
        return $this->stok;
    }

    public function getDeskripsi(): string
    {
        return $this->deskripsi;
    }

    // ==========================================
    // 4. METHOD PENGELOLAAN PRODUK
    // ==========================================

    // Mengambil harga produk.
    public function hitungHarga(): int
    {
        return $this->harga;
    }

    // Memeriksa ketersediaan stok.
    public function cekStok(int $jumlah = 1): bool
    {
        return $jumlah > 0 &&
            $this->stok >= $jumlah;
    }

    // Mengurangi stok produk.
    public function kurangiStok(int $jumlah): bool
    {
        if (!$this->cekStok($jumlah)) {
            return false;
        }

        $this->stok -= $jumlah;

        return true;
    }

    // Menambah stok produk.
    public function tambahStok(int $jumlah): bool
    {
        if (
            $jumlah <= 0 ||
            $jumlah > 100000 - $this->stok
        ) {
            return false;
        }

        $this->stok += $jumlah;

        return true;
    }
} 