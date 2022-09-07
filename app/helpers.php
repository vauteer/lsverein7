<?php

use App\Models\Club;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

if (!function_exists('isCli')) {
    function isCli(): bool
    {
        return (php_sapi_name() === 'cli' || defined('STDIN'));
    }
}

if (!function_exists('currentClubId')) {
    function currentClubId(): int
    {
        return isCli() ? 1 : auth()->user()->club_id;
    }
}

if (!function_exists('currentClub')) {
    function currentClub(): Club
    {
        return Club::find(currentClubId());
    }
}

if (!function_exists('currentUser')) {
    function currentUser(): User
    {
        return isCli() ? User::find(1) : auth()->user();
    }
}

if (!function_exists('getRange')) {
    function getRange(string $from, ?string $to): string
    {
        $from = new Carbon($from);
        $range = formatDate($from) . '-';
        if ($to !== null) {
            $to = new Carbon($to);
            $range .= formatDate($to);
        }

        return $range;
    }
}

if (!function_exists('inRange')) {
    function inRange(?Carbon $date, ?Carbon $from, ?Carbon $to): bool
    {
        if ($date === null)
            return false;

        if ($from !== null && $date->lt($from))
            return false;

        return ($to === null || $date->lte($to));
    }
}

if (!function_exists('formatDate')) {
    function formatDate(Carbon|string|null $date): string
    {
        if ($date === null)
            return '';

        if (is_string($date))
            $date = new Carbon($date);

        return $date->format('d.m.Y');
    }
}

if (!function_exists('ibanLength')) {
    function ibanLength(string $locale): int
    {
        return match($locale) {
            'DE' => 22, 'AT' => 20, 'AD' => 24, 'AE' => 23, 'AZ' => 28, 'BA' => 20, 'BE' => 16, 'BG' => 22, 'BH' => 22, 'BR' => 29,
            'CH' => 21, 'CR' => 22, 'CY' => 28, 'CZ' => 24, 'DK' => 18, 'DO' => 28, 'EE' => 20, 'ES' => 24,
        'FI' => 18, 'FO' => 18, 'FR' => 27, 'GB' => 22, 'GI' => 23, 'GL' => 18, 'GR' => 27, 'GT' => 28, 'HR' => 21,
        'HU' => 28, 'IE' => 22, 'IL' => 23, 'IS' => 26, 'IT' => 27, 'JO' => 30, 'KW' => 30, 'KZ' => 20, 'LB' => 28,
        'LI' => 21, 'LT' => 20, 'LU' => 20, 'LV' => 21, 'MC' => 27, 'MD' => 24, 'ME' => 22, 'MK' => 19, 'MR' => 27,
        'MT' => 31, 'MU' => 30, 'NL' => 18, 'NO' => 15, 'PK' => 24, 'PL' => 28, 'PS' => 29, 'PT' => 25, 'QA' => 29,
        'RO' => 24, 'RS' => 22, 'SA' => 24, 'SE' => 24, 'SI' => 19, 'SK' => 24, 'SM' => 27, 'TN' => 24, 'TR' => 26,
        'AL' => 28, 'BY' => 28, 'EG' => 29, 'GE' => 22, 'IQ' => 23, 'LC' => 32, 'SC' => 31, 'ST' => 25,
        'SV' => 28, 'TL' => 23, 'UA' => 29, 'VA' => 22, 'VG' => 24, 'XK' => 20
        };
    }
}
if (!function_exists('checkIban')) {
    function checkIban(string $iban): Bool
    {
        $iban = strtoupper(trim(str_replace(' ', '', $iban)));
        $pattern = "|[A-Z]{2}\d{11,29}|";

        if (!preg_match($pattern, $iban))
            return false;

        $countryCode = substr($iban, 0, 2);
        if (ibanLength($countryCode) !== strlen($iban))
            return false;

        $givenSum = intval(substr($iban, 2, 2));
        $digits = substr($iban, 4) . (ord($countryCode[0]) - 55). (ord($countryCode[1]) - 55) . '00';
        $checksum = 98 - mod97($digits);

        return ($checksum === $givenSum);
    }
}

if (!function_exists('normalizeIban')) {
    function normalizeIban($iban): string
    {
        return join(' ', str_split(strtoupper(str_replace(' ', '', $iban)), 4));
    }
}

if (!function_exists('mod97')) {
    function mod97($string): int
    {
        $checksum = substr($string, 0, 2);
        for ($offset = 2; $offset < strlen($string); $offset += 7)
        {
            $fragment = $checksum . substr($string, $offset, 7);
            $checksum = intval($fragment) % 97;
        }

        return $checksum;
    }
}

if (!function_exists('optionsFromArray')) {
    function optionsFromArray(array $array, bool $sorted = true)
    {
        $result = Arr::map($array, function ($value, $key) {
           return ['id' => $key, 'name' => $value];
        });


        return $sorted ? Arr::sort($result, fn ($item) => $item['name']) : $result;
    }
}
