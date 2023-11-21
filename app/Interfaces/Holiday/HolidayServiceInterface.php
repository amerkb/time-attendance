<?php

namespace App\Interfaces\Holiday;


interface HolidayServiceInterface
{
    public function create_weekly_holiday($data);
    public function create_annual_holiday($data);
    public function update_annual_holiday($data);
}
