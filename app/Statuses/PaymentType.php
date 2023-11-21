<?php

namespace App\Statuses;

class PaymentType
{
    public const PAYMENT = 1;
    public const UNPAYMENT = 2;


    public static array $statuses = [self::PAYMENT, self::UNPAYMENT];
}
