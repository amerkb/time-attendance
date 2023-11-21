<?php

namespace App\Statuses;

class  RequestStatus
{
    public const  APPROVEED  = 1;
    public const  REJECTED = 2;
    public const PENDING = 3;


    public static array $statuses = [self::APPROVEED, self::REJECTED, self::PENDING];
}
