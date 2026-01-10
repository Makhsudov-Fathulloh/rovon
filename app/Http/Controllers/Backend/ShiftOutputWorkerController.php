<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Search\ShiftOutputWorkerSearch;
use App\Models\Shift;
use App\Models\ShiftOutputWorker;
use App\Models\ShiftOutput;
use App\Models\Stage;
use App\Models\User;
use App\Services\PeriodService;
use App\Services\DateFilterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ShiftOutputWorkerController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ShiftOutputWorkerSearch(new DateFilterService());
        $query = $searchModel->search($request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('shift_output_worker', $sort)) {
                $query->orderBy($sort, $direction);
            } elseif ($sort === 'created_at') {
                $query->orderBy('created_at', $direction);
            }
        }

        $shifts = Shift::whereHas('shiftOutputs.shiftOutputWorkers')->orderBy('title')->pluck('title', 'id');
        $stages = Stage::whereHas('shiftOutputs.shiftOutputWorkers')->orderBy('title')->pluck('title', 'id');
        $users = User::whereHas('shiftOutputWorker')->orderBy('username')->pluck('username', 'id');

        $filters = $request->get('filters', []);
        $from = $filters['created_from'] ?? null;
        $to   = $filters['created_to'] ?? null;

        $periods = PeriodService::getPeriods($from, $to);

        $dailyFrom   = $periods['daily']['from'];
        $dailyTo     = $periods['daily']['to'];
        $monthlyFrom = $periods['monthly']['from'];
        $monthlyTo   = $periods['monthly']['to'];
        $yearlyFrom  = $periods['yearly']['from'];
        $yearlyTo    = $periods['yearly']['to'];

        $userIds = (clone $query)->pluck('user_id')->unique();

        $userStatistics = User::where('role_id', Role::where('title', 'Worker')->value('id'))
            ->whereHas('shiftOutputWorker')
            ->when($userIds->isNotEmpty(), fn($q) => $q->whereIn('id', $userIds))
            ->with(['shiftOutputWorker' => fn($q) => $q->select('id', 'user_id', 'shift_output_id', 'stage_count', 'defect_amount', 'price', 'created_at')])
            ->get()
            ->map(fn($user) => [
                'username'        => $user->username,
                'daily_product'   => $user->shiftOutputWorker->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('stage_count'),
                'daily_defect'    => $user->shiftOutputWorker->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('defect_amount'),
                'daily_price'     => $user->shiftOutputWorker->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('price'),
                'monthly_product' => $user->shiftOutputWorker->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('stage_count'),
                'monthly_defect'  => $user->shiftOutputWorker->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('defect_amount'),
                'monthly_price'   => $user->shiftOutputWorker->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('price'),
                'yearly_product'  => $user->shiftOutputWorker->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('stage_count'),
                'yearly_defect'   => $user->shiftOutputWorker->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('defect_amount'),
                'yearly_price'    => $user->shiftOutputWorker->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('price'),
            ]);

        $shiftOutputWorkers = $query->paginate(20)->appends($request->query());

        return view('backend.shift-output-worker.index', compact(
            'shiftOutputWorkers',
            'shifts',
            'stages',
            'users',
            'userStatistics',
        ));
    }


    public function list(Request $request, $shiftOutputId)
    {
        $shiftOutput = ShiftOutput::findOrFail($shiftOutputId);

        $searchModel = new ShiftOutputWorkerSearch(new DateFilterService());
        $query = $searchModel->search($request);

        // ShiftOutputWorker dagi filterlar faqat shu shift_output_id ga bog‘lanadi
        $query->where('shift_output_id', $shiftOutputId);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('shift_output_worker', $sort)) {
                $query->orderBy($sort, $direction);
            } elseif ($sort === 'created_at') {
                $query->orderBy('created_at', $direction);
            }
        }

        // Select2 uchun listlar
        $shifts = Shift::whereHas('shiftOutputs', function ($q) use ($shiftOutputId) {
            $q->where('id', $shiftOutputId);
        })->orderBy('title')->pluck('title', 'id');
        $stages = Stage::whereHas('shiftOutputs.shiftOutputWorkers', function ($q) use ($shiftOutputId) {
            $q->where('shift_output_id', $shiftOutputId);
        })->orderBy('title')->pluck('title', 'id');
        $users = User::whereHas('shiftOutputWorker', function ($q) use ($shiftOutputId) {
            $q->where('shift_output_id', $shiftOutputId);
        })->where('role_id', \App\Models\Role::where('title', 'Worker')->value('id'))->orderBy('username')->pluck('username', 'id');

        $filters = $request->get('filters', []);
        $from = $filters['created_from'] ?? null;
        $to   = $filters['created_to'] ?? null;

        $periods = PeriodService::getPeriods($from, $to);

        $dailyFrom   = $periods['daily']['from'];
        $dailyTo     = $periods['daily']['to'];
        $monthlyFrom = $periods['monthly']['from'];
        $monthlyTo   = $periods['monthly']['to'];
        $yearlyFrom  = $periods['yearly']['from'];
        $yearlyTo    = $periods['yearly']['to'];

        $userIds = (clone $query)->pluck('user_id')->unique();

        $userStatistics = User::whereHas('shiftOutputWorker', fn($q) => $q->where('shift_output_id', $shiftOutputId))
            ->where('role_id', Role::where('title', 'Worker')->value('id'))
            ->when($userIds->isNotEmpty(), fn($q) => $q->whereIn('id', $userIds))
            ->with(['shiftOutputWorker' => fn($q) => $q->where('shift_output_id', $shiftOutputId)
                ->select('id', 'user_id', 'stage_count', 'defect_amount', 'price', 'created_at')])
            ->get()
            ->map(fn($user) => [
                'username'        => $user->username,
                'daily_product'   => $user->shiftOutputWorker->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('stage_count'),
                'daily_defect'    => $user->shiftOutputWorker->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('defect_amount'),
                'daily_price'     => $user->shiftOutputWorker->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('price'),
                'monthly_product' => $user->shiftOutputWorker->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('stage_count'),
                'monthly_defect'  => $user->shiftOutputWorker->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('defect_amount'),
                'monthly_price'   => $user->shiftOutputWorker->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('price'),
                'yearly_product'  => $user->shiftOutputWorker->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('stage_count'),
                'yearly_defect'   => $user->shiftOutputWorker->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('defect_amount'),
                'yearly_price'    => $user->shiftOutputWorker->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('price'),
            ]);

        $workers = $query->paginate(20)->appends($request->query());

        return view('backend.shift-output-worker.list', compact(
            'shiftOutput',
            'workers',
            'shifts',
            'stages',
            'users',
            'userStatistics'
        ));
    }


    public function show(ShiftOutputWorker $shiftOutputWorker)
    {
        return view('backend.shift-output-worker.show', compact('shiftOutputWorker'));
    }
}
