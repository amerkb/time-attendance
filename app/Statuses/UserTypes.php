<?php

namespace App\Statuses;

class UserTypes

{
    public const SUPER_ADMIN = 1;
    public const ADMIN = 2;
    public const HR = 3;
    public const EMPLOYEE = 4;

    public static array $statuses = [self::SUPER_ADMIN, self::ADMIN, self::EMPLOYEE, self::HR];
}
