<?php

namespace App\Filter\Deposit;

use App\Filter\OthersBaseFilter;

class DepositFilter extends OthersBaseFilter
{
    public ?int $status = null;

    /**
     * @param int $duration
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $duration
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
}
