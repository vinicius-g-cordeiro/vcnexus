<?php

namespace App\Shared\Helpers;

class Utils
{

    static function pgArrayToPhp(string $pgArray): array
    {
        $pgArray = trim($pgArray);
        if ($pgArray === '{}' || $pgArray === '') {
            return [];
        }

        // Strip outer braces
        $inner = substr($pgArray, 1, -1);

        $result = [];
        $current = '';
        $inQuotes = false;
        $len = strlen($inner);

        for ($i = 0; $i < $len; $i++) {
            $char = $inner[$i];

            if ($char === '"' && ($i === 0 || $inner[$i - 1] !== '\\')) {
                $inQuotes = !$inQuotes;
                continue;
            }

            if ($char === '\\' && $inQuotes && isset($inner[$i + 1]) && $inner[$i + 1] === '"') {
                $current .= '"';
                $i++; // skip the escaped quote
                continue;
            }

            if ($char === ',' && !$inQuotes) {
                $result[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $result[] = $current;

        return $result;
    }

}
