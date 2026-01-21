<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\ProductLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ExchangeRates;
use App\Services\StatusService;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Search\ProductVariationSearch;

class ProductVariationController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ProductVariationSearch(new DateFilterService());
        $query = $searchModel->applyFilters(ProductVariation::query()->with('product'), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('product_variation', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $productIds = ProductVariation::distinct()->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->pluck('title', 'id');

        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCountUzs = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_UZS)->count();
            $allCountUsd = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_USD)->count();

            $uzsTotal = (clone $filteredQuery)->reorder()->where('currency', StatusService::CURRENCY_UZS)->sum(DB::raw('body_price * count'));
            $usdTotal = (clone $filteredQuery)->reorder()->where('currency', StatusService::CURRENCY_USD)->sum(DB::raw('body_price * count'));

            $totalPrice = (clone $filteredQuery)->sum('total_price');
        } else {
            $allCountUzs = ProductVariation::where('currency', StatusService::CURRENCY_UZS)->count();
            $allCountUsd = ProductVariation::where('currency', StatusService::CURRENCY_USD)->count();

            $uzsTotal = ProductVariation::where('currency', StatusService::CURRENCY_UZS)->selectRaw('SUM(body_price * count) as total')->value('total');
            $usdTotal = ProductVariation::where('currency', StatusService::CURRENCY_USD)->selectRaw('SUM(body_price * count) as total')->value('total');

            $totalPrice = ProductVariation::whereYear('created_at', now()->year)->sum('total_price');
        }

        $bodyPriceTotal = ($uzsTotal ?? 0) + (($usdTotal ?? 0) * $usdRate);

        $productVariations = $query->paginate(20)->withQueryString();

        return view('backend.product-variation.index', compact(
            'productVariations',
            'products',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'bodyPriceTotal',
            'totalPrice'
        ));
    }


    public function list(Request $request, $product_id)
    {
        $product = Product::findOrFail($product_id);

        $searchModel = new ProductVariationSearch(new DateFilterService());
        $query = ProductVariation::query()->where('product_id', $product_id)->with('product');
        $query = $searchModel->applyFilters($query, $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('product_variation', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCountUzs = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_UZS)->count();
            $allCountUsd = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_USD)->count();

            $uzsTotal = (clone $filteredQuery)->reorder()->where('currency', StatusService::CURRENCY_UZS)->sum(DB::raw('body_price * count'));
            $usdTotal = (clone $filteredQuery)->reorder()->where('currency', StatusService::CURRENCY_USD)->sum(DB::raw('body_price * count'));

            $totalPrice = (clone $filteredQuery)->sum('total_price');
        } else {
            $allCountUzs = ProductVariation::where('currency', StatusService::CURRENCY_UZS)->where('product_id', $product_id)->count();
            $allCountUsd = ProductVariation::where('currency', StatusService::CURRENCY_USD)->where('product_id', $product_id)->count();

            $uzsTotal = ProductVariation::where('currency', StatusService::CURRENCY_UZS)->where('product_id', $product_id)->selectRaw('SUM(body_price * count) as total')->value('total');
            $usdTotal = ProductVariation::where('currency', StatusService::CURRENCY_USD)->where('product_id', $product_id)->selectRaw('SUM(body_price * count) as total')->value('total');

            $totalPrice = ProductVariation::where('product_id', $product_id)->sum('total_price');
        }

        $bodyPriceTotal = ($uzsTotal ?? 0) + (($usdTotal ?? 0) * $usdRate);

        $productVariations = $query->paginate(20)->withQueryString();

        return view('backend.product-variation.list', compact(
            'product',
            'productVariations',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'bodyPriceTotal',
            'totalPrice'
        ));
    }

    public function addCount(Request $request, ProductVariation $variation)
    {
        $request->validate([
            'add_count' => 'required|integer|min:1',
        ]);

        $variation->total_price = $variation->count * $variation->price;
        $variation->save();

        DB::transaction(function () use ($request, $variation) {

            $oldCount = $variation->count;
            $addCount = $request->add_count;

            $variation->incrementStock($addCount);

            ProductLog::create([
                'product_variation_id' => $variation->id,
                'old_count'   => $oldCount,
                'added_count' => $addCount,
                'new_count'   => $variation->count,
                'user_id'     => auth()->id(),
                'action'      => 'add_count',
            ]);
        });

        return response()->json([
            'success' => true,
            'title' => $variation->title,
            'new_count' => $variation->count,
            'new_total_price' => $variation->total_price,
            'unit' => $variation->unit,

            'all_som_count' => ProductVariation::where('currency', StatusService::CURRENCY_UZS)->count(),
            'all_dollar_count' => ProductVariation::where('currency', StatusService::CURRENCY_USD)->count(),
            'total_price' => ProductVariation::sum('total_price'),
        ]);
    }


    public function show(ProductVariation $productVariation)
    {
        return view('backend.product-variation.show', compact('productVariation'));
    }


    public function create($product_id)
    {
        $product = Product::findOrFail($product_id);
        $productVariation = new ProductVariation();

        return view('backend.product-variation.create', compact('product', 'productVariation'));
    }


    public function store(Request $request, $product_id)
    {
        $request->merge([
            'body_price' => str_replace(' ', '', $request->body_price) ?: 0,
            'price' => str_replace(' ', '', $request->price),
            'count' => str_replace(' ', '', $request->count),
            'min_count' => str_replace(' ', '', $request->min_count),
            'unit' => $request->input('unit', 1),
        ]);

        $validated = $request->validate(
            [
                'code' => 'nullable|string|max:50',
                'product_id' => 'exists:product,id',
                'title' => 'required|string|max:100',
                'subtitle' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'body_price' => 'nullable|numeric|min:0',
                'count' => 'required|numeric|min:0',
                'min_count' => 'nullable|numeric|min:0',
                'unit' => 'required|integer|',
                'price' => 'required|numeric|min:0',
                'currency' => 'required|in:1,2',
                'type' => 'nullable|string|max:100',
                'top' => 'required|integer',
                'status' => 'required|integer',
            ],
            [
                'title.required' => 'Модел номи мажбурий.',
                'title.string' => 'Модел номи матн бўлиши керак.',
                'title.max' => 'Модел номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'count.required' => 'Сонини киритиш мажбурий.',
                'price.required' => 'Нарҳини киритиш мажбурий.',
            ]
        );

        $product = Product::findOrFail($product_id);

        $productVariation = new ProductVariation($validated);
        $productVariation->code = $request->code;
        $productVariation->product_id = $product->id;
        $productVariation->title = $request->title;
        $productVariation->subtitle = $request->subtitle;
        $productVariation->description = $request->description;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $path = $request->file('image')->store('files', 'public');

            $file = new \App\Models\File();
            $file->name = $uploadedFile->getClientOriginalName();
            $file->path = $path;
            $file->extension = $uploadedFile->getClientOriginalExtension();
            $file->mime_type = $uploadedFile->getMimeType();
            $file->size = $uploadedFile->getSize();
            $file->date_create = time();
            $file->save();

            $productVariation->image = $file->id;
        }

        $productVariation->body_price = $request->body_price;

        $productVariation->count = $request->count;
        $productVariation->min_count = $request->min_count;
        $productVariation->unit = $request->unit;
        $productVariation->price = $request->price;

        // 💰 USD bo‘lsa, kursni olish
        if ($productVariation->currency == StatusService::CURRENCY_USD) {
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
            $productVariation->rate = $usdRate;
            $productVariation->price_uzs = $productVariation->price * $usdRate;
        } else {
            $productVariation->rate = 1;
            $productVariation->price_uzs = $productVariation->price;
        }

        $productVariation->total_price = $productVariation->price * $productVariation->count;
        $productVariation->type = $request->type;
        $productVariation->top = $request->top;
        $productVariation->status = $request->status;

        $productVariation->save();

        return redirect()->route('product.index');
        // return redirect()->route('product-variation.list', $product->id)->with('success', 'Махсулот яратилди!');
    }


    public function edit(ProductVariation $productVariation)
    {
        $productDropdown = Product::getDropdownList();
        return view('backend.product-variation.update', compact('productVariation', 'productDropdown'));
    }


    public function update(Request $request, ProductVariation $productVariation)
    {
        $request->merge([
            'body_price' => str_replace(' ', '', $request->body_price) ?: 0,
            'price' => str_replace(' ', '', $request->price),
            'count' => str_replace(' ', '', $request->count),
            'min_count' => str_replace(' ', '', $request->min_count),
            'unit' => $request->input('unit', 1),
        ]);

        $request->validate(
            [
                'code' => 'nullable|string|max:50',
                'product_id' => 'exists:product,id',
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'body_price' => 'nullable|numeric|min:0',
                'count' => 'required|numeric|min:0',
                'min_count' => 'nullable|numeric|min:0',
                'unit' => 'required|integer',
                'price' => 'required|numeric|min:0',
                'currency' => 'required|in:1,2',
                'type' => 'nullable|string|max:255',
                'top' => 'required|integer',
                'status' => 'required|integer',
            ],
            [
                'product_id.required' => 'Моделни белгилаш мажбурий.',
                'title.required' => 'Модел номи мажбурий.',
                'title.string' => 'Модел номи матн бўлиши керак.',
                'title.max' => 'Модел номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'count.required' => 'Сонини киритиш мажбурий.',
                'price.required' => 'Нарҳини киритиш мажбурий.',
            ]
        );

        $productVariation->code = $request->code;
        $productVariation->product_id = $request->product_id;
        $productVariation->title = $request->title;
        $productVariation->subtitle = $request->subtitle;
        $productVariation->description = $request->description;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $path = $uploadedFile->store('files', 'public');

            $file = new \App\Models\File();
            $file->name = $uploadedFile->getClientOriginalName();
            $file->path = $path;
            $file->extension = $uploadedFile->getClientOriginalExtension();
            $file->mime_type = $uploadedFile->getMimeType();
            $file->size = $uploadedFile->getSize();
            $file->date_create = time();
            $file->save();

            $productVariation->image = $file->id;
        }

        $productVariation->body_price = $request->body_price;

        $productVariation->count = $request->count;
        $productVariation->min_count = $request->min_count;
        $productVariation->unit = $request->unit;
        $productVariation->price = $request->price;
        $productVariation->currency = $request->currency;

        // 💰 USD bo‘lsa, kursni olish
        if ($productVariation->currency == StatusService::CURRENCY_USD) {
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
            $productVariation->rate = $usdRate;
            $productVariation->price_uzs = $productVariation->price * $usdRate;
        } else {
            $productVariation->rate = 1;
            $productVariation->price_uzs = $productVariation->price;
        }
        $productVariation->total_price = $productVariation->price * $productVariation->count;
        $productVariation->type = $request->type;
        $productVariation->top = $request->top;
        $productVariation->status = $request->status;

        $productVariation->save();

        return redirect()->route('product.index');
        // return redirect()->route('product-variation.list', $productVariation->product_id)->with('success', 'Махсулот янгиланди!');
    }


    public function destroy(ProductVariation $productVariation)
    {
        if ($productVariation->file) {
            Storage::disk('public')->delete($productVariation->file->path);
            $productVariation->file->delete();
        }
        $productVariation->delete();

        return response()->json([
            'message' => 'Махсулот ўчирилди!',
            'type' => 'delete',
            'redirect' => route('product-variation.index')
        ]);
    }
}
