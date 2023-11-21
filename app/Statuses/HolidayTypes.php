<?php

namespace App\Statuses;

class HolidayTypes
{
    public const WEEKLY = 1;
    public const ANNUL = 2;


    public static array $statuses = [self::WEEKLY, self::ANNUL];
}
