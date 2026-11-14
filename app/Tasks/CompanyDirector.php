<?php

declare(strict_types=1);

namespace App\Tasks;

class CompanyDirector extends DepartmentHead
{
    public function calculateSalary(): float
    {
        return parent::calculateSalary()
            + $this->baseSalary * 0.30;
    }
}
