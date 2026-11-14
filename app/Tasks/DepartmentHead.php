<?php

declare(strict_types=1);

namespace App\Tasks;

class DepartmentHead extends WorkshopManager
{
    public function calculateSalary(): float
    {
        return parent::calculateSalary()
            + $this->baseSalary * 0.20;
    }
}
