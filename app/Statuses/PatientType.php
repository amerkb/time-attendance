<?php

namespace App\Statuses;

class PatientType
{
    public const FATHER = 1;
    public const MOTHER = 2;
    public const SISTER = 3;
    public const PROTHER = 4;
    public const SON = 5;
    public const DAUGHTER = 6;
    public const HUSBAND = 7;
    public const ME = 8;
    public const GRAND_FATHER = 9;
    public const GRAND_MOTHER = 10;
    public const UNCLE = 11;
    public const AUNT = 12;
    public const MATERNAL_UNCLE = 13;
    public const MATERNAL_AUNT = 14;

    public static array $statuses1 = [self::FATHER, self::MOTHER, self::SISTER, self::PROTHER, self::SON, self::DAUGHTER, self::HUSBAND, self::ME];
    public static array $statuses2 = [self::GRAND_FATHER, self::GRAND_MOTHER, self::UNCLE, self::AUNT, self::MATERNAL_UNCLE, self::MATERNAL_AUNT, self::FATHER, self::MOTHER, self::SISTER, self::PROTHER, self::SON, self::DAUGHTER, self::HUSBAND];
}
