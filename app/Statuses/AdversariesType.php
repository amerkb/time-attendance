<?php

namespace App\Statuses;

class AdversariesType
{
    public const NUMBER = 1;
    public const RATE = 2;


    public static array $statuses = [self::NUMBER, self::RATE];
}
