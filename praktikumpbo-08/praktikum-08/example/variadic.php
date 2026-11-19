<?php
declare(strict_types=1);

final class Calculator
{
    public function add(int ...$numbers): int
    {
        return array_sum($numbers);
    }
}

$calc = new Calculator();

echo $calc->add(10, 20) . PHP_EOL;
echo $calc->add(10, 20, 30) . PHP_EOL;