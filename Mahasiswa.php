<?php
declare(strict_types=1);

class Mahasiswa
{
    public function __construct(
        public string $nim="",
        public string $nama="",
        public string $fakultas="",
        public string $prodi="",
        public string $asalSekolah="",
        public string $alamat="",
        public int $semester,
        public string $hobi="",
        public string $agama=""
        
    ) {}

    public function ringkasan(): string
    {
        return $this->nim . ' - ' . $this->nama;
    }
}
