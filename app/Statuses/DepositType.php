<?php

namespace App\Statuses;

class  DepositType
{
    public const CAR = 1;
    public const LAPTOP = 2;
    public const MOBILE = 3;


    public static array $statuses = [self::MOBILE, self::LAPTOP, self::CAR];
}
