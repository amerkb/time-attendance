<?php

namespace App\Filter\Attendance;

use App\Filter\OthersBaseFilter;

class AttendanceOverviewFilter extends OthersBaseFilter
{
    public ?string $year;


    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): void
    {
        $this->year = $year;
    }
}
