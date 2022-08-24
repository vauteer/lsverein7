<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

if (!function_exists('isCli')) {
    function isCli()
    {
        return (php_sapi_name() === 'cli' || defined('STDIN'));
    }
}

if (!function_exists('getRange')) {
    function getRange(string $from, ?string $to): string
    {
        $from = new Carbon($from);
        $range = $from->format('d.m.Y') . '-';
        if ($to !== null) {
            $to = new Carbon($to);
            $range .= $to->format('d.m.Y');
        }

        return $range;
    }
}

if (!function_exists('inRange')) {
    function inRange(?Carbon $date, ?Carbon $from, ?Carbon $to)
    {
        if ($date === null)
            return false;

        if ($from !== null && $date->lt($from))
            return false;

        return ($to === null || $date->lte($to));
    }
}

if (!function_exists('checkIban')) {
    function checkIban(string $iban): Bool
    {
        $iban = strtoupper(trim(str_replace(' ', '', $iban)));
        $pattern = "|[A-Z]{2}\d{18,32}|";

        if (!preg_match($pattern, $iban))
            return false;

        $countryCode = substr($iban, 0, 2);
        $givenSum = intval(substr($iban, 2, 2));
        $digits = substr($iban, 4) . (ord($countryCode[0]) - 55). (ord($countryCode[1]) - 55) . '00';

        $checksum = 98 - mod97($digits);

        return ($checksum === $givenSum);
    }
}

if (!function_exists('normalizeIban')) {
    function normalizeIban($iban)
    {
        return join(' ', str_split(strtoupper(str_replace(' ', '', $iban)), 4));
    }
}

if (!function_exists('mod97')) {
    function mod97($string)
    {
        $checksum = substr($string, 0, 2);
        for ($offset = 2; $offset < strlen($string); $offset += 7)
        {
            $fragment = $checksum . substr($string, $offset, 7);
            $checksum = intval($fragment) % 97;
            Log::info($checksum);
        }

        return $checksum;
    }
}
