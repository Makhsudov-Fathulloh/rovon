<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('sortLink')) {
    function sortLink(string $column, string $label): string
    {
        $current = request('sort');
        $direction = 'asc';
        $icon = '';

        if ($current === $column) {
            $direction = 'desc';
            $icon = '↑';
        } elseif ($current === "-{$column}") {
            $direction = 'asc';
            $icon = '↓';
        }

        $newSort = $direction === 'asc' ? $column : "-{$column}";
        $url = request()->fullUrlWithQuery(['sort' => $newSort]);

        return '<a href="' . e($url) . '">' . e($label) . ' ' . $icon . '</a>';
    }
}
