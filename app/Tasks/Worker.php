<?php

declare(strict_types=1);

namespace App\Tasks;

abstract class Worker
{
    public function __construct(
        protected string $name,
        protected float $baseSalary
    ) {}

    abstract public function calculateSalary(): float;
}
