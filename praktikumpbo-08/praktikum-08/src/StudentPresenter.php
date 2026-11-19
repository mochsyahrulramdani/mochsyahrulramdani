<?php
declare(strict_types=1);
/** PHP overloading: __call() menangani method yang TIDAK dideklarasikan. */
final class StudentPresenter {
    public function __call(string $method, array $args): string {
        if ($method !== 'showData') throw new BadMethodCallException("Method tidak dikenal: $method");
        if (count($args) === 1 && is_string($args[0])) return 'Nama: ' . $args[0];
        if (count($args) === 2 && is_string($args[0]) && is_string($args[1])) return 'NIM: ' . $args[0] . ', Nama: ' . $args[1];
        if (count($args) === 3 && is_string($args[0]) && is_string($args[1]) && is_string($args[2])) return 'NIM: ' . $args[0] . ', Nama: ' . $args[1] . ', Prodi: ' . $args[2];
        throw new InvalidArgumentException('showData menerima 1, 2, atau 3 argumen string.');
    }
}

$presenter = new StudentPresenter();

echo $presenter->showData(
    '2026001',
    'Ahmad',
    'Informatika'
);