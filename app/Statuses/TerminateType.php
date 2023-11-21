<?php

namespace App\Statuses;

class TerminateType
{
    public const TEMPORARY = 1;
    public const PERMANENT = 2;

    public static array $statuses = [self::TEMPORARY, self::PERMANENT];
}
