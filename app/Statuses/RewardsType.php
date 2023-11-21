<?php

namespace App\Statuses;

class RewardsType
{
    public const NUMBER = 1;
    public const RATE = 2;


    public static array $statuses = [self::NUMBER, self::RATE];
}