<?php

namespace App\Models\Search;

use App\Models\User;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class UserSearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = User::query();

        if (!empty($filters['id'])) {
            $filters['id'] = preg_replace('/\D/', '', $filters['id']);
            $query->where('id', (int) $filters['id']);
        }

        if (!empty($filters['first_name'])) {
            $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($filters['first_name']) . '%']);
        }

        if (!empty($filters['last_name'])) {
            $query->whereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($filters['last_name']) . '%']);
        }

        if (!empty($filters['username'])) {
            $query->where('username', 'like', '%' . $filters['username'] . '%');
        }

        if (!empty($filters['address'])) {
            $query->where('address', 'like', '%' . $filters['address'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['email_verified_at'])) {
            $query->whereDate('email_verified_at', Carbon::parse($filters['email_verified_at'])->format('Y-m-d'));
        }

        if (!empty($filters['phone'])) {
            $phone = preg_replace('/\D/', '', $filters['phone']); // faqat raqamlarni olish

            if (strlen($phone) === 12 && str_starts_with($phone, '998')) {
                // 1️⃣ To‘liq +998 bilan kiritilgan raqam
                $query->whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$phone]);
            } elseif (strlen($phone) === 7 && str_starts_with($phone, '998')) {
                // 2️⃣ +9981234 → oxirgi 4 raqam qismi
                $last4 = substr($phone, -4);
                $query->whereRaw("RIGHT(REGEXP_REPLACE(phone, '[^0-9]', ''), 4) = ?", [$last4]);
            }
        }

        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['debt']) && $filters['debt'] !== '') {
            $amount = (int) preg_replace('/\D/', '', $filters['debt']);

            if ($amount === 0) {
                $query->where(function ($q) {
                    // 1. Umuman user_debt jadvalida recordi yo'qlar
                    $q->whereDoesntHave('userDebt')
                        // 2. YOKI recordi bor, lekin summasi 0 bo'lganlar
                        ->orWhereHas('userDebt', function ($sub) {
                            $sub->select('user_id')
                                ->groupBy('user_id')
                                ->havingRaw('SUM(amount) = 0');
                        });
                });
            } else {
                // Qarzi aynan kiritilgan summaga teng bo'lganlar (har qanday valyutada)
                $query->whereHas('userDebt', function ($sub) use ($amount) {
                    $sub->select('user_id')
                        ->groupBy('user_id', 'currency') // Har bir valyuta bo'yicha alohida tekshirish
                        ->havingRaw('SUM(amount) = ?', [$amount]);
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
                    $query->whereBetween('user.created_at', [$fromDate, $toDate]);
                }
            } elseif ($from) {
                // faqat o‘sha kun
                $query->whereBetween('user.created_at', [
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
