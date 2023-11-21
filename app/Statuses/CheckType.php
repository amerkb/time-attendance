<?php

namespace App\Statuses;

class  CheckType
{
    public const MAC_ADDRESS = 1;
    public const LOCATION = 2;
    public const TOGETHER = 3;

    public static array $statuses = [self::MAC_ADDRESS, self::LOCATION, self::TOGETHER];
}
