<?php

namespace App\Statuses;

class TimeSelect
{
    public const FIRST = 30;
    public const SECOND = 60;
    public const THIRD = 90;


    public static array $statuses = [self::FIRST, self::SECOND, self::THIRD];
}
