<?php

namespace App\Statuses;

class  DepositStatus
{
    public const APPROVED = 1;
    public const PENDING = 3;
    public const REJECTED = 2;


    public const UN_PAID = 4;
    public const PAID = 5;
    public const UN_PAID_REJECTED = 6;


    public static array $statuses = [self::PAID, self::UN_PAID, self::PENDING, self::REJECTED,     self::APPROVED, self::UN_PAID_REJECTED];
    public static array $statuses2 = [self::APPROVED, self::PENDING, self::REJECTED];
}
