<?php

namespace App\Models\Search;

use App\Models\Shift;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ShiftSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request, $query = null)
    {
        $filters = $request->get('filters', []);

        // Agar query uzatilgan bo'lsa o'shani ishlatamiz, yo'q bo'lsa yangi Shift::query()
        $query = $query ?: Shift::query();

        if (!empty($filters['id'])) {
            $query->where('shift.id', $filters['id']);
        }

        if (!empty($filters['organization_section_shift'])) {
            $raw = trim($filters['organization_section_shift']);
            $parts = array_values(array_filter(
                array_map(fn($v) => mb_strtolower(trim($v)), explode('.', $raw))
            ));

            $query->where(function ($q) use ($parts) {

                // 🔹 1) NUQTASIZ — oddiy qidiruv
                if (count($parts) === 1) {
                    $keyword = $parts[0];

                    $q->whereRaw('LOWER(organization.title) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(section.title) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(shift.title) LIKE ?', ["%{$keyword}%"]);
                }

                // 🔹 2) Organization.Section
                elseif (count($parts) === 2) {
                    [$org, $section] = $parts;

                    $q->whereRaw('LOWER(organization.title) LIKE ?', ["%{$org}%"])
                        ->whereRaw('LOWER(section.title) LIKE ?', ["%{$section}%"]);
                }

                // 🔹 3) Organization.Section.Shift
                elseif (count($parts) >= 3) {
                    [$org, $section, $shift] = $parts;

                    $q->whereRaw('LOWER(organization.title) LIKE ?', ["%{$org}%"])
                        ->whereRaw('LOWER(section.title) LIKE ?', ["%{$section}%"])
                        ->whereRaw('LOWER(shift.title) LIKE ?', ["%{$shift}%"]);
                }
            });
        }

        if (!empty($filters['user_id'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('user.id', $filters['user_id']);
            });
        }

        if (!empty($filters['description'])) {
            $query->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($filters['description']) . '%']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
                    $query->whereBetween('shift.created_at', [$fromDate, $toDate]);
                }
            } elseif ($from) {
                // faqat o‘sha kun
                $query->whereBetween('shift.created_at', [
                    \Carbon\Carbon::parse($from)->startOfDay(),
                    \Carbon\Carbon::parse($from)->endOfDay(),
                ]);
            }
        } catch (\Exception $e) {
            Session::flash('date_format_errors', ['Сана формати нотўғри.']);
        }

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderBy('id');
        }

        return $query;
    }
}
