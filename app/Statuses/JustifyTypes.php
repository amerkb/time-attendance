<?php

namespace App\Statuses;

class  JustifyTypes
{
    public const ILLNESS = 1;
    public const TRAVEL = 2;
    public const OTHERS = 3;

    public static array $statuses = [self::ILLNESS, self::TRAVEL, self::OTHERS];
}
