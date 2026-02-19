<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $number = (int) $number;
        
        if ($number == 0) {
            return 'zero';
        }

        $words = [
            0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
            5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
            14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
            18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
            80 => 'eighty', 90 => 'ninety'
        ];

        if ($number < 21) {
            return $words[$number];
        }

        if ($number < 100) {
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            return $words[$tens] . ($units ? ' ' . $words[$units] : '');
        }

        if ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            return $words[$hundreds] . ' hundred' . ($remainder ? ' and ' . self::convert($remainder) : '');
        }

        if ($number < 100000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            return self::convert($thousands) . ' thousand' . ($remainder ? ' ' . self::convert($remainder) : '');
        }

        if ($number < 10000000) {
            $lakhs = (int) ($number / 100000);
            $remainder = $number % 100000;
            return self::convert($lakhs) . ' lakh' . ($remainder ? ' ' . self::convert($remainder) : '');
        }

        $crores = (int) ($number / 10000000);
        $remainder = $number % 10000000;
        return self::convert($crores) . ' crore' . ($remainder ? ' ' . self::convert($remainder) : '');
    }
}
