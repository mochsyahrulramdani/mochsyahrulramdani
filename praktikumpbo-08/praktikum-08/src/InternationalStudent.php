<?php
declare(strict_types=1);

final class InternationalStudent extends Student
{
    public function __construct(
        string $id,
        string $name,
        string $major,
        float $baseFee,
        private float $serviceFee
    ) {
        if (
            !is_finite($serviceFee)
            || $serviceFee < 0
        ) {
            throw new InvalidArgumentException(
                'Biaya layanan harus berupa angka terbatas dan tidak negatif.'
            );
        }

        parent::__construct(
            $id,
            $name,
            $major,
            $baseFee
        );

        if (!is_finite($baseFee + $serviceFee)) {
            throw new InvalidArgumentException(
                'Total biaya terlalu besar.'
            );
        }
    }

    public function getType(): string
    {
        return 'international';
    }

    public function getServiceFee(): float
    {
        return $this->serviceFee;
    }

    public function calculateFee(): float
    {
        return $this->baseFee + $this->serviceFee;
    }

    public function summary(): string
    {
        return parent::summary() . ' [Internasional]';
    }
}