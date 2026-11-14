<?php

declare(strict_types=1);

namespace App\Tasks;

use InvalidArgumentException;

class Triangle
{
    public function __construct(
        public readonly float $a,
        public readonly float $b,
        public readonly float $c
    ) {
        if (
            $a <= 0 || $b <= 0 || $c <= 0 ||
            $a + $b <= $c ||
            $a + $c <= $b ||
            $b + $c <= $a
        ) {
            throw new InvalidArgumentException(
                'Некоректні сторони трикутника'
            );
        }
    }

    public function getPerimeter(): float
    {
        return $this->a + $this->b + $this->c;
    }

    public function getArea(): float
    {
        $p = $this->getPerimeter() / 2;

        return sqrt(
            $p *
            ($p - $this->a) *
            ($p - $this->b) *
            ($p - $this->c)
        );
    }
}
