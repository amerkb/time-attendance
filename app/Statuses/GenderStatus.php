<?php

namespace App\Statuses;

class GenderStatus
{
    public const MALE = 1;
    public const FEMALE = 2;
    public static array $statuses = [self::MALE, self::FEMALE];
}
