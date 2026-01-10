<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialTransfer;
use App\Models\RawMaterialTransferItem;
use App\Models\RawMaterialVariation;
use App\Models\Search\RawMaterialTransferItemSearch;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RawMaterialTransferItemController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new RawMaterialTransferItemSearch(new DateFilterService());
        $query = $searchModel->applyFilters(RawMaterialTransferItem::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('raw_material_transfer_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $rawMaterialTransfer = RawMaterialTransfer::whereIn('id', RawMaterialTransferItem::distinct()->pluck('raw_material_transfer_id'))->orderBy('title')->pluck('title', 'id');
        $rawMaterialVariation = RawMaterialVariation::whereIn('id', RawMaterialTransferItem::distinct()->pluck('raw_material_variation_id'))->orderBy('title')->pluck('title', 'id');

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCountUzs = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->count();
            $allCountUsd = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->count();
            $totalPriceUzs = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->sum('total_price');
            $totalPriceUsd = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->sum('total_price');
        } else {
            $allCountUzs = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->count();
            $allCountUsd = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->count();
            $totalPriceUzs = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->sum('total_price');
            $totalPriceUsd = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->sum('total_price');
        }

        $transferItems = $query->with('rawMaterialVariation:id,title,currency')->paginate(20)->withQueryString();

        return view('backend.raw-material-transfer-item.index', compact(
            'transferItems',
            'rawMaterialTransfer',
            'rawMaterialVariation',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'totalPriceUzs',
            'totalPriceUsd',
        ));
    }


    public function list(Request $request, $transfer_id)
    {
        $rawMaterialTransfer = RawMaterialTransfer::findOrFail($transfer_id);

        $searchModel = new RawMaterialTransferItemSearch(new \App\Services\DateFilterService());
        $query = RawMaterialTransferItem::query()->where('raw_material_transfer_id', $transfer_id)->with('rawMaterialTransfer');
        $query = $searchModel->applyFilters($query, $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('raw_material_transfer_item', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $rawMaterialVariation = RawMaterialVariation::whereIn('id', RawMaterialTransferItem::distinct()->pluck('raw_material_variation_id'))->orderBy('title')->pluck('title', 'id');

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCountUzs = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->count();
            $allCountUsd = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->count();
            $totalPriceUzs = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->sum('total_price');
            $totalPriceUsd = (clone $filteredQuery)->whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->sum('total_price');
        } else {
            $allCountUzs = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->where('raw_material_transfer_id', $transfer_id)->whereYear('created_at', now()->year)->count();
            $allCountUsd = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->where('raw_material_transfer_id', $transfer_id)->whereYear('created_at', now()->year)->count();
            $totalPriceUzs = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->where('raw_material_transfer_id', $transfer_id)->whereYear('created_at', now()->year)->sum('total_price');
            $totalPriceUsd = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->where('raw_material_transfer_id', $transfer_id)->whereYear('created_at', now()->year)->sum('total_price');
        }

        $rawMaterialTransferItems = $query->paginate(20)->withQueryString();

        return view('backend.raw-material-transfer-item.list', compact(
            'rawMaterialTransfer',
            'rawMaterialVariation',
            'rawMaterialTransferItems',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'totalPriceUzs',
            'totalPriceUsd',
        ));
    }


    public function show(RawMaterialTransferItem $rawMaterialTransferItem)
    {
        return view('backend.raw-material-transfer-item.show', compact('rawMaterialTransferItem'));
    }
}

