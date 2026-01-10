<?php

namespace App\Services;

use Carbon\Carbon;

class PeriodService
{
    /**
     * Statistikalar uchun periodlarni hisoblash.
     *
     * Default → bugun / oy boshidan bugungacha / yil boshidan bugungacha
     * Faqat from → from kuni / oy boshidan fromgacha / yil boshidan fromgacha
     * From + To → from kuni / from–to / to kuni
     *
     * @param string|null $from
     * @param string|null $to
     * @return array
     */
    public static function getPeriods(?string $from, ?string $to): array
    {
        $hasFrom = !empty($from);
        $hasTo   = !empty($to);

        // Carbon instance
        $fromDate = $hasFrom ? Carbon::parse($from)->startOfDay() : now()->startOfDay();
        $toDate   = $hasTo ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        // Kunlik
        if (!$hasFrom && !$hasTo) {
            $dailyFrom = now()->startOfDay();
            $dailyTo   = now()->endOfDay();
        } elseif ($hasFrom && !$hasTo) {
            $dailyFrom = $fromDate->copy();
            $dailyTo   = $fromDate->copy()->endOfDay();
        } else {
            // From + To
            $dailyFrom = $fromDate->copy();
            $dailyTo   = $fromDate->copy()->endOfDay();
        }

        // Oylik
        if (!$hasFrom && !$hasTo) {
            $monthlyFrom = now()->startOfMonth();
            $monthlyTo   = now()->endOfDay();
        } elseif ($hasFrom && !$hasTo) {
            $monthlyFrom = $fromDate->copy()->startOfMonth();
            $monthlyTo   = $fromDate->copy()->endOfDay();
        } else {
            // From + To
            $monthlyFrom = $fromDate->copy();
            $monthlyTo   = $toDate->copy();
        }

        // Yillik
        if (!$hasFrom && !$hasTo) {
            $yearlyFrom = now()->startOfYear();
            $yearlyTo   = now()->endOfDay();
        } elseif ($hasFrom && !$hasTo) {
            $yearlyFrom = $fromDate->copy()->startOfYear();
            $yearlyTo   = $fromDate->copy()->endOfDay();
        } else {
            // From + To → yillik faqat to kuni
            $yearlyFrom = $toDate->copy()->startOfDay();
            $yearlyTo   = $toDate->copy()->endOfDay();
        }

        return [
            'daily'   => ['from' => $dailyFrom, 'to' => $dailyTo],
            'monthly' => ['from' => $monthlyFrom, 'to' => $monthlyTo],
            'yearly'  => ['from' => $yearlyFrom, 'to' => $yearlyTo],
        ];
    }
}
