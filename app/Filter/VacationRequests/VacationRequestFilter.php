<?php

namespace App\Filter\VacationRequests;

use App\Filter\OthersBaseFilter;

class VacationRequestFilter extends OthersBaseFilter
{
    public ?int $duration;

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
