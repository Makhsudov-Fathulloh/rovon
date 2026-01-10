<?php

namespace App\Helpers;

use App\Services\StatusService;

class CountHelper
{
    /**
     * Format count and optionally show type
     *
     * @param float|int $count
     * @param int|null $typeCount
     * @param bool $showType
     * @return string
     */
    public static function format($count, ?int $typeCount = null, bool $showType = true): string
    {
        $precision = $typeCount === StatusService::UNIT_PSC ? 0 : 3;
        $formatted = number_format($count, $precision, '.', ' ');

        if ($showType && $typeCount !== null) {
            $typeLabel = StatusService::getTypeCount()[$typeCount] ?? '-';
            return $formatted . ' ' . $typeLabel;
        }

        return $formatted;
    }
}
