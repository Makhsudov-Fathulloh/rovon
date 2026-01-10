<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRates;
use App\Models\Organization;
use App\Models\RawMaterialTransfer;
use App\Models\RawMaterialTransferItem;
use App\Models\RawMaterialVariation;
use App\Models\Role;
use App\Models\Search\RawMaterialTransferSearch;
use App\Models\Section;
use App\Models\Shift;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RawMaterialTransferController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new RawMaterialTransferSearch(new DateFilterService());
        $query = $searchModel->applyFilters(RawMaterialTransfer::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('raw_material_transfer', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $organizations = RawMaterialTransfer::with('organization')->get()->pluck('organization.title', 'organization.id')->unique();
        $warehouses = RawMaterialTransfer::with('warehouse')->get()->pluck('warehouse.title', 'warehouse.id')->unique();
        $sections = RawMaterialTransfer::with('section')->get()->pluck('section.title', 'section.id')->unique();
        $shifts = RawMaterialTransfer::with('shift')->get()->pluck('shift.title', 'shift.id')->unique();
        $senders = RawMaterialTransfer::with('sender')->get()->pluck('sender.username', 'sender.id')->unique();
        $receivers = RawMaterialTransfer::with('receiver')->get()->pluck('receiver.username', 'receiver.id')->unique();

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            // Faqat filterlangan transferlar IDlari
            $transferIds = (clone $filteredQuery)->pluck('id');

            // Hisoblash – Item orqali
            $allCountUzs = RawMaterialTransferItem::whereIn('raw_material_transfer_id', $transferIds)->whereHas('rawMaterialVariation', fn($q) => $q->where('currency', StatusService::CURRENCY_UZS))->count();
            $allCountUsd = RawMaterialTransferItem::whereIn('raw_material_transfer_id', $transferIds)->whereHas('rawMaterialVariation', fn($q) => $q->where('currency', StatusService::CURRENCY_USD))->count();
            $totalPriceUzs = RawMaterialTransferItem::whereIn('raw_material_transfer_id', $transferIds)->whereHas('rawMaterialVariation', fn($q) => $q->where('currency', StatusService::CURRENCY_UZS))->sum('total_price');
            $totalPriceUsd = RawMaterialTransferItem::whereIn('raw_material_transfer_id', $transferIds)->whereHas('rawMaterialVariation', fn($q) => $q->where('currency', StatusService::CURRENCY_USD))->sum('total_price');
        }
        else {
            $allCountUzs = RawMaterialTransfer::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->count();
            $allCountUsd = RawMaterialTransfer::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->count();
            $totalPriceUzs = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_UZS);})->whereYear('created_at', now()->year)->sum('total_price');
            $totalPriceUsd = RawMaterialTransferItem::whereHas('rawMaterialVariation', function ($q) {$q->where('currency', StatusService::CURRENCY_USD);})->whereYear('created_at', now()->year)->sum('total_price');
        }

        $transfers = $query->paginate(20)->withQueryString();

        return view('backend.raw-material-transfer.index', compact(
            'transfers',
            'organizations',
            'warehouses',
            'sections',
            'shifts',
            'senders',
            'receivers',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'totalPriceUzs',
            'totalPriceUsd',
        ));
    }


    public function show(RawMaterialTransfer $rawMaterialTransfer)
    {
        return view('backend.raw-material-transfer.show', compact('rawMaterialTransfer'));
    }


    public function create()
    {
        $organizations = Organization::pluck('title', 'id');
        $warehouses = Warehouse::pluck('title', 'id');
        $sections = Section::pluck('title', 'id');
        $shifts = Shift::pluck('title', 'id');
        $roleIds = Role::where('title', '!=', 'Client')->where('title', '!=', 'Worker')->where('title', '!=', 'Developer')->pluck('id');
        $users = User::whereIn('role_id', $roleIds)->pluck('username', 'id');
        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 0;

        // Bu sahifada ombordan tanlangandan keyin AJAX orqali to‘liqlanadi.
        $rawMaterials = collect();

        return view('backend.raw-material-transfer.create', compact(
            'organizations',
            'warehouses',
            'sections',
            'shifts',
            'users',
            'rawMaterials',
            'usdRate'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'organization_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'section_id' => 'required|integer',
            'shift_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'receiver_id' => 'required|integer',
            'status' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.raw_material_variation_id' => 'required|integer|exists:raw_material_variation,id',
            'items.*.count' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $transfer = RawMaterialTransfer::create([
                    'organization_id' => $data['organization_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'section_id' => $data['section_id'],
                    'shift_id' => $data['shift_id'],
                    'title' => $data['title'],
                    'sender_id' => Auth::id(),
                    'receiver_id' => $data['receiver_id'],
                    'total_item_price' => 0,
                    'status' => $data['status'],
                ]);

                $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 0;
                $total = 0;

                foreach ($data['items'] as $item) {
                    $variation = RawMaterialVariation::find($item['raw_material_variation_id']);

                    // 💱 Valyutaga qarab narxni so‘mda hisoblash
                    $priceInUzs = $variation->currency == \App\Services\StatusService::CURRENCY_USD
                        ? ($variation->price * $usdRate)
                        : $variation->price;

                    $totalPrice = $priceInUzs * $item['count'];
                    $total += $totalPrice;

                    $transfer->items()->create([
                        'raw_material_variation_id' => $item['raw_material_variation_id'],
                        'count' => $item['count'],
                        'unit' => $variation->unit,
                        'price' => $variation->price,
                        'currency' => $variation->currency,
                        'total_price' => $totalPrice,
                    ]);
                }

                $transfer->update(['total_item_price' => $total]);
            });

            return redirect()->route('raw-material-transfer.index')->with('success', 'Хомашё трансфери яратилди!');

        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]); // Modeldagi RuntimeException
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['general' => 'Кутилмаган хато: ' . $e->getMessage()]); // boshqa xatoliklar
        }
    }


    public function edit(RawMaterialTransfer $rawMaterialTransfer)
    {
        $organizations = Organization::pluck('title', 'id');
        $warehouses = Warehouse::pluck('title', 'id');
        $sections = Section::pluck('title', 'id');
        $shifts = Shift::pluck('title', 'id');

        $roleIds = Role::where('title', '!=', 'Client')->where('title', '!=', 'Worker')->where('title', '!=', 'Developer')->pluck('id');
        $users = User::whereIn('role_id', $roleIds)->pluck('username', 'id');

        // Transferga bog‘langan itemlar bilan birga yuklaymiz
        $rawMaterialTransfer->load('items.rawMaterialVariation');

        $rawMaterials = RawMaterialVariation::whereHas('rawMaterial', function ($q) use ($rawMaterialTransfer) {
            $q->where('warehouse_id', $rawMaterialTransfer->warehouse_id);
        })->select('id', 'code', 'title', 'price', 'count', 'unit', 'currency')->get();

        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 0;

        return view('backend.raw-material-transfer.update', compact(
            'organizations',
            'warehouses',
            'sections',
            'shifts',
            'users',
            'rawMaterials',
            'rawMaterialTransfer',
            'usdRate',
        ));
    }

    public function update(Request $request, RawMaterialTransfer $rawMaterialTransfer)
    {
        $data = $request->validate([
            'organization_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'section_id' => 'required|integer',
            'shift_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'receiver_id' => 'required|integer',
            'status' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.raw_material_variation_id' => 'required|integer|exists:raw_material_variation,id',
            'items.*.count' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::transaction(function () use ($data, $rawMaterialTransfer, $request) {

                // 1️⃣ O‘chirilgan itemlarni stockga qaytarish va delete
                if($request->has('deleted_items')){
                    $deletedIds = $request->input('deleted_items');
                    $deletedItems = RawMaterialTransferItem::whereIn('id', $deletedIds)->get();
                    foreach($deletedItems as $oldItem){
                        $oldItem->rawMaterialVariation?->incrementStock($oldItem->count);
                        $oldItem->delete();
                    }
                }

                // 2️⃣ Eski itemlarni omborga qaytarish (faqat o‘zgarmagan itemlar uchun)
                $oldItems = $rawMaterialTransfer->items()->get();
                foreach ($oldItems as $oldItem) {
                    $oldItem->rawMaterialVariation?->incrementStock($oldItem->count);
                }

                // 3️⃣ Eski itemlarni o‘chirib tashlash
                $rawMaterialTransfer->items()->delete();

                // 4️⃣ Transfer ma’lumotlarini yangilash
                $rawMaterialTransfer->update([
                    'organization_id' => $data['organization_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'section_id' => $data['section_id'],
                    'shift_id' => $data['shift_id'],
                    'title' => $data['title'],
                    'receiver_id' => $data['receiver_id'],
                    'status' => $data['status'],
                ]);

                // 5️⃣ Yangi itemlarni yaratish (creating event allaqachon stockni ayiradi)
                $usdRate = \App\Models\ExchangeRates::where('currency', 'USD')->value('rate') ?? 0;
                $total = 0;

                foreach ($data['items'] as $item) {
                    $count = round((float)$item['count'], 3);
                    $variation = RawMaterialVariation::find($item['raw_material_variation_id']);

                    // 💱 Valyutaga qarab narxni so‘mda hisoblash
                    $priceInUzs = $variation->currency == \App\Services\StatusService::CURRENCY_USD
                        ? ($variation->price * $usdRate)
                        : $variation->price;

                    $totalPrice = $priceInUzs * $item['count'];
                    $total += $totalPrice;

                    $rawMaterialTransfer->items()->create([
                        'raw_material_variation_id' => $variation->id,
                        'count' => $count,
                        'unit' => $variation->unit,
                        'price' => $variation->price,
                        'currency' => $variation->currency,
                        'total_price' => $totalPrice,
                    ]);
                }

                // 6️⃣ Umumiy summani yangilash
                $rawMaterialTransfer->update(['total_item_price' => $total]);
            });

            return redirect()
                ->route('raw-material-transfer.index')
                ->with('success', 'Трансфер муваффақиятли янгиланди!');

        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['general' => 'Кутилмаган хато: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX orqali warehouse bo‘yicha xomashyolarni olish
     */
    public function getRawMaterials(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        if (!$warehouseId) return response()->json([]);

        $rawMaterials = RawMaterialVariation::whereHas('rawMaterial', function ($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        })
            ->select('id', 'code', 'title', 'price', 'count', 'unit', 'currency')
            ->get();

        return response()->json($rawMaterials);
    }


    public function destroy(RawMaterialTransfer $rawMaterialTransfer)
    {
        DB::transaction(function () use ($rawMaterialTransfer) {
            foreach ($rawMaterialTransfer->items as $item) {
                $item->delete(); // deleted event ishlaydi
            }
            $rawMaterialTransfer->delete();
        });

        return response()->json([
            'message' => 'Хомашё трансфери ўчирилди!',
            'type' => 'delete',
            'redirect' => route('raw-material-transfer.index')
        ]);
    }
}
