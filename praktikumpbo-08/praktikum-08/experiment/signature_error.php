<?php
declare(strict_types=1);

abstract class FeeParent
{
    abstract public function calculateFee(): float;
}

class FeeChild extends FeeParent
{
    public function calculateFee(float $tax): float
    {
        return 5000000 + $tax;
    }
}

$student->calculateFee();