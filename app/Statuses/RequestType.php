<?php

namespace App\Statuses;

class  RequestType
{
    public const  VACATION  = 1;
    public const  JUSTIFY = 2;
    public const  RETIREMENT  = 3; // تقاعد
    public const  RESIGNATION = 4; // استقالة

    public static array $statuses = [self::VACATION, self::JUSTIFY, self::RETIREMENT, self::RESIGNATION];
}
