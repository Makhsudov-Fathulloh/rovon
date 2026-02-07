<?php

namespace App\Models\Search;

use App\Models\ShiftOutputWorker;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ShiftOutputWorkerSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = ShiftOutputWorker::query();

        if (!empty($filters['id'])) {
            $query->where('shift_output_worker.id', $filters['id']);
        }

        if (!empty($filters['shift_output_id'])) {
            $query->where('shift_output_id', $filters['shift_output_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['shift_or_stage'])) {
            $raw = trim($filters['shift_or_stage']);

            $parts = array_values(array_filter(
                array_map(fn ($v) => mb_strtolower(trim($v)), explode('.', $raw))
            ));

            $query->where(function ($q) use ($parts) {

                // 🔹 1) Nuqtasiz — shift VA stage dan qidiradi
                if (count($parts) === 1) {
                    $keyword = $parts[0];

                    $q->whereHas('shiftOutput.shift', function ($sq) use ($keyword) {
                        $sq->whereRaw('LOWER(shift.title) LIKE ?', ["%{$keyword}%"]);
                    })
                        ->orWhereHas('shiftOutput.stage', function ($stq) use ($keyword) {
                            $stq->whereRaw('LOWER(stage.title) LIKE ?', ["%{$keyword}%"]);
                        });
                }

                // 🔹 2) Shift.Stage
                elseif (count($parts) >= 2) {
                    [$shift, $stage] = $parts;

                    $q->whereHas('shiftOutput.shift', function ($sq) use ($shift) {
                        $sq->whereRaw('LOWER(shift.title) LIKE ?', ["%{$shift}%"]);
                    })
                        ->whereHas('shiftOutput.stage', function ($stq) use ($stage) {
                            $stq->whereRaw('LOWER(stage.title) LIKE ?', ["%{$stage}%"]);
                        });
                }
            });
        }

        if (isset($filters['stage_defect_price']) && $filters['stage_defect_price'] !== '') {
            $value = (int) preg_replace('/[^\d.]/', '', $filters['stage_defect_price']);

            if ($value !== '') {
                $query->where(function ($q) use ($value) {
                    $q->whereRaw('CAST(stage_count AS TEXT) LIKE ?', ["%{$value}%"])
                        ->orWhereRaw('CAST(defect_amount AS TEXT) LIKE ?', ["%{$value}%"])
                        ->orWhereRaw('CAST(price AS TEXT) LIKE ?', ["%{$value}%"]);
                });
            }
        }

        // Sanadan-sanagacha filter
        $from = $filters['created_from'] ?? null;
        $to   = $filters['created_to'] ?? null;

        try {
            if ($from && $to) {
                $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
                $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

                if ($toDate->lt($fromDate)) {
                    Session::flash('date_format_errors', ['Охирги сана бошланғич санадан олдин бўлиши мумкин эмас.']);
                } else {
                    $query->whereBetween('shift_output_worker.created_at', [$fromDate, $toDate]);
                }
            } elseif ($from) {
                // faqat o‘sha kun
                $query->whereBetween('shift_output_worker.created_at', [
                    \Carbon\Carbon::parse($from)->startOfDay(),
                    \Carbon\Carbon::parse($from)->endOfDay(),
                ]);
            }
        } catch (\Exception $e) {
            Session::flash('date_format_errors', ['Сана формати нотўғри.']);
        }

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderByDesc('id');
        }

        return $query;
    }
}
