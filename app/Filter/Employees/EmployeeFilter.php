<?php

namespace App\Filter\Employees;

use App\Filter\OthersBaseFilter;

class EmployeeFilter extends OthersBaseFilter
{
    public ?string $name;


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
