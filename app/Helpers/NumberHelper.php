<?php

namespace App\Helpers;

class NumberHelper
{
    public static function _formatNumber(int|float $number, int|null $decimal = null, string $decimal_separator = ".", string $thousands_separator = ",")
    {
        $decimal = $decimal === null ? env("DEFAULT_DECIMALS", 2) : $decimal;
        return number_format($number, $decimal, $decimal_separator, $thousands_separator);
    }
    public static function _formatNumberWithCurrency(string $currency, int|float $number, int|null $decimal = null, string $decimal_separator = ".", string $thousands_separator = ",")
    {
        return $currency . self::_formatNumber($number, $decimal, $decimal_separator, $thousands_separator);
    }
}
