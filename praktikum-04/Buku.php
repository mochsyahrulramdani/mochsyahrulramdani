<?php
declare(strict_types=1);

class Buku
{
    public function __construct(
        public string $judul,
        public string $penulis,
        public int $tahun
    ) {
    }

    public function tampilInfo(): string
    {
        return "Judul: {$this->judul}"
            . " | Penulis: {$this->penulis}"
            . " | Tahun: {$this->tahun}";
    }

    public function __destruct()
    {
        if (defined('DEMO_BUKU') && DEMO_BUKU) {
            echo "Destructor dijalankan: {$this->judul}"
                . PHP_EOL;
        }
    }
}