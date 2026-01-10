<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\ProductLog;
use App\Models\MaterialLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Models\Search\LogSearch;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use App\Models\RawMaterialVariation;
use Illuminate\Support\Facades\Schema;

class LogController extends Controller
{
    public function material(Request $request)
    {
        $searchModel = new LogSearch(new DateFilterService());
        $query = $searchModel->materialLogSearch($request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('material_log', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $rawMaterials = RawMaterialVariation::whereIn('id', MaterialLog::select('raw_material_variation_id')->distinct())->orderBy('title')->pluck('title', 'id');
        $users = User::whereIn('id', MaterialLog::distinct()->pluck('user_id'))->pluck('username', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $materialLogCount = $query->distinct()->count('raw_material_variation_id');
        } else {
            $materialLogCount = MaterialLog::distinct()->count('raw_material_variation_id');
        }

        $materialLogs = $query->with([
            'rawMaterialVariation:id,code,title,count,unit'
        ])->paginate(20)->withQueryString();

        return view('backend.log.material', compact(
            'materialLogs',
            'rawMaterials',
            'users',
            'isFiltered',
            'materialLogCount'
        ));
    }

    public function product(Request $request)
    {
        $searchModel = new LogSearch(new DateFilterService());
        $query = $searchModel->productLogSearch($request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('product_log', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $products = ProductVariation::whereIn('id', ProductLog::select('product_variation_id')->distinct())->orderBy('title')->pluck('title', 'id');
        $users = User::whereIn('id', ProductLog::distinct()->pluck('user_id'))->pluck('username', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $productLogCount = $query->distinct()->count('product_variation_id');
        } else {
            $productLogCount = ProductLog::distinct()->count('product_variation_id');
        }

        $productLogs = $query->with([
            'productVariation:id,code,title,count,unit'
        ])->paginate(20)->withQueryString();

        return view('backend.log.product', compact(
            'productLogs',
            'products',
            'users',
            'isFiltered',
            'productLogCount'
        ));
    }
}
