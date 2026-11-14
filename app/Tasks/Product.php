<?php

declare(strict_types=1);

namespace App\Tasks;

class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $price
    ) {}
}
