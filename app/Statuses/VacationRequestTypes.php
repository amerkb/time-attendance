<?php

namespace App\Statuses;

class VacationRequestTypes
{
    public const HOURLY = 1;
    public const DAILY = 2;
    public const DEATH = 3;
    public const SATISFYING  = 4;
    public const PILGRIMAME = 5;
    public const NEW_BABY = 6;
    public const EXAM = 7;
    public const PREGNANT_WOMAN = 8;
    public const METERNITY = 9;
    public const SICK_CHILD = 10;
    public const MARRIED = 11;

    public static array $statuses = [self::HOURLY, self::DAILY,   self::DEATH, self::SATISFYING, self::PILGRIMAME, self::NEW_BABY, self::EXAM, self::PREGNANT_WOMAN,   self::METERNITY, self::SICK_CHILD, self::MARRIED];
}
