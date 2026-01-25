<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Section;
use App\Models\Role;
use App\Models\Search\ShiftSearch;
use App\Models\Shift;
use App\Models\User;
use App\Services\DateFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ShiftSearch(new DateFilterService());
        $query = $searchModel->search($request);
        $query->join('section', 'shift.section_id', '=', 'section.id')
            ->join('organization', 'section.organization_id', '=', 'organization.id')
            ->select('shift.*', 'organization.title as organization_title');

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('shift', $sort)) {
                $query->orderBy("shift.$sort", $direction);
            } elseif ($sort === 'organization_title') {
                $query->orderBy('organization.title', $direction);
            }
        }

        $organizations = Organization::selectRaw('MIN(id) as id, title')->groupBy('title')->orderBy('title')->pluck('title', 'id');
        $sections = Section::selectRaw('MIN(id) as id, title')->groupBy('title')->orderBy('title')->pluck('title', 'id');
        $titles = Shift::selectRaw('MIN(id) as id, title')->groupBy('title')->orderBy('title')->pluck('title', 'id');

        $users = User::whereHas('shiftUser') // User modelida shift_user bilan Shift ga ulangan
            ->orderBy('username')
            ->pluck('username', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        if ($isFiltered) {
            $shiftCount = $query->count();
        } else {
            $shiftCount = Shift::count();
        }

        $shifts = $query->paginate(20)->withQueryString();

        return view('backend.shift.index', compact(
            'shifts',
            'organizations',
            'sections',
            'titles',
            'users',
            'isFiltered',
            'shiftCount'
        ));
    }


    public function list(Request $request, $section_id)
    {
        $section = Section::findOrFail($section_id);

        $searchModel = new ShiftSearch(new DateFilterService());
        $query = Shift::query()->where('section_id', $section_id)->with('section');
        $query = $searchModel->search($request, $query);

        // Sort
        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = Str::startsWith($sort, '-') ? 'desc' : 'asc';
            $sort = ltrim($sort, '-');

            if (Schema::hasColumn('section', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $titles = Shift::where('section_id', $section_id)->selectRaw('MIN(id) as id, title')->groupBy('title')->orderBy('title')->pluck('title', 'id');
        $users = User::whereHas('shifts', function ($q) use ($section_id) {
            $q->where('shift.section_id', $section_id);
        })->orderBy('username')->pluck('username', 'id');

        $isFiltered = $request->has('filters') && count($request->input('filters', [])) > 0;

        if ($isFiltered) {
            $shiftCount = $query->count();
        } else {
            $shiftCount = Shift::where('section_id', $section_id)->count();
        }

        $shifts = $query->paginate(20)->withQueryString();

        return view('backend.shift.list', compact(
            'section',
            'shifts',
            'titles',
            'users',
            'isFiltered',
            'shiftCount'
        ));
    }


    public function show(Shift $shift)
    {
        return view('backend.shift.show', compact('shift'));
    }


    public function create()
    {
        $organizations = Organization::pluck('title', 'id');
        $sections = Section::all();
        $shift = new Shift();
        
        // // Barcha "Worker" rolidagi foydalanuvchilar
        // $allUsers = User::where('role_id', Role::where('title', 'Worker')->value('id'))->get();
        // // Create uchun: allUsers ichidan faqat hali bir smenaga biriktirilmaganlar
        // $assignedUserIds = DB::table('shift_user')->pluck('user_id')->toArray();
        // $users = $allUsers->whereNotIn('id', $assignedUserIds);

        $users = User::where('role_id', Role::where('title', 'Worker')->value('id'))->get();

        return view('backend.shift.create', compact('organizations', 'sections', 'users', 'shift'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'section_id' => 'required|exists:section,id',
            'user_id' => 'required|array',
            'user_id.*' => 'required|exists:user,id',
            'title' => 'required|string|max:255',
            'started_at' => 'required|date_format:H:i',
            'ended_at'   => 'required|date_format:H:i|after:started_at',
        ], [
            'organization_id.required' => 'Филиални танлаш мажбурий.',
            'section_id.required' => 'Бўлимни танлаш мажбурий.',
            'user_id.required' => 'Сменага жавобгар ходим мажбурий.',
            'title.required' => 'Смена номи мажбурий.',
            'title.string' => 'Смена номи матн бўлиши керак.',
            'started_at.required' => 'Смена бошланиш вакти мажбурий.',
        ]);

        $shift = Shift::create($request->only([
            'organization_id',
            'section_id',
            'title',
            'status',
            'started_at',
            'ended_at',
        ]));

        // ❗ Pivot jadvalga userlarni ulash
        $shift->users()->sync($request->user_id);

        return redirect()->route('shift.index')->with('success', 'Смена яратилди!');
    }


    public function edit(Shift $shift)
    {
        $organizations = Organization::pluck('title', 'id');
        $sections = Section::all();
        $shift->load('users');

        // // Barcha "Ходим" rolidagi foydalanuvchilar
        // $allUsers = User::where('role_id', Role::where('title', 'Worker')->value('id'))->get();
        // // Edit uchun: boshqa smenalarga biriktirilganlarni filterlash, hozirgi shift xodimlari bilan birga
        // $assignedUserIds = DB::table('shift_user')->where('shift_id', '!=', $shift->id)->pluck('user_id')->toArray();
        // $users = $allUsers->whereNotIn('id', $assignedUserIds)->merge($shift->user)->unique('id');

        $users = User::where('role_id', Role::where('title', 'Worker')->value('id'))->get();

        return view('backend.shift.update', compact('shift', 'organizations', 'sections', 'users'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'organization_id' => 'required|exists:organization,id',
            'section_id' => 'required|exists:section,id',
            'user_id' => 'required|array',
            'user_id.*' => 'exists:user,id',
            'title' => 'required|string|max:255',
            'started_at' => 'required|date_format:H:i',
            'ended_at'   => 'nullable|date_format:H:i|after:started_at',
        ], [
            'organization_id.required' => 'Филиални танлаш мажбурий.',
            'section_id.required' => 'Бўлимни танлаш мажбурий.',
            'user_id.required' => 'Сменага жавобгар ходим мажбурий.',
            'title.required' => 'Смена номи мажбурий.',
            'title.string' => 'Смена номи матн бўлиши керак.',
            'started_at.required' => 'Смена бошланиш вакти мажбурий.',
        ]);

        $shift->update($request->only([
            'organization_id',
            'section_id',
            'title',
            'status',
            'started_at',
            'ended_at',
        ]));

        // ❗ Pivot yangilash
        $shift->users()->sync($request->user_id);

        return redirect()->route('shift.index')->with('success', 'Смена янгиланди!');
    }


    public function destroy(Shift $shift)
    {
        $shift->delete();

        return response()->json([
            'message' => 'Смена ўчирилди!',
            'type' => 'delete',
            'redirect' => route('shift.index')
        ]);
    }
}
