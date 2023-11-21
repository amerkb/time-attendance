<?php

namespace App\Statuses;

class MaterialStatus
{
    public const SINGLE = 1;
    public const MARRRIED = 2;
    public const DIVORCED = 3;
    public const WIDOWER = 4;


    public static array $statuses = [self::SINGLE, self::MARRRIED, self::DIVORCED, self::WIDOWER];
}
