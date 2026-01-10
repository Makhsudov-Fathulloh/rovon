<?php

namespace App\Services;

use Carbon\Carbon;

class DateFilterService
{
    // from → to
    protected array $errors = [];

    public function applyDateToFilters($query, array $filters)
    {
        $from = $filters['created_from'] ?? null;
        $to   = $filters['created_to'] ?? null;

        try {

            // ✅ from + to → range
            if ($from && $to) {
                $fromDate = Carbon::parse($from)->startOfDay();
                $toDate   = Carbon::parse($to)->endOfDay();

                if ($toDate->lt($fromDate)) {
                    $this->errors[] = 'Охирги сана бошланғич санадан олдин бўлиши мумкин эмас.';
                } else {
                    $query->whereBetween('created_at', [$fromDate, $toDate]);
                }
            }

            // ✅ faqat from → faqat o‘sha kun
            elseif ($from) {
                $query->whereBetween('created_at', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($from)->endOfDay(),
                ]);
            }
        } catch (\Exception $e) {
            $this->errors[] = 'Сана формати нотўғри.';
        }

        return $this->errors;
    }
}
