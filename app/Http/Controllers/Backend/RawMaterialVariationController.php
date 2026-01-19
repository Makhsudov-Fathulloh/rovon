<?php

namespace App\Http\Controllers\Backend;

use App\Models\MaterialLog;
use App\Models\RawMaterial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ExchangeRates;
use App\Services\StatusService;
use Illuminate\Support\Facades\DB;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use App\Models\RawMaterialVariation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Search\RawMaterialVariationSearch;

class RawMaterialVariationController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new RawMaterialVariationSearch(new DateFilterService());
        $query = $searchModel->applyFilters(RawMaterialVariation::query()->with('rawMaterial'), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('raw_material_variation', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $rawMaterialIds = RawMaterialVariation::distinct()->pluck('raw_material_id');
        $rawMaterials = RawMaterial::whereIn('id', $rawMaterialIds)->pluck('title', 'id');

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCountUzs = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->count();
            $allCountUsd = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->count();
            $totalPrice = (clone $filteredQuery)->whereYear('created_at', now()->year)->sum('total_price');
        } else {
            $allCountUzs = RawMaterialVariation::where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->count();
            $allCountUsd = RawMaterialVariation::where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->count();
            $totalPrice = RawMaterialVariation::whereYear('created_at', now()->year)->sum('total_price');
        }

        $rawMaterialVariations = $query->paginate(20)->withQueryString();

        return view('backend.raw-material-variation.index', compact(
            'rawMaterialVariations',
            'rawMaterials',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'totalPrice',
        ));
    }


    public function list(Request $request, $rawMaterial_id)
    {
        $rawMaterial = RawMaterial::findOrFail($rawMaterial_id);

        $searchModel = new RawMaterialVariationSearch(new DateFilterService());
        $query = RawMaterialVariation::query()->where('raw_material_id', $rawMaterial_id)->with('rawMaterial');
        $query = $searchModel->applyFilters($query, $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('raw_material_variation', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $filteredQuery = clone $query;

            $allCountUzs = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_UZS)->whereYear('created_at', now()->year)->count();
            $allCountUsd = (clone $filteredQuery)->where('currency', StatusService::CURRENCY_USD)->whereYear('created_at', now()->year)->count();
            $totalPrice = (clone $filteredQuery)->whereYear('created_at', now()->year)->sum('total_price');
        } else {
            $allCountUzs = RawMaterialVariation::where('currency', StatusService::CURRENCY_UZS)->where('raw_material_id', $rawMaterial_id)->whereYear('created_at', now()->year)->count();
            $allCountUsd = RawMaterialVariation::where('currency', StatusService::CURRENCY_USD)->where('raw_material_id', $rawMaterial_id)->whereYear('created_at', now()->year)->count();
            $totalPrice = RawMaterialVariation::where('raw_material_id', $rawMaterial_id)->whereYear('created_at', now()->year)->sum('total_price');
        }

        $rawMaterialVariations = $query->paginate(20)->withQueryString();

        return view('backend.raw-material-variation.list', compact(
            'rawMaterial',
            'rawMaterialVariations',
            'isFiltered',
            'allCountUzs',
            'allCountUsd',
            'totalPrice',
        ));
    }


    public function addCount(Request $request, RawMaterialVariation $variation)
    {
        $request->validate([
            'add_count' => 'required|numeric|min:0.001',
        ]);

        $variation->total_price = $variation->count * $variation->price;
        $variation->save();

        DB::transaction(function () use ($request, $variation) {

            $oldCount = $variation->count;
            $addCount = $request->add_count;

            $variation->incrementStock($addCount);

            MaterialLog::create([
                'raw_material_variation_id' => $variation->id,
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

            'all_som_count' => RawMaterialVariation::where('currency', StatusService::CURRENCY_UZS)->count(),
            'all_dollar_count' => RawMaterialVariation::where('currency', StatusService::CURRENCY_USD)->count(),
            'total_price' => RawMaterialVariation::sum('total_price'),
        ]);
    }


    public function show(RawMaterialVariation $rawMaterialVariation)
    {
        return view('backend.raw-material-variation.show', compact('rawMaterialVariation'));
    }


    public function create($rawMaterial_id)
    {
        $rawMaterial = RawMaterial::findOrFail($rawMaterial_id);
        $rawMaterialVariation = new RawMaterialVariation();

        return view('backend.raw-material-variation.create', compact('rawMaterial', 'rawMaterialVariation'));
    }

    public function store(Request $request, $rawMaterial_id)
    {
        $request->merge([
            'price' => str_replace(' ', '', $request->price),
            'count' => str_replace(' ', '', $request->count),
            'min_count' => str_replace(' ', '', $request->min_count),
            'unit' => $request->input('unit', 1),
        ]);

        $validated = $request->validate(
            [
                'code' => 'nullable|string|max:50',
                'raw_material_id' => 'exists:raw_material,id',
                'title' => 'required|string|max:100',
                'subtitle' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'count' => 'required|numeric|min:0',
                'min_count' => 'required|numeric|min:0',
                'unit' => 'required|integer',
                'price' => 'required|numeric|min:0',
                'currency' => 'required|in:1,2',
                'type' => 'nullable|string|max:100',
                'status' => 'required|integer',
            ],
            [
                'title.required' => 'Хомашё номи мажбурий.',
                'title.string' => 'Хомашё номи матн бўлиши керак.',
                'title.max' => 'Хомашё номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'count.required' => 'Микдорини киритиш мажбурий.',
                'min_count.required' => 'Минимал микдорини киритиш мажбурий.',
                'price.required' => 'Нарҳини киритиш мажбурий.',
            ]
        );

        $rawMaterial = RawMaterial::findOrFail($rawMaterial_id);

        $rawMaterialVariation = new RawMaterialVariation($validated);
        $rawMaterialVariation->code = $request->code;
        $rawMaterialVariation->raw_material_id = $rawMaterial->id;
        $rawMaterialVariation->title = $request->title;
        $rawMaterialVariation->subtitle = $request->subtitle;
        $rawMaterialVariation->description = $request->description;

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

            $rawMaterialVariation->image = $file->id;
        }

        $rawMaterialVariation->count = $request->count;
        $rawMaterialVariation->min_count = $request->min_count;
        $rawMaterialVariation->unit = $request->unit;
        $rawMaterialVariation->price = $request->price;
//        $rawMaterialVariation->currency = $request->currency;

        // 💰 USD bo‘lsa, kursni olish
        if ($rawMaterialVariation->currency == StatusService::CURRENCY_USD) {
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate');
            $rawMaterialVariation->rate = $usdRate;
            $rawMaterialVariation->price_uzs = $rawMaterialVariation->price * $usdRate;
        } else {
            $rawMaterialVariation->rate = 1;
            $rawMaterialVariation->price_uzs = $rawMaterialVariation->price;
        }

        $rawMaterialVariation->total_price = $rawMaterialVariation->price_uzs * $rawMaterialVariation->count;
        $rawMaterialVariation->type = $request->type;
        $rawMaterialVariation->status = $request->status;

        $rawMaterialVariation->save();

        return redirect()->route('raw-material.index');
//        return redirect()->route('raw-material-variation.list', $rawMaterial->id)->with('success', 'Хомашё яратилди!');
    }


    public function edit(RawMaterialVariation $rawMaterialVariation)
    {
        $rawMaterialDropdown = RawMaterial::getDropdownList();
        return view('backend.raw-material-variation.update', compact('rawMaterialVariation', 'rawMaterialDropdown'));
    }

    public function update(Request $request, RawMaterialVariation $rawMaterialVariation)
    {
        $request->merge([
            'price' => str_replace(' ', '', $request->price),
            'count' => str_replace(' ', '', $request->count),
            'min_count' => str_replace(' ', '', $request->min_count),
        ]);

        $request->validate(
            [
                'code' => 'nullable|string|max:50',
                'raw_material_id' => 'exists:raw_material,id',
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'count' => 'required|numeric|min:0',
                'min_count' => 'required|numeric|min:0',
                'unit' => 'required|integer',
                'price' => 'required|numeric|min:0',
                'currency' => 'required|in:1,2',
                'type' => 'nullable|string|max:255',
                'status' => 'required|integer',
            ],
            [
                'raw_material_id.required' => 'Хомашёни белгилаш мажбурий.',
                'title.required' => 'Хомашё номи мажбурий.',
                'title.string' => 'Хомашё номи матн бўлиши керак.',
                'title.max' => 'Хомашё номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'count.required' => 'Микдорини киритиш мажбурий.',
                'min_count.required' => 'Минимал микдорини киритиш мажбурий.',
                'price.required' => 'Нарҳини киритиш мажбурий.',
            ]
        );

        $rawMaterialVariation->code = $request->code;
        $rawMaterialVariation->raw_material_id = $request->raw_material_id;
        $rawMaterialVariation->title = $request->title;
        $rawMaterialVariation->subtitle = $request->subtitle;
        $rawMaterialVariation->description = $request->description;

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

            $rawMaterialVariation->image = $file->id;
        }

        $rawMaterialVariation->count = $request->count;
        $rawMaterialVariation->min_count = $request->min_count;
        $rawMaterialVariation->unit = $request->unit;
        $rawMaterialVariation->price = $request->price;
        $rawMaterialVariation->currency = $request->currency;

        // 💰 USD bo‘lsa, kursni olish
        if ($rawMaterialVariation->currency == 2) {
            $usdRate = ExchangeRates::where('currency', 'USD')->value('rate') ?? 12800;
            $rawMaterialVariation->rate = $usdRate;
            $rawMaterialVariation->price_uzs = $rawMaterialVariation->price * $usdRate;
        } else {
            $rawMaterialVariation->rate = 1;
            $rawMaterialVariation->price_uzs = $rawMaterialVariation->price;
        }

        $rawMaterialVariation->total_price = $rawMaterialVariation->price_uzs * $rawMaterialVariation->count;
        $rawMaterialVariation->type = $request->type;
        $rawMaterialVariation->status = $request->status;

        $rawMaterialVariation->save();

        return redirect()->route('raw-material.index');
//        return redirect()->route('raw-material-variation.list', $rawMaterialVariation->raw_material_id)->with('success', 'Хомашё янгиланди!');
    }


    public function destroy(RawMaterialVariation $rawMaterialVariation)
    {
        if ($rawMaterialVariation->file) {
            Storage::disk('public')->delete($rawMaterialVariation->file->path);
            $rawMaterialVariation->file->delete();
        }
        $rawMaterialVariation->delete();

        return response()->json([
            'message' => 'Хомашё ўчирилди!',
            'type' => 'delete',
            'redirect' => route('raw-material-variation.index')
        ]);
    }
}
