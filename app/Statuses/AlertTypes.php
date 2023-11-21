<?php

namespace App\Statuses;

class  AlertTypes
{
    public const SWEARING = 1;
    public const FABRICATE_PROBLEMS = 2;
    public const OTHERS = 3;

    public static array $statuses = [self::SWEARING, self::FABRICATE_PROBLEMS, self::OTHERS];
}
