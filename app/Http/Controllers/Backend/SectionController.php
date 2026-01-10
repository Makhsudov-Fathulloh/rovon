<?php

namespace App\Http\Controllers\Backend;

use App\Models\Section;
use App\Http\Controllers\Controller;
use App\Models\Search\SectionSearch;
use App\Models\Organization;
use App\Models\SectionStageBalance;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new SectionSearch(new DateFilterService());
        $query = $searchModel->search($request);

        // Sort
        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = Str::startsWith($sort, '-') ? 'desc' : 'asc';
            $sort = ltrim($sort, '-');

            if (Schema::hasColumn('section', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        // Pagination
        $sections = $query->paginate(20)->withQueryString();
        $filteredSectionIds = $sections->pluck('id')->toArray();

        /**
         * 🔑 KANBAN UCHUN ASOSIY QUERY
         */
        $orgSection = Organization::whereHas('section', function ($q) use ($filteredSectionIds) {
            if (!empty($filteredSectionIds)) {
                $q->whereIn('id', $filteredSectionIds);
            }
        })
            ->with([
                'section' => function ($q) use ($filteredSectionIds) {
                    if (!empty($filteredSectionIds)) {
                        $q->whereIn('id', $filteredSectionIds);
                    }
                    $q->orderBy('id');

                    $q->with([
                        'stages' => function ($q) {
                            $q->orderBy('id');
                            $q->with([
                                'balances' => function ($q) {
                                    // MUHIM: balance faqat o‘sha section uchun
                                    $q->select('*');
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->orderBy('id')
            ->get();

        $organizations = Organization::orderBy('id')->pluck('title', 'id');
        $titles = Section::selectRaw('MIN(id) as id, title')
            ->groupBy('title')
            ->orderBy('id')
            ->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;
        $sectionCount = $isFiltered ? $query->count() : Section::count();

        return view('backend.section.index', compact(
            'sections',
            'organizations',
            'orgSection',
            'titles',
            'isFiltered',
            'sectionCount',
        ));
    }

    public function getSections($organizationId)
    {
        return Section::where('organization_id', $organizationId)->orderBy('id')->get(['id', 'title']);
    }


    public function show(Section $section)
    {
        return view('backend.section.show', compact('section'));
    }


    public function create()
    {
        $organizations = Organization::pluck('title', 'id');
        $sections = Section::pluck('title', 'id');
        $section = new Section();

        return view('backend.section.create', compact('organizations', 'sections', 'section'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'previous_id' => 'nullable|exists:section,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'user_id.required' => 'Бўлим ходими мажбурий.',
            'title.required' => 'Бўлим номи мажбурий.',
            'title.string'   => 'Бўлим номи матн бўлиши керак.',
        ]);

        Section::create($request->all());

        return redirect()->route('section.index')->with('success', 'Бўлим яратилди!');
    }


    public function edit(Section $section)
    {
        $organizations = Organization::pluck('title', 'id');
        $sections = Section::where('id', '!=', $section->id)->pluck('title', 'id');

        return view('backend.section.update', compact('organizations', 'sections', 'section'));
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'previous_id' => 'nullable|exists:section,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'user_id.required' => 'Бўлим ходими мажбурий.',
            'title.required' => 'Бўлим номи мажбурий.',
            'title.string'   => 'Бўлим номи матн бўлиши керак.',
        ]);

        $section->update($request->all());

        return redirect()->route('section.index')->with('success', 'Бўлим янгиланди!');
    }


    public function destroy(Section $section)
    {
        $section->delete();

        return response()->json([
            'message' => 'Бўлим ўчирилди!',
            'type' => 'delete',
            'redirect' => route('section.index')
        ]);
    }
}
