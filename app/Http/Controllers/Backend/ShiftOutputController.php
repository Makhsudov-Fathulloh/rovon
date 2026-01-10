<?php

namespace App\Http\Controllers\Backend;

use App\Models\Shift;
use App\Models\Stage;
use App\Models\Section;
use App\Models\ShiftOutput;
use Illuminate\Support\Str;
use App\Models\DefectReport;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Helpers\TelegramHelper;
use App\Services\PeriodService;
use App\Services\StatusService;
use App\Models\ShiftOutputWorker;
use Illuminate\Support\Facades\DB;
use App\Services\DateFilterService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Models\Search\ShiftOutputSearch;

class ShiftOutputController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new ShiftOutputSearch(new DateFilterService());
        $query = $searchModel->search($request);

        // Base query
        $query->join('shift', 'shift_output.shift_id', '=', 'shift.id')
            ->join('section', 'shift.section_id', '=', 'section.id')
            ->join('organization', 'section.organization_id', '=', 'organization.id')
            ->select(
                'shift_output.*',
                'section.title as section_title',
                'organization.title as organization_title'
            )
            ->with(['shift', 'stage', 'stage.shiftOutputs' => function ($q) {
                $q->select('id', 'stage_id', 'stage_count', 'defect_amount', 'created_at');
            }]);

        // Sorting
        if ($sort = $request->get('sort')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $sort = ltrim($sort, '-');

            if (Schema::hasColumn('shift_output', $sort)) {
                $query->orderBy("shift_output.$sort", $direction);
            } elseif ($sort === 'section_title') {
                $query->orderBy('section.title', $direction);
            } elseif ($sort === 'organization_title') {
                $query->orderBy('organization.title', $direction);
            }
        } else {
            $query->orderByDesc('shift_output.created_at');
        }

        $organizations = Organization::orderBy('title')->pluck('title', 'id');
        $sections      = Section::orderBy('title')->pluck('title', 'id');
        $shifts        = Shift::whereHas('shiftOutputs')->pluck('title', 'id');
        $stages    = Stage::whereHas('shiftOutputs')->pluck('title', 'id');

        // Date filter
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

        // Stage filtered
        $stageIds = (clone $query)->pluck('stage_id')->unique();
        $productStatistics = Stage::whereIn('id', $stageIds)
            ->with(['shiftOutputs' => function ($q) {
                $q->select('id', 'stage_id', 'stage_count', 'defect_amount', 'created_at');
            }])
            ->get()
            ->map(function ($stage) use ($dailyFrom, $dailyTo, $monthlyFrom, $monthlyTo, $yearlyFrom, $yearlyTo) {
                $outputs = $stage->shiftOutputs;

                return [
                    'title' => $stage->title,
                    'daily_product'   => $outputs->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('stage_count'),
                    'daily_defect'    => $outputs->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('defect_amount'),
                    'monthly_product' => $outputs->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('stage_count'),
                    'monthly_defect'  => $outputs->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('defect_amount'),
                    'yearly_product'  => $outputs->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('stage_count'),
                    'yearly_defect'   => $outputs->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('defect_amount'),
                ];
            });

        $shiftOutputs = $query->paginate(20)->appends($request->query());

        return view('backend.shift-output.index', compact(
            'shiftOutputs',
            'shifts',
            'stages',
            'organizations',
            'sections',
            'productStatistics'
        ));
    }


    public function list(Request $request, $shiftId)
    {
        $shift = Shift::findOrFail($shiftId);

        $searchModel = new ShiftOutputSearch(new DateFilterService());
        $query = $searchModel->search($request);

        // faqat shu shiftId ga tegishli shiftOutputs
        $query->where('shift_id', $shiftId);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }

            if (Schema::hasColumn('shift_output', $sort)) {
                $query->orderBy($sort, $direction);
            } elseif ($sort === 'created_at') {
                $query->orderBy('shift_output.created_at', $direction);
            }
        }

        $stages = Stage::whereHas('shiftOutputs', function ($q) use ($shiftId) {
            $q->where('shift_id', $shiftId);
        })
            ->orderBy('title')
            ->pluck('title', 'id');

        // Date filter
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

        // Stage filtered
        $stageIds = (clone $query)->pluck('stage_id')->unique();
        $productStatistics = Stage::whereHas('shiftOutputs', function ($q) use ($shiftId) {
            $q->where('shift_id', $shiftId);
        })
            ->when($stageIds->isNotEmpty(), function ($q) use ($stageIds) {
                $q->whereIn('id', $stageIds);
            })
            ->with(['shiftOutputs' => function ($q) use ($shiftId) {
                $q->where('shift_id', $shiftId)
                    ->select('id', 'stage_id', 'stage_count', 'defect_amount', 'created_at');
            }])
            ->get()
            ->map(function ($stage) use ($dailyFrom, $dailyTo, $monthlyFrom, $monthlyTo, $yearlyFrom, $yearlyTo) {

                $outputs = $stage->shiftOutputs;

                return [
                    'title' => $stage->title,
                    'daily_product'   => $outputs->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('stage_count'),
                    'daily_defect'    => $outputs->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('defect_amount'),
                    'monthly_product' => $outputs->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('stage_count'),
                    'monthly_defect'  => $outputs->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('defect_amount'),
                    'yearly_product'  => $outputs->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('stage_count'),
                    'yearly_defect'   => $outputs->whereBetween('created_at', [$yearlyFrom, $yearlyTo])->sum('defect_amount'),
                ];
            });

        $outputs = $query->paginate(20)->appends($request->query());

        return view('backend.shift-output.list', compact(
            'shift',
            'outputs',
            'stages',
            'productStatistics'
        ));
    }


    public function show(ShiftOutput $shiftOutput)
    {
        return view('backend.shift-output.show', compact('shiftOutput'));
    }


    public function create(Shift $shift)
    {
        $sections = Section::where('status', StatusService::STATUS_ACTIVE)->orderBy('id', 'asc')->get();
        $shifts = Shift::where('section_id', $shift->section_id)->orderBy('id', 'asc')->get();
        $stages = Stage::where('section_id', $shift->section_id)->where('status', StatusService::STATUS_ACTIVE)->get();

        $workers = $shift->user()->get();

        return view('backend.shift-output.create', compact('shift', 'stages', 'workers', 'shifts', 'sections'));
    }

    public function store(Request $request, Shift $shift)
    {
        $inputs = $request->input('input', []);
        $workerDefects = $request->input('worker_defects', []);

        DB::transaction(function () use ($inputs, $workerDefects, $shift) {
            $stages = Stage::where('section_id', $shift->section_id)->get();
            $workers = $shift->user()->get();

            foreach ($stages as $stage) {
                $stageTotalQty = 0;
                $workerDataForStage = [];

                // 1. Ushbu bosqich (stage) uchun jami miqdorni aniqlaymiz
                foreach ($workers as $worker) {
                    $qty = (int)preg_replace('/[^\d]/', '', $inputs[$worker->id][$stage->id]['stage_count'] ?? 0);
                    if ($qty > 0) {
                        $stageTotalQty += $qty;
                        $workerDataForStage[$worker->id] = $qty;
                    }
                }

                if ($stageTotalQty > 0) {
                    // 2. ShiftOutput yaratish
                    $shiftOutput = $shift->shiftOutputs()->create([
                        'stage_id' => $stage->id,
                        'stage_count' => $stageTotalQty,
                        'defect_amount' => 0, // Quyida hisoblab yangilaymiz
                    ]);

                    $totalStageDefect = 0;

                    foreach ($workerDataForStage as $workerId => $qty) {
                        $worker = $workers->find($workerId);

                        // Ishchining barcha bosqichlardagi jami miqdori (brakni taqsimlash uchun)
                        $totalWorkerQtyAcrossAllStages = 0;
                        foreach ($stages as $s) {
                            $totalWorkerQtyAcrossAllStages += (int)preg_replace('/[^\d]/', '', $inputs[$workerId][$s->id]['stage_count'] ?? 0);
                        }

                        // Umumiy kiritilgan brakni ushbu stage'ga taqsimlaymiz
                        $totalWorkerDefectInput = (float)($workerDefects[$workerId] ?? 0);
                        $workerStageDefect = $totalWorkerQtyAcrossAllStages > 0
                            ? ($qty / $totalWorkerQtyAcrossAllStages) * $totalWorkerDefectInput
                            : 0;

                        $totalStageDefect += $workerStageDefect;

                        // 3. Worker Output record yaratish
                        ShiftOutputWorker::create([
                            'shift_output_id' => $shiftOutput->id,
                            'stage_id'      => $stage->id,
                            'user_id'       => $workerId,
                            'stage_count'   => $qty,
                            'defect_amount' => round($workerStageDefect, 3),
                            'price'         => $stage->price * $qty,
                        ]);

                        // ❗ BRAK CHECK (Sikl ichida, har bir ishchi uchun)
                        $limitAmount = $qty * 0.02; // 2% limit

                        if ($workerStageDefect > $limitAmount) {
                            $excess = $workerStageDefect - $limitAmount;
                            $excessPercent = ($workerStageDefect / $qty) * 100;

                            DefectReport::create([
                                'organization_id'     => $shift->section->organization_id,
                                'section_id'          => $shift->section_id,
                                'shift_id'            => $shift->id,
                                'user_id'             => $workerId,
                                'stage_id'            => $stage->id,
                                'stage_count'         => $qty,
                                'total_defect_amount' => round($workerStageDefect, 3),
                                'defect_amount'       => round($excess, 3),
                                'defect_percent'      => round($excessPercent, 2),
                            ]);

                            $workerName = $worker->username ?? "ID: $workerId";
                            $msg = "<b>❗ Брак лимитидан ошди! </b>\n\n"
                                . "👤 Ишчи: <b>{$workerName}</b>\n"
                                . "🏭 Филиал: <b>{$shift->organization?->title}</b>\n"
                                . "📍 Бўлим: <b>{$shift->section?->title}</b>\n"
                                . "⚙️ Махсулот: <b>{$stage->title}</b>\n"
                                . "🔢 Чиқарилган: <b>{$qty} dona</b>\n"
                                . "🔻 Ишчи браки: <b>" . round($workerStageDefect, 3) . " кг</b>\n"
                                . "⚠️ Лимит (2%): <b>" . round($limitAmount, 3) . " кг</b>\n"
                                . "🚫 Ортиқча: <b>" . round($excess, 3) . " кг</b>\n"
                                . "📊 Фоиз: <b>" . round($excessPercent, 2) . "%</b>";

                            TelegramHelper::notifyDefect($msg);
                        }
                    }

                    // 4. ShiftOutput'ning jami bragini yangilaymiz (Observer xomashyoni ayiradi)
                    $shiftOutput->update(['defect_amount' => $totalStageDefect]);
                }
            }
        });

        return redirect()->route('shift.index', $shift->id)->with('success', 'Смена махсулоти яратилди!');
    }


    public function edit(ShiftOutput $shiftOutput)
    {
        // Faqat bitta stage uchun
        $stage = Stage::where('id', $shiftOutput->stage_id)
            ->where('status', StatusService::STATUS_ACTIVE)
            ->first();

        $workers = $shiftOutput->shift->user()->get();

        $oldInputs = [];
        $workerDefects = [];

        foreach ($workers as $worker) {
            $workerOutput = $shiftOutput->shiftOutputWorkers->where('user_id', $worker->id)->first();

            $oldInputs[$worker->id][$shiftOutput->stage_id] = [
                'stage_count' => $workerOutput->stage_count ?? 0,
            ];

            // Faqat shu stage uchun defect summasi
            $workerDefects[$worker->id] = \App\Models\ShiftOutputWorker::where('shift_output_id', $shiftOutput->id)
                ->where('user_id', $worker->id)
                ->sum('defect_amount');
        }

        return view('backend.shift-output.update', compact(
            'shiftOutput',
            'stage',
            'workers',
            'oldInputs',
            'workerDefects'
        ));
    }


    public function update(Request $request, ShiftOutput $shiftOutput)
    {
        $inputs = $request->input('input', []);
        $stageId = $shiftOutput->stage_id;

        foreach ($inputs as $workerId => $data) {
            if (isset($data[$stageId]['stage_count'])) {
                $inputs[$workerId][$stageId]['stage_count'] = (int)preg_replace('/[^\d]/', '', $data[$stageId]['stage_count']);
            }
            if (isset($data[$stageId]['defect_amount'])) {
                $inputs[$workerId][$stageId]['defect_amount'] = (float)preg_replace('/[^0-9.]/', '', $data[$stageId]['defect_amount']);
            }
        }

        $request->merge(['input' => $inputs]);

        $request->validate([
            'input.*.*.stage_count' => 'nullable|integer|min:0',
            'input.*.*.defect_amount' => 'nullable|numeric|min:0',
        ]);

        foreach ($inputs as $userId => $stageData) {
            $data = $stageData[$stageId] ?? [];
            $worker = ShiftOutputWorker::firstOrNew([
                'shift_output_id' => $shiftOutput->id,
                'user_id' => $userId,
            ]);

            $worker->stage_count = $data['stage_count'] ?? 0;
            $worker->defect_amount = $data['defect_amount'] ?? 0;
            $worker->price = $shiftOutput->stage->price * ($worker->stage_count);
            $worker->save();
        }

        // Jami va defect
        $stageTotal = $shiftOutput->shiftOutputWorkers()->sum('stage_count');
        $defectTotal = $shiftOutput->shiftOutputWorkers()->sum('defect_amount');

        $shiftOutput->update([
            'stage_count' => $stageTotal,
            'defect_amount' => $defectTotal,
        ]);

        // Defect faqat 2% dan oshsa yozish
        if ($stageTotal > 0) {
            $limitAmount = $stageTotal * 0.02;
            $excess = max($defectTotal - $limitAmount, 0);

            if ($excess > 0) {
                $excessPercent = ($excess / $stageTotal) * 100;
                foreach ($shiftOutput->shiftOutputWorkers as $worker) {
                    $workerExcess = $defectTotal > 0
                        ? ($worker->defect_amount / $defectTotal) * $excess
                        : 0;

                    DefectReport::updateOrCreate(
                        [
                            'shift_id' => $shiftOutput->shift_id,
                            'stage_id' => $stageId,
                            'user_id'  => $worker->user_id,
                        ],
                        [
                            'organization_id'    => $shiftOutput->shift->organization?->id,
                            'section_id'         => $shiftOutput->shift->section_id,
                            'stage_count'        => $worker->stage_count,
                            'total_defect_amount' => $defectTotal,
                            'defect_amount'      => round($workerExcess, 2),
                            'defect_percent'     => round($excessPercent, 2),
                        ]
                    );
                }

                $msg =
                    "<b>♻ Брак янгиланди!</b>\n"
                    . "🏭 Филиал: <b>{$shiftOutput->shift->organization?->title}</b>\n"
                    . "📍 Бўлим: <b>{$shiftOutput->shift->section?->title}</b>\n"
                    . "🛠 Смена: <b>{$shiftOutput->shift->title}</b>\n"
                    . "⚙️ Махсулот: <b>{$shiftOutput->stage->title}</b>\n"
                    . "🔢 Умумий брак: <b>{$defectTotal} кг</b>\n"
                    . "🔻 Лимитдан ошган: <b>{$excess} кг</b>\n"
                    . "📊 Ошган фоиз: <b>" . round($excessPercent, 2) . "%</b>";

                TelegramHelper::notifyDefect($msg);
            } else {
                // Limit oshmagan → eski defectlar o‘chirilsin
                DefectReport::where('shift_id', $shiftOutput->shift_id)
                    ->where('stage_id', $stageId)
                    ->delete();
            }
        }

        return redirect()->route('shift-output-worker.list', $shiftOutput->id)
            ->with('success', 'Смена махсулоти янгиланди!');
    }


    public function destroy(ShiftOutput $shiftOutput)
    {
        $shiftOutput->delete();

        return response()->json([
            'message' => 'Смена махсулоти ўчирилди!',
            'type' => 'delete',
            'redirect' => route('shift-output.list', $shiftOutput->shift->id),
        ]);
    }
}
