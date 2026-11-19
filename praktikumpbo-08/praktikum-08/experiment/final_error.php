<?php
declare(strict_types=1);

class FinalParent
{
    final public function getId(): string
    {
        return 'M01';
    }
}

class FinalChild extends FinalParent
{
    public function getId(): string
    {
        return 'M02';
    }
}