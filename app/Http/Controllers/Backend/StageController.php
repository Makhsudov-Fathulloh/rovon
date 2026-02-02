<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Organization;
use App\Models\RawMaterialVariation;
use App\Models\Section;
use App\Models\Search\StageSearch;
use App\Models\Stage;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StageController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new StageSearch(new DateFilterService());
        $query = $searchModel->search($request);
        $query->join('section', 'stage.section_id', '=', 'section.id')
            ->join('organization', 'section.organization_id', '=', 'organization.id')
            ->select('stage.*', 'organization.title as organization_title');

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('stage', $sort)) {
                $query->orderBy($sort, $direction);
            } elseif ($sort === 'organization_title') {
                $query->orderBy('organization.title', $direction);
            } elseif ($sort === 'created_at') {
                $query->orderBy('created_at', $direction);
            }
        }

        $organizations = Organization::whereHas('section.organization')->orderBy('title')->pluck('title', 'id');
        $sections = Section::whereHas('stages')->orderBy('title')->pluck('title', 'id');
        $titles = Stage::selectRaw('MIN(id) as id, title')->groupBy('title')->orderBy('title')->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $stageCount = $query->count();
        } else {
            $stageCount = Stage::count();
        }

        $stages = $query->with(['stageMaterials.rawMaterialVariation'])->paginate(20)->withQueryString();

        return view('backend.stage.index', compact(
            'stages',
            'organizations',
            'sections',
            'titles',
            'isFiltered',
            'stageCount',
        ));
    }


    public function show(Stage $stage)
    {
        return view('backend.stage.show', compact('stage'));
    }


    public function getPreStages($sectionId)
    {
        //     $section = Section::find($sectionId);

        //     if (!$section || !$section->previous_id) {
        //         return collect(); // Oldingi section mavjud bo'lmasa, bo'sh kolleksiya qaytaradi
        //     }

        //     return Stage::where('section_id', $section->previous_id)->orderBy('id')->get(['id', 'title']);

        $stages = Stage::with('preStages')->get();

        // JSON formatda preStages id larini jo‘natish
        $data = $stages->map(function ($s) {
            return [
                'id' => $s->id,
                'title' => $s->title . " (" . ($s->section->title ?? 'Номаълум') . ")",
                'pre_stage_ids' => $s->preStages->pluck('id')->toArray(),
            ];
        });

        return response()->json($data);
    }


    public function create()
    {
        $organizations = Organization::pluck('title', 'id');
        $rawMaterialVariations = RawMaterialVariation::whereHas('rawMaterial.category', function ($q) {
            $q->whereIn('type', [
                StatusService::TYPE_RAW_MATERIAL,
                StatusService::TYPE_ALL
            ]);
        })->get();

        $stage = new Stage();

        return view('backend.stage.create', compact('organizations', 'rawMaterialVariations', 'stage'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'price' => str_replace(' ', '', $request->price),
        ]);

        $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'section_id' => 'required|exists:section,id',
            'pre_stage_ids' => 'nullable|array',
            'pre_stage_ids.*' => 'exists:stage,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'defect_type' => 'required|in:1,2',
        ], [
            'organization_id.required' => 'Филиални танлаш мажбурий.',
            'section_id.required' => 'Бўлимни танлаш мажбурий.',
            'title.required' => 'Бўлим махсулоти номи мажбурий.',
            'title.string' => 'Бўлим махсулоти номи матн бўлиши керак.',
            'price.required' => 'Бўлим махсулоти нархи мажбурий.',
        ]);

        DB::transaction(function () use ($request) {
            $stage = Stage::create($request->only(['section_id', 'title', 'description', 'price', 'defect_type', 'status']));

            foreach ($request->stage_materials ?? [] as $materialData) {
                $stage->stageMaterials()->create($materialData);
            }

            $stage->preStages()->sync($request->pre_stage_ids ?? []);
        });

        return redirect()->route('stage.index')->with('success', 'Бўлим махсулоти яратилди!');
    }


    public function edit(Stage $stage)
    {
        $organizations = Organization::pluck('title', 'id');
        $sections = Section::all();
        $rawMaterialVariations = RawMaterialVariation::whereHas('rawMaterial.category', function ($q) {
            $q->whereIn('type', [
                StatusService::TYPE_RAW_MATERIAL,
                StatusService::TYPE_ALL
            ]);
        })->get();

        return view('backend.stage.update', compact('stage', 'organizations', 'sections', 'rawMaterialVariations'));
    }

    public function update(Request $request, Stage $stage)
    {
        $request->merge([
            'price' => str_replace(' ', '', $request->price),
        ]);

        $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'section_id' => 'required|exists:section,id',
            'pre_stage_ids' => 'nullable|array',
            'pre_stage_ids.*' => 'exists:stage,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'defect_type' => 'required|in:1,2',
        ], [
            'organization_id.required' => 'Филиални танлаш мажбурий.',
            'section_id.required' => 'Бўлимни танлаш мажбурий.',
            'title.required' => 'Бўлим махсулоти номи мажбурий.',
            'title.string' => 'Бўлим махсулоти номи матн бўлиши керак.',
            'price.required' => 'Бўлим махсулотлари нархи мажбурий.',
        ]);


        DB::transaction(function () use ($request, $stage) {
            $stage->update($request->only(['section_id', 'title', 'description', 'price', 'defect_type', 'status']));

            $stage->stageMaterials()->delete();

            foreach ($request->stage_materials ?? [] as $materialData) {
                $stage->stageMaterials()->create($materialData);
            }

            $stage->preStages()->sync($request->pre_stage_ids ?? []);
        });

        return redirect()->route('stage.index')->with('success', 'Бўлим махсулоти янгиланди!');
    }


    public function destroy(Stage $stage)
    {
        $stage->delete();

        return response()->json([
            'message' => 'Бўлим махсулоти ўчирилди!',
            'type' => 'delete',
            'redirect' => route('stage.index')
        ]);
    }
}
