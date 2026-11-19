<?php
declare(strict_types=1);

class ParentExample
{
    private function label(): string
    {
        return 'Parent private';
    }

    public function summary(): string
    {
        return $this->label();
    }
}

class ChildExample extends ParentExample
{
    public function label(): string
    {
        return 'Child public';
    }
}

$child = new ChildExample();

echo $child->summary() . PHP_EOL;
echo $child->label() . PHP_EOL;