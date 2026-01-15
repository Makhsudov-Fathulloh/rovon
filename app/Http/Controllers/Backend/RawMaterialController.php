<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\RawMaterial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\StatusService;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Search\RawMaterialSearch;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new RawMaterialSearch(new DateFilterService());
        $query = $searchModel->applyFilters(RawMaterial::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            // Faqat raw_material jadvalidagi ustunlar bo‘yicha sort qiliadi
            if (Schema::hasColumn('raw_material', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $warehouses = Warehouse::whereHas('rawMaterial')->distinct()->pluck('title', 'id');

        $userIds = RawMaterial::distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->where('role_id', '!=', Role::where('title', 'Client')->value('id'))->pluck('username', 'id');

        $categoryIds = RawMaterial::distinct()->pluck('category_id');
        $categories = Category::whereIn('id', $categoryIds)->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $rawMaterialCount = $query->whereYear('created_at', now()->year)->count();
        } else {
            $rawMaterialCount = RawMaterial::whereYear('created_at', now()->year)->count();
        }

        $rawMaterials = $query->paginate(20)->withQueryString();

        return view('backend.raw-material.index', compact(
            'rawMaterials',
            'searchModel',
            'warehouses',
            'users',
            'categories',
            'isFiltered',
            'rawMaterialCount'
        ));
    }


    public function show(RawMaterial $rawMaterial)
    {
        return view('backend.raw-material.show', compact('rawMaterial'));
    }


    public function create()
    {
        $warehouses = Warehouse::whereNot('type', StatusService::TYPE_PRODUCT)->pluck('title', 'id');
        $rawMaterial = new RawMaterial();
        $categories = Category::whereNot('type', StatusService::TYPE_PRODUCT)->pluck('title', 'id');

        return view('backend.raw-material.create', compact('warehouses', 'rawMaterial', 'categories'));
    }

    public function store(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate(
            [
                'warehouse_id' => 'exists:warehouse,id',
                'title' => 'required|string|max:100',
                'subtitle' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'category_id' => 'required|exists:category,id',
                'type' => 'nullable|string|max:255',
                'status' => 'required|integer',
            ],
            [
                'title.required' => 'Хомашё турининг номи мажбурий.',
                'title.string' => 'Хомашё турининг номи матн бўлиши керак.',
                'title.max' => 'Хомашё турининг номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'category_id.required' => 'Категорияни белгилаш мажбурий.',
            ]
        );

        $rawMaterial->warehouse_id = $request->warehouse_id;
        $rawMaterial->title = $request->title;
        $rawMaterial->subtitle = $request->subtitle;
        $rawMaterial->description = $request->description;

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

            $rawMaterial->image = $file->id;
        }

        $rawMaterial->category_id = $request->category_id;
        $rawMaterial->type = $request->type;
        $rawMaterial->status = $request->status;

        $rawMaterial->save();

        return redirect()->route('raw-material.index')->with('success', 'Хомашё тури яратилди!');
    }


    public function edit(RawMaterial $rawMaterial)
    {
        $warehouses = Warehouse::whereNot('type', StatusService::TYPE_PRODUCT)->pluck('title', 'id');
        $categories = Category::whereNot('type', StatusService::TYPE_PRODUCT)->pluck('title', 'id');

        return view('backend.raw-material.update', compact('warehouses', 'rawMaterial', 'categories'));
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate(
            [
                'warehouse_id' => 'exists:warehouse,id',
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'category_id' => 'required|exists:category,id',
                'type' => 'nullable|string|max:255',
                'status' => 'required|integer',
            ],
            [
                'title.required' => 'Хомашё турининг номи мажбурий.',
                'title.string' => 'Хомашё турининг номи матн бўлиши керак.',
                'title.max' => 'Хомашё турининг номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'category_id.required' => 'Категорияни белгилаш мажбурий.',
            ]
        );

        $rawMaterial->warehouse_id = $request->warehouse_id;
        $rawMaterial->title = $request->title;
        $rawMaterial->subtitle = $request->subtitle;
        $rawMaterial->description = $request->description;

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

            $rawMaterial->image = $file->id;
        }

        $rawMaterial->category_id = $request->category_id;
        $rawMaterial->type = $request->type;
        $rawMaterial->status = $request->status;

        $rawMaterial->save();

        return redirect()->route('raw-material.index')->with('success', 'Хомашё тури янгиланди!');
    }


    public function destroy(RawMaterial $rawMaterial)
    {
        if ($rawMaterial->file) {
            Storage::disk('public')->delete($rawMaterial->file->path);
            $rawMaterial->file->delete();
        }
        $rawMaterial->delete();

        return response()->json([
            'message' => 'Хомашё тури ўчирилди!',
            'type' => 'delete',
            'redirect' => route('raw-material.index')
        ]);
    }
}
