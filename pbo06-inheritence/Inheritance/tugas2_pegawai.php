<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

abstract class Pegawai
{
    private string $id;
    private string $nama;

    public function __construct(string $id, string $nama)
    {
        $id = trim($id);
        $nama = trim($nama);

        if ($id === '' || $nama === '') {
            throw new InvalidArgumentException(
                'ID dan nama wajib diisi.'
            );
        }

        $this->id = $id;
        $this->nama = $nama;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    abstract public function hitungGaji(): int;
}

final class PegawaiTetap extends Pegawai
{
    private int $gajiPokok;

    public function __construct(
        string $id,
        string $nama,
        int $gajiPokok
    ) {
        parent::__construct($id, $nama);

        if ($gajiPokok < 0) {
            throw new InvalidArgumentException(
                'Gaji pokok tidak boleh negatif.'
            );
        }

        $this->gajiPokok = $gajiPokok;
    }

    public function hitungGaji(): int
    {
        return $this->gajiPokok;
    }
}

final class PegawaiKontrak extends Pegawai
{
    private int $upahPerJam;
    private int $jamKerja;

    public function __construct(
        string $id,
        string $nama,
        int $upahPerJam,
        int $jamKerja
    ) {
        parent::__construct($id, $nama);

        if ($upahPerJam < 0 || $jamKerja < 0) {
            throw new InvalidArgumentException(
                'Upah dan jam kerja tidak boleh negatif.'
            );
        }

        $this->upahPerJam = $upahPerJam;
        $this->jamKerja = $jamKerja;
    }

    public function hitungGaji(): int
    {
        return $this->upahPerJam * $this->jamKerja;
    }
}

$pegawaiTetap = new PegawaiTetap(
    'P001', 'Andi', 5000000
);

$pegawaiKontrak = new PegawaiKontrak(
    'P002', 'Budi', 50000, 80
);

foreach ([$pegawaiTetap, $pegawaiKontrak] as $pegawai) {
    echo $pegawai->getId()
        . ' | ' . $pegawai->getNama()
        . ' | Rp'
        . number_format($pegawai->hitungGaji(), 0, ',', '.')
        . PHP_EOL;
}

echo PHP_EOL . 'PENGUJIAN ANGKA NEGATIF' . PHP_EOL;

$kasus = [
    'Gaji pokok negatif' =>
        fn() => new PegawaiTetap('P003', 'Citra', -1),

    'Upah per jam negatif' =>
        fn() => new PegawaiKontrak('P004', 'Dina', -1, 80),

    'Jam kerja negatif' =>
        fn() => new PegawaiKontrak('P005', 'Eka', 50000, -1)
];

foreach ($kasus as $label => $aksi) {
    try {
        $aksi();
        echo "GAGAL UJI: {$label} diterima." . PHP_EOL;
    } catch (InvalidArgumentException $e) {
        echo "{$label}: ditolak. "
            . $e->getMessage() . PHP_EOL;
    }
}