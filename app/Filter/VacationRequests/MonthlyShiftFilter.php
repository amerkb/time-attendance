<?php

namespace App\Filter\VacationRequests;

use App\Filter\OthersBaseFilter;

class MonthlyShiftFilter extends OthersBaseFilter
{
    public ?int $duration = null;

    /**
     * @param int $duration
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }
}
