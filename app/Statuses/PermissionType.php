<?php

namespace App\Statuses;

class PermissionType
{
    public const TRUE = 1;
    public const FALSE = 0;


    public static array $statuses = [self::TRUE, self::FALSE];
}
