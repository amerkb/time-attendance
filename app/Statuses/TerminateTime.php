<?php

namespace App\Statuses;

class TerminateTime
{
    public const OPEN_TERM = 0;
    public const THREE_MONTH = 3;
    public const SIX_MONTH = 6;
    public const ONE_YEAR = 12;



    public static array $statuses = [self::THREE_MONTH, self::SIX_MONTH, self::ONE_YEAR, self::OPEN_TERM];
}
