<?php

namespace App\Helpers;

use Carbon\Carbon;

class PeriodLabelHelper
{
    /**
     * Kun/Oy/Yil labelini chiqaradi.
     *
     * @param string $type 'daily', 'monthly', 'yearly'
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return string
     */
    public static function getLabel(string $type, ?Carbon $from, ?Carbon $to): string
    {
        if (!$from && !$to) {
            return match ($type) {
                'daily' => 'Кунлик',
                'monthly' => 'Ойлик',
                'yearly' => 'Йиллик',
            };
        }

        if ($from && !$to) {
            return match ($type) {
                'daily' => 'Бошланғич',
                'monthly' => 'Ой бошидан ' . $from->format('d.m.Y') . ' гача',
                'yearly' => 'Йил бошидан ' . $from->format('d.m.Y') . ' гача',
            };
        }

        if ($from && $to) {
            return match ($type) {
                'daily' => 'Бошлангич кун: ' . $from->format('d.m.Y'),
                'monthly' => 'Оралиқ' . $from->format('d.m.Y') . ' – ' . $to->format('d.m.Y'),
                'yearly' => 'Охирги кун: ' . $to->format('d.m.Y'),
            };
        }

        return '';
    }
}
