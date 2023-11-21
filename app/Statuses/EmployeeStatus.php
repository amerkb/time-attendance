<?php

namespace App\Statuses;

class EmployeeStatus
{
    public const UN_ACTIVE = 0;
    public const ACTIVE = 1;
    public const ON_VACATION = 2;
    public const ABSENT = 3;
    public const TEMPORARY_DISMISSED = 4;
    public const PERMANENT_DISMISSED = 5;


    public static array $statuses = [self::ACTIVE, self::ON_VACATION, self::ABSENT, self::TEMPORARY_DISMISSED, self::UN_ACTIVE, self::PERMANENT_DISMISSED];
}
