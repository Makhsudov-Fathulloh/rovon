<?php

namespace App\Models\Search;

use App\Models\Category;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CategorySearch
{
    protected $dateFilter;
    public function __construct(DateFilterService $dateFilter)
    {
        $this->dateFilter = $dateFilter;
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters', []);
        $query = Category::query();

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        if (!empty($filters['subtitle'])) {
            $query->whereRaw('LOWER(subtitle) LIKE ?', ['%' . strtolower($filters['subtitle']) . '%']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Sanadan-sanagacha filter
        $errors = $this->dateFilter->applyDateToFilters($query, $filters);
        Session::flash('date_format_errors', $errors);


        // 1. Aniq sana (kun-oy-yil) bo‘yicha filter
        //$exactDateErrors = $this->dateFilter->applyExactDateFilters($query, $filters);
        // 2. Oy-yil bo‘yicha filter
        //$errors = $this->dateFilter->applyDateFilters($query, $filters);
        // Xatoliklarni sessionga o‘tkazish
        //Session::flash('date_format_errors', array_merge($errors, $exactDateErrors));

        // 🔥 Default sort (sort parametri yo‘q bo‘lsa)
        if (!$request->has('sort')) {
            $query->orderBy('id');
        }

        return $query;
    }
}
