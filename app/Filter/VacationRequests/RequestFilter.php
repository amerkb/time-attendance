<?php

namespace App\Filter\VacationRequests;

use App\Filter\OthersBaseFilter;

class RequestFilter extends OthersBaseFilter
{
    public ?int $type = null;


    public function getRequestType(): int
    {
        return $this->type;
    }


    public function setRequestType(int $type): void
    {
        $this->type = $type;
    }
}
