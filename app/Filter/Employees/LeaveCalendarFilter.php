<?php

namespace App\Filter\Employees;

use App\Filter\OthersBaseFilter;

class LeaveCalendarFilter extends OthersBaseFilter
{
    public ?string $date;
    public ?int $day = null;


    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }


    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): void
    {
        $this->day = $day;
    }
}
