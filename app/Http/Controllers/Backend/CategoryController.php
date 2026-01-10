<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Search\CategorySearch;
use App\Models\User;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new CategorySearch(new DateFilterService());
        $query = $searchModel->search($request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('category', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $categoryParents = Category::whereNull('parent_id')->pluck('title', 'id');

        $users = User::whereIn('id', Category::distinct()->pluck('user_id'))->pluck('username', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $categoryCount = $query->count();
        } else {
            $categoryCount = Category::count();
        }

        $categories = $query->paginate(20)->withQueryString();

        return view('backend.category.index', compact(
            'categories',
            'categoryParents',
            'users',
            'isFiltered',
            'categoryCount'
        ));
    }


    public function show(Category $category)
    {
        return view('backend.category.show', compact('category'));
    }


    public function create()
    {
        $category = new Category();
        $categoryDropdown = Category::getDropdownList(null, Category::TYPE_PRODUCT);;

        return view('backend.category.create', compact('category', 'categoryDropdown'));
    }


    public function store(Request $request, Category $category)
    {
        $request->validate(
            [
                'parent_id' => 'nullable|exists:category,id',
                'title' => 'required|string|max:100',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'type' => 'nullable|string|max:50',
            ],
            [
                'title.required' => 'Категория номи мажбурий.',
                'title.string' => 'Категория номи матн бўлиши керак.',
                'title.max' => 'Категория номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
            ]
        );

        $category->parent_id = $request->parent_id;
        $category->title = $request->title;
        $category->subtitle = $request->subtitle;
        $category->description = $request->description;

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

            $category->image = $file->id;
        }

        $category->type = $request->type;

        $category->save();

        return redirect()->route('category.index')->with('success', 'Қатегория яратилди!');
    }


    public function edit(Category $category)
    {
        $categoryDropdown = Category::getDropdownList(null, Category::TYPE_PRODUCT);
        return view('backend.category.update', compact('category', 'categoryDropdown'));
    }


    public function update(Request $request, Category $category)
    {
        $request->validate(
            [
                'parent_id' => 'nullable|exists:category,id',
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'type' => 'nullable|string|max:50',
            ],
            [
                'title.required' => 'Категория номи мажбурий.',
                'title.string' => 'Категория номи матн бўлиши керак.',
                'title.max' => 'Категория номи 100 белгидан ошмаслиги керак.',
                'image.mimes' => 'Расм JPG, JPEG, PNG ёки WEBP форматида бўлиши шарт.',
                'image.max' => 'Расм ҳажми 5 МБ дан ошмаслиги керак.',
            ]
        );

        $category->parent_id = $request->parent_id;
        $category->title = $request->title;
        $category->subtitle = $request->subtitle;
        $category->description = $request->description;

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

            $category->image = $file->id;
        }

        //        $category->user_id = auth()->id();
        $category->type = $request->type;

        $category->save();

        return redirect()->route('category.index')->with('success', 'Қатегория янгиланди!');
    }


    public function destroy(Category $category)
    {
        if ($category->file) {
            Storage::disk('public')->delete($category->file->path);
            $category->file->delete();
        }
        $category->delete();

        return response()->json([
            'message' => 'Категория ўчирилди!',
            'type' => 'delete',
            'redirect' => route('category.index')
        ]);
    }
}
