<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use App\Models\PreOrder;
use Illuminate\Support\Str;
use App\Models\PreOrderItem;
use Illuminate\Http\Request;
use App\Helpers\TelegramHelper;
use App\Models\ProductVariation;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Search\PreOrderSearch;
use Illuminate\Support\Facades\Schema;

class PreOrderController extends Controller
{
    public function index(Request $request)
    {
//        $filters = $request->only(['code', 'title', 'user_id', 'customer_id', 'status']);
//
//        $orders = PreOrder::with(['productVariation', 'customer', 'creator'])
//            ->filter($filters)
//            ->latest()
//            ->paginate(20)
//            ->withQueryString();
//
//        return view('backend.pre-order.index', compact('orders', 'filters'));

        $searchModel = new PreOrderSearch(new DateFilterService());
        $query = $searchModel->applyFilters(PreOrder::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('pre_order', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $userIds = PreOrder::distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->where('role_id', Role::where('title', 'Client')->value('id'))->pluck('username', 'id');
        $customers = User::whereIn('id', PreOrder::pluck('customer_id'))->orderBy('username')->pluck('username', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $baseQuery = clone $query;

            $orderNew = (clone $baseQuery)->where('status', PreOrder::STATUS_NEW)->whereYear('created_at', now()->year)->count();
            $orderInprogress = (clone $baseQuery)->where('status', PreOrder::STATUS_INPROGRESS)->whereYear('created_at', now()->year)->count();
            $orderDone = (clone $baseQuery)->where('status', PreOrder::STATUS_DONE)->whereYear('created_at', now()->year)->count();
            $orderCanceled = (clone $baseQuery)->where('status', PreOrder::STATUS_CANCELLED)->whereYear('created_at', now()->year)->count();
        } else {
            $orderNew = PreOrder::where('status', PreOrder::STATUS_NEW)->whereYear('created_at', now()->year)->count();
            $orderInprogress = PreOrder::where('status', PreOrder::STATUS_INPROGRESS)->whereYear('created_at', now()->year)->count();
            $orderDone = PreOrder::where('status', PreOrder::STATUS_DONE)->whereYear('created_at', now()->year)->count();
            $orderCanceled = PreOrder::where('status', PreOrder::STATUS_CANCELLED)->whereYear('created_at', now()->year)->count();
        }

        $pre_orders = $query->paginate(20)->withQueryString();

        return view('backend.pre-order.index', compact(
            'pre_orders',
            'customers',
            'users',
            'orderNew',
            'orderInprogress',
            'orderDone',
            'orderCanceled'
        ));
    }


    public function show(PreOrder $pre_order)
    {
        $pre_order->load(['user', 'customer', 'preOrderItems.productVariation.product']);

        return view('backend.pre-order.show', compact('pre_order'));
    }


    public function create()
    {
        $pre_order = new PreOrder();
        $users = User::where('role_id', Role::where('title', 'Client')->value('id'))->get();

        return view('backend.pre-order.create', compact('pre_order', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:user,id'],
            'title' => ['required', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variation_id' => ['required', 'exists:product_variation,id'],
            'items.*.count' => ['required', 'numeric', 'gt:0'],
        ]);

        $pre_order = PreOrder::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'count' => count($validated['items']),
//            'count' => collect($validated['items'])->sum('count'),
            'user_id'     => $validated['user_id'],
            'customer_id' => Auth::id(), // kim yaratgan
            'status'      => PreOrder::STATUS_NEW,
        ]);

        // ✅ ITEMLARNI SAQLASH
        foreach ($validated['items'] as $item) {

            $pv = ProductVariation::select('id','code','title','unit')
                ->findOrFail($item['product_variation_id']);

            PreOrderItem::create([
                'pre_order_id'         => $pre_order->id,
                'product_variation_id' => $pv->id,
                'code'                 => $pv->code ?? '',
                'title'                => $pv->title,
                'count'                => $item['count'],
                'unit'           => $pv->unit,
            ]);
        }

        // After save
        $pre_order->load(['user', 'customer', 'preOrderItems']);
        $message = TelegramHelper::preOrderMessage($pre_order);

        TelegramHelper::notifyPreOrder($message);

        return redirect()->route('pre-order.index')->with('success', 'Навбатдаги буюртма яратилди!');
    }


    public function edit(PreOrder $pre_order)
    {
        $pre_order->load('preOrderItems');
        $users = User::where('role_id', Role::where('title', 'Client')->value('id'))->get();

        return view('backend.pre-order.update', compact('pre_order', 'users'));
    }

    public function update(Request $request, PreOrder $pre_order)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:user,id'],
            'title' => ['required', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status'      => ['required', 'in:1,2,3,4'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variation_id' => ['required', 'exists:product_variation,id'],
            'items.*.count' => ['required', 'numeric', 'gt:0'],
        ]);

        $pre_order->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'count' => count($validated['items']),
//            'count' => collect($validated['items'])->sum('count'),
            'user_id' => $validated['user_id'],
            'status'      => $validated['status'],
        ]);

        // ✅ DELETE QILSANG BO‘LADI
        $pre_order->preOrderItems()->delete();

        // ✅ YANGI ITEMLAR
        foreach ($validated['items'] as $item) {

            $pv = ProductVariation::select('id','code','title','unit')
                ->findOrFail($item['product_variation_id']);

            PreOrderItem::create([
                'pre_order_id'         => $pre_order->id,
                'product_variation_id' => $pv->id,
                'code'                 => $pv->code ?? '',
                'title'                => $pv->title,
                'count'                => $item['count'],
                'unit'           => $pv->unit,
            ]);
        }

        return redirect()->route('pre-order.index')
            ->with('success', 'Навбатдаги буюртма янгиланди!');
    }


    public function destroy(PreOrder $preOrder)
    {
        $preOrder->delete();

        return response()->json([
            'message' => 'Навбатдаги буюртма ўчирилди!',
            'type' => 'delete',
            'redirect' => route('pre-order.index')
        ]);
    }

    // Tezkor "yakunlash" (DONE) action (POST)
    public function complete(Request $request, PreOrder $pre_order)
    {
        if ($pre_order->status !== PreOrder::STATUS_DONE) {
            $pre_order->status = PreOrder::STATUS_DONE;
            $pre_order->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $pre_order->status
            ]);
        }

        // After save
        $pre_order->load(['user', 'customer', 'preOrderItems']);

        $message = TelegramHelper::preOrderMessage($pre_order, 'update');
        TelegramHelper::notifyPreOrder($message);

        return back()->with('success', 'Навбатдаги буюртма якунланди!');
    }

    // ✅ SELECT2 AJAX QIDIRUV
    public function searchProduct(Request $request)
    {
        $q = trim($request->get('q', ''));

        $items = ProductVariation::query()
            ->select('id', 'code', 'title')
            ->when($q, fn($qq) =>
            $qq->where(function($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%");
            })
            )
            ->orderByRaw("CASE WHEN code LIKE ? THEN 0 ELSE 1 END", ["%{$q}%"])
            ->orderBy('title')
            ->limit(20)
            ->get()
            ->map(fn($pv) => [
                'id' => $pv->id,
                'text' => ($pv->code ? "{$pv->code} — " : '') . $pv->title,
                'code' => $pv->code,
                'title' => $pv->title,
            ]);

        return response()->json(['results' => $items]);
    }
}
