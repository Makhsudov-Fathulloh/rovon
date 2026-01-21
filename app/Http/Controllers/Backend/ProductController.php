<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Search\ProductSearch;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ProductSearch(new DateFilterService());
        $query = $searchModel->applyFilters(Product::query(), $request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            // Faqat product jadvalidagi ustunlar bo‘yicha sort qiliadi
            if (Schema::hasColumn('product', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $warehouses = Warehouse::whereHas('product')->distinct()->pluck('title', 'id');

        $userIds = Product::distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->where('role_id', '!=', \App\Models\Role::where('title', 'Client')->value('id'))->pluck('username', 'id');

        $categoryIds = Product::distinct()->pluck('category_id');
        $categories = Category::whereIn('id', $categoryIds)->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $productCount = $query->whereYear('created_at', now()->year)->count();
        } else {
            $productCount = Product::whereYear('created_at', now()->year)->count();
        }

        $products = $query->paginate(20)->withQueryString();

        return view('backend.product.index', compact(
            'products',
            'searchModel',
            'warehouses',
            'users',
            'categories',
            'isFiltered',
            'productCount'
        ));
    }

//    public function index()
//    {
//        $searchModel = new ProductSearch();
//        $query = $searchModel->search(request());
//
//        $filteredCount = $query->count();
//        $isFiltered = count(request()->get('filters', [])) > 0;
//
//        $productCount = \App\Models\Product::count();
//
//        $dataProvider = new \Woo\GridView\DataProviders\EloquentDataProvider($query);
////        $dataProvider = new EloquentDataProvider(Product::query());
//
//        return view('backend.product.index', compact(
//            'searchModel',
//            'filteredCount',
//            'isFiltered',
//            'productCount',
//            'dataProvider'
//        ));
//    }


    public function show(Product $product)
    {
        return view('backend.product.show', compact('product'));
    }


    public function create()
    {
        $product = new Product();
        $warehouses = Warehouse::whereIn('type', [StatusService::TYPE_ALL, StatusService::TYPE_PRODUCT])->pluck('title', 'id');
        $categories = Category::whereIn('type', [StatusService::TYPE_ALL, StatusService::TYPE_PRODUCT])->pluck('title', 'id');

        return view('backend.product.create', compact('warehouses', 'product', 'categories'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
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
                'title.required' => 'Маҳсулот номи мажбурий.',
                'title.string' => 'Маҳсулот номи матн бўлиши керак.',
                'title.max' => 'Маҳсулот номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'category_id.required' => 'Категорияни белгилаш мажбурий.',
            ]
        );

        $product->warehouse_id = $request->warehouse_id;
        $product->title = $request->title;
        $product->subtitle = $request->subtitle;
        $product->description = $request->description;

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

            $product->image = $file->id;
        }

//        $product->user_id = auth()->id();
        $product->category_id = $request->category_id;
        $product->type = $request->type;
        $product->status = $request->status;

        $product->save();

        return redirect()->route('product.index')->with('success', 'Махсулот тури яратилди!');
    }


    public function edit(Product $product)
    {
        $warehouses = Warehouse::whereIn('type', [StatusService::TYPE_ALL, StatusService::TYPE_PRODUCT])->pluck('title', 'id');
        $categories = Category::whereIn('type', [StatusService::TYPE_ALL, StatusService::TYPE_PRODUCT])->pluck('title', 'id');

        return view('backend.product.update', compact('warehouses', 'product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
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
                'title.required' => 'Маҳсулот номи мажбурий.',
                'title.string' => 'Маҳсулот номи матн бўлиши керак.',
                'title.max' => 'Маҳсулот номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
                'category_id.required' => 'Категорияни белгилаш мажбурий.',
            ]
        );

        $product->warehouse_id = $request->warehouse_id;
        $product->title = $request->title;
        $product->subtitle = $request->subtitle;
        $product->description = $request->description;

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

            $product->image = $file->id;
        }

        $product->category_id = $request->category_id;
        $product->type = $request->type;
        $product->status = $request->status;

        $product->save();

        return redirect()->route('product.index')->with('success', 'Махсулот тури янгиланди!');
    }


    public function destroy(Product $product)
    {
        if ($product->file) {
            Storage::disk('public')->delete($product->file->path);
            $product->file->delete();
        }
        $product->delete();

        return response()->json([
            'message' => 'Маҳсулот тури ўчирилди',
            'type' => 'delete',
            'redirect' => route('product.index')
        ]);
    }
}
