<?php
declare(strict_types=1);

class Latihan
{
    private array $atributTambahan = [];

    public function __set(string $nama, mixed $nilai): void
    {
        echo "__set() dipanggil untuk {$nama}" . PHP_EOL;
        $this->atributTambahan[$nama] = $nilai;
    }

    public function __get(string $nama): mixed
    {
        echo "__get() dipanggil untuk {$nama}" . PHP_EOL;
        return $this->atributTambahan[$nama] ?? null;
    }

    public function __isset(string $nama): bool
    {
        echo "__isset() dipanggil untuk {$nama}" . PHP_EOL;
        return isset($this->atributTambahan[$nama]);
    }

    public function __unset(string $nama): void
    {
        echo "__unset() dipanggil untuk {$nama}" . PHP_EOL;
        unset($this->atributTambahan[$nama]);
    }
}