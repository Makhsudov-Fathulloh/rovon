<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\ProductVariation;
use App\Models\RawMaterialVariation;
use App\Models\Warehouse;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    protected $user;
    protected $roleTitle;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->roleTitle = $this->user->role->title ?? null;
            return $next($request);
        });
    }

    public function index()
    {
        if (in_array($this->roleTitle, ['Admin', 'Manager', 'Developer'])) {
            $warehouses = Warehouse::with('organization')->paginate(10);
        } elseif ($this->roleTitle === 'Moderator') {
            $warehouses = Warehouse::with('organization')
                ->whereHas('organization', function ($query) {
                    $query->where('user_id', $this->user->id);
                })
                ->paginate(10);
        } else {
            abort(403, 'Сизда бу саҳифага кириш ҳуқуқи йўқ..');
        }

        return view('backend.warehouse.index', compact('warehouses'));
    }


    public function list(Request $request, $warehouse_id)
    {
        $dateService = new DateFilterService();

        if (in_array($this->roleTitle, ['Admin', 'Manager', 'Developer'])) {

            $warehouse = Warehouse::findOrFail($warehouse_id);

            $productQuery = ProductVariation::whereHas('product', function ($q) use ($warehouse_id) {$q->where('warehouse_id', $warehouse_id);});
            $rawQuery = RawMaterialVariation::whereHas('rawMaterial', function ($q) use ($warehouse_id) {$q->where('warehouse_id', $warehouse_id);});

        } elseif ($this->roleTitle === 'Moderator') {

            $user = Auth::user();

            $warehouse = Warehouse::whereHas('organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->findOrFail($warehouse_id);

            $productQuery = ProductVariation::whereHas('product.warehouse.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereHas('product', function ($q) use ($warehouse_id) {
                $q->where('warehouse_id', $warehouse_id);
            });

            // 🔹 RawMaterialVariation → RawMaterial → Warehouse → Organization
            $rawQuery = RawMaterialVariation::whereHas('rawMaterial.warehouse.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereHas('rawMaterial', function ($q) use ($warehouse_id) {
                $q->where('warehouse_id', $warehouse_id);
            });

        } else {
            abort(403, 'Сизда бу саҳифага кириш ҳуқуқи йўқ.');
        }

        // Filter borligini tekshirish
        $filters = $request->input('filters', []);
        $isFiltered = !empty($filters);

        if ($isFiltered) {
            // 🔹 Product filtr
            if (!empty($filters['code'])) $productQuery->where('code', 'ilike', "%{$filters['code']}%");
            if (!empty($filters['title'])) $productQuery->where('title', 'ilike', "%{$filters['title']}%");
            if (isset($filters['status']) && $filters['status'] !== '') {$productQuery->where('status', $filters['status']);}
            $dateService->applyDateToFilters($productQuery, $filters);

            // 🔹 Raw Material filtr
            if (!empty($filters['code'])) $rawQuery->where('code', 'ilike', "%{$filters['code']}%");
            if (!empty($filters['title'])) $rawQuery->where('title', 'ilike', "%{$filters['title']}%");
            if (isset($filters['status']) && $filters['status'] !== '') {$rawQuery->where('status', $filters['status']);}
            $dateService->applyDateToFilters($rawQuery, $filters);
        }

        $productAllCount = (clone $productQuery)->count();
        $productTotalPrice = (clone $productQuery)->sum('total_price');
        $productVariations = $productQuery->paginate(10, ['*'], 'product_page')->withQueryString();

        $rawAllCount = (clone $rawQuery)->count();
        $rawTotalPrice = (clone $rawQuery)->sum('total_price');
        $rawMaterialVariations = $rawQuery->paginate(10, ['*'], 'raw_page')->withQueryString();

        return view('backend.warehouse.list', compact(
            'warehouse',
            'productVariations',
            'rawMaterialVariations',
            'productAllCount',
            'productTotalPrice',
            'rawAllCount',
            'rawTotalPrice',
            'isFiltered'
        ));
    }

//    public function list(Request $request, $warehouse_id)
//    {
//        $warehouse = Warehouse::findOrFail($warehouse_id);
//
//        // Filter borligini tekshirish
//        $filters = $request->input('filters', []);
//        $isFiltered = !empty($filters);
//        $dateService = new DateFilterService();
//
//        // ✅ 1. Product Variations
//        $productQuery = ProductVariation::where('warehouse_id', $warehouse_id);
//
//        if ($isFiltered) {
//            if (!empty($filters['code'])) {
//                $productQuery->where('code', 'ilike', "%{$filters['code']}%");
//            }
//            if (!empty($filters['title'])) {
//                $productQuery->where('title', 'ilike', "%{$filters['title']}%");
//            }
//            if (isset($filters['status']) && $filters['status'] !== '') {
//                $productQuery->where('status', $filters['status']);
//            }
//
//            // 📅 Aniq sana (dd-mm-yyyy) filtrlash
//            $dateService->applyExactDateFilters($productQuery, $filters, ['created_at']);
//
//            // 🗓️ Oy-yil (mm-yyyy) filtrlash
//            $dateService->applyDateFilters($productQuery, $filters, ['created_at']);
//        }
//
//        $productAllCount = (clone $productQuery)->count();
//        $productTotalPrice = (clone $productQuery)->sum('total_price');
//        $productVariations = $productQuery->paginate(10, ['*'], 'product_page')->withQueryString();
//
//
//        // ✅ 2. Raw Material Variations
//        $rawQuery = RawMaterialVariation::where('warehouse_id', $warehouse_id);
//
//        if ($isFiltered) {
//            if (!empty($filters['code'])) {
//                $rawQuery->where('code', 'ilike', "%{$filters['code']}%");
//            }
//            if (!empty($filters['title'])) {
//                $rawQuery->where('title', 'ilike', "%{$filters['title']}%");
//            }
//            if (isset($filters['status']) && $filters['status'] !== '') {
//                $rawQuery->where('status', $filters['status']);
//            }
//
//            // 📅 Aniq sana (dd-mm-yyyy) filtrlash
//            $dateService->applyExactDateFilters($rawQuery, $filters, ['created_at']);
//
//            // 🗓️ Oy-yil (mm-yyyy) filtrlash
//            $dateService->applyDateFilters($rawQuery, $filters, ['created_at']);
//        }
//
//        $rawAllCount = (clone $rawQuery)->count();
//        $rawTotalPrice = (clone $rawQuery)->sum('total_price');
//        $rawMaterialVariations = $rawQuery->paginate(10, ['*'], 'raw_page')->withQueryString();
//
//        return view('backend.warehouse.list', compact(
//            'warehouse',
//            'productVariations',
//            'rawMaterialVariations',
//            'productAllCount',
//            'productTotalPrice',
//            'rawAllCount',
//            'rawTotalPrice',
//            'isFiltered'
//        ));
//    }


    public function addCount(Request $request)
    {
        $request->validate([
            'model_type' => 'required|in:product,raw_material',
            'id' => 'required|integer',
            'add_count' => 'required|numeric|min:0.001',
        ]);

        $model = $request->model_type === 'product'
            ? \App\Models\ProductVariation::find($request->id)
            : \App\Models\RawMaterialVariation::find($request->id);

        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Маълумот топилмади.']);
        }

        $model->count += $request->add_count;
        $model->total_price = $model->price * $model->count;
        $model->save();

        return response()->json([
            'success' => true,
            'title' => $model->title,
            'new_count' => $model->count,
            'new_total_price' => $model->total_price,
            'unit' => $model->unit,
        ]);
    }


    public function show(Warehouse $warehouse)
    {
        return view('backend.warehouse.show', compact('warehouse'));
    }

    public function create()
    {
        $organizations = Organization::pluck('title', 'id');
        $warehouse = new Warehouse();

        return view('backend.warehouse.create', compact('organizations', 'warehouse'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouse.index')->with('success', 'Омбор яратилди!');
    }

    public function edit(Warehouse $warehouse)
    {
        $organizations = Organization::pluck('title', 'id');
        return view('backend.warehouse.update', compact('organizations', 'warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouse.index')->with('success', 'Омбор янгиланди!');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return response()->json([
            'message' => 'Омбор ўчирилди!',
            'redirect' => route('warehouse.index')
        ]);
    }
}
