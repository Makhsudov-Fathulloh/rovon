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
        $stages = Stage::whereHas('shiftOutputs')
            ->with('section')
            ->get()
            ->mapWithKeys(function ($stage) {
                return [
                    $stage->id => $stage->title . ' (' . ($stage->section?->title ?? '-') . ')'
                ];
            });

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
                    'section_title' => $stage->section?->title,
                    'daily_product'   => $outputs->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('stage_count'),
                    'daily_defect'    => $outputs->whereBetween('created_at', [$dailyFrom, $dailyTo])->sum('defect_amount'),
                    'monthly_product' => $outputs->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('stage_count'),
                    'monthly_defect'  => $outputs->whereBetween('created_at', [$monthlyFrom, $monthlyTo])->sum('defect_amount'),

                    'monthly_defect_raw' => ($stage->defect_type === StatusService::DEFECT_RAW_MATERIAL)
                        ? $outputs->sum('defect_amount') : 0,

                    'monthly_defect_prev' => ($stage->defect_type === StatusService::DEFECT_PREVIOUS_STAGE)
                        ? $outputs->sum('defect_amount') : 0,

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
            'stageIds',
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
                    'section_title' => $stage->section?->title,
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
        $stages = Stage::where('section_id', $shift->section_id)->where('status', StatusService::STATUS_ACTIVE)->with('preStages')->get();

        $workers = $shift->users()->get();

        return view('backend.shift-output.create', compact('shift', 'stages', 'workers', 'shifts', 'sections'));
    }

    public function store(Request $request, Shift $shift)
    {
        $inputs = $request->input('input', []);
        $workerDefects = $request->input('worker_defects', []);
        $sourceStages = $request->input('source_stages', []);

        DB::transaction(function () use ($inputs, $workerDefects, $shift, $sourceStages) {
            $stages = Stage::where('section_id', $shift->section_id)->get();
            $workers = $shift->users()->get();

            foreach ($stages as $stage) {
                $stageTotalQty = 0;
                $stageTotalDefect = 0;
                $preparedWorkerData = [];

                // 1. Oldindan hisob-kitob qilamiz
                foreach ($workers as $worker) {
                    $qty = (int)preg_replace('/[^\d]/', '', $inputs[$worker->id][$stage->id]['stage_count'] ?? 0);

                    if ($qty > 0) {
                        // Ishchining jami ishini hisoblash (brakni proporsional bo'lish uchun)
                        $totalWorkerQtyAcrossAllStages = 0;
                        foreach ($stages as $s) {
                            $totalWorkerQtyAcrossAllStages += (int)preg_replace('/[^\d]/', '', $inputs[$worker->id][$s->id]['stage_count'] ?? 0);
                        }

                        $totalWorkerDefectInput = (float)($workerDefects[$worker->id] ?? 0);

                        $workerStageDefect = $totalWorkerQtyAcrossAllStages > 0
                            ? ($qty / $totalWorkerQtyAcrossAllStages) * $totalWorkerDefectInput
                            : 0;

                        $stageTotalQty += $qty;
                        $stageTotalDefect += $workerStageDefect;

                        $preparedWorkerData[] = [
                            'worker' => $worker,
                            'qty' => $qty,
                            'defect' => $workerStageDefect
                        ];
                    }
                }

                // 2. Agar ushbu stageda ish bo'lgan bo'lsa, BIR MARTA create qilamiz
                if ($stageTotalQty > 0) {
                    $sourceStageId = $sourceStages[$stage->id] ?? null;
                    $sourceSectionId = $sourceStageId ? Stage::find($sourceStageId)?->section_id : null;

                    // ShiftOutput yaratish (BIR MARTA - hamma miqdorlar bilan)
                    $shiftOutput = $shift->shiftOutputs()->create([
                        'stage_id' => $stage->id,
                        'source_stage_id' => $sourceStageId,
                        'source_section_id' => $sourceSectionId,
                        'stage_count' => $stageTotalQty,
                        'defect_amount' => $stageTotalDefect, // Endi bu 0 emas!
                    ]);

                    // 3. Ishchilar kesimida saqlash va Brak Report
                    foreach ($preparedWorkerData as $data) {
                        $w = $data['worker'];
                        $wQty = $data['qty'];
                        $wDefect = $data['defect'];

                        ShiftOutputWorker::create([
                            'shift_output_id' => $shiftOutput->id,
                            'stage_id'      => $stage->id,
                            'user_id'       => $w->id,
                            'stage_count'   => $wQty,
                            'defect_amount' => round($wDefect, 3),
                            'price'         => $stage->price * $wQty,
                        ]);

                        if ($stage->defect_type === StatusService::DEFECT_RAW_MATERIAL) {
                            // Xomashyo kg da
                            $totalWorkerMaterialWeight = 0;
                            foreach ($stage->stageMaterials as $stageMaterial) {
                                // Ishchining chiqargan miqdori uchun ketgan xomashyo (brakni qo'shmagan holda)
                                $requiredForWorker = $shiftOutput->calculateRequired(
                                    $stageMaterial,
                                    $wQty,
                                    0 // Faqat sof sarfni bilish uchun brakni 0 beramiz
                                );
                                $totalWorkerMaterialWeight += $requiredForWorker;
                            }

                            // Brak limitini tekshirish
                            $limitAmount = $totalWorkerMaterialWeight * 0.02;

                            if ($workerStageDefect > $limitAmount) {
                                $this->sendMaterialDefectNotify($shift, $w, $stage, $wQty, $wDefect, $limitAmount);
                            }
                        } elseif ($stage->defect_type === StatusService::DEFECT_PREVIOUS_STAGE) {
                            $limitAmount = 0;

                            if ($workerStageDefect > $limitAmount) {
                                $this->sendPreviousDefectNotify($shift, $w, $stage, $wQty, $wDefect, $limitAmount);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('shift.index')->with('success', 'Смена махсулоти яратилди!');
    }

    public function edit(ShiftOutput $shiftOutput)
    {
        $stage = Stage::where('id', $shiftOutput->stage_id)
            ->where('status', StatusService::STATUS_ACTIVE)
            ->first();

//        $stages = Stage::where('section_id', $shiftOutput->shift->section_id)
//            ->where('status', StatusService::STATUS_ACTIVE)
//            ->get();

        $stages = Stage::whereHas('shiftOutputs', function ($q) use ($shiftOutput) {
            $q->where('id', $shiftOutput->id)
                ->where(function ($q) {
                    $q->where('stage_count', '>', 0)
                        ->orWhere('defect_amount', '>', 0);
                });
        })
            ->where('status', StatusService::STATUS_ACTIVE)
            ->get();


        $workers = $shiftOutput->shift->users()->get();

        $oldInputs = [];
        $workerDefects = [];

        foreach ($workers as $worker) {
            $workerOutput = $shiftOutput->shiftOutputWorkers->where('user_id', $worker->id)->first();

            $oldInputs[$worker->id][$shiftOutput->stage_id] = [
                'stage_count' => $workerOutput->stage_count ?? 0,
            ];

            // Faqat shu stage uchun defect summasi
            $workerDefects[$worker->id] = ShiftOutputWorker::where('shift_output_id', $shiftOutput->id)
                ->where('user_id', $worker->id)
                ->sum('defect_amount');
        }

        return view('backend.shift-output.update', compact(
            'shiftOutput',
            'stage',
            'stages',
            'workers',
            'oldInputs',
            'workerDefects'
        ));
    }

    public function update(Request $request, ShiftOutput $shiftOutput)
    {
        $inputs = $request->input('input', []);
        $stageId = $shiftOutput->stage_id;
        $sourceStageId = $request->input('source_stage_id');
        $stage = $shiftOutput->stage;

        $sourceSectionId = null;
        if ($sourceStageId) {
            $sourceSectionId = Stage::find($sourceStageId)?->section_id;
        }

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

        DB::transaction(function () use ($shiftOutput, $inputs, $stageId, $sourceStageId, $sourceSectionId, $stage) {

            // 1. Ishchilar ma'lumotlarini yangilash
            foreach ($inputs as $userId => $stageData) {
                $data = $stageData[$stageId] ?? [];
                $wQty = (int)($data['stage_count'] ?? 0);
                $wDefect = (float)($data['defect_amount'] ?? 0);

                ShiftOutputWorker::updateOrCreate(
                    ['shift_output_id' => $shiftOutput->id, 'user_id' => $userId],
                    [
                        'stage_id' => $stageId,
                        'stage_count' => $wQty,
                        'defect_amount' => $wDefect,
                        'price' => $stage->price * $wQty,
                    ]
                );
            }

            // 2. Jami miqdorlarni yangilash
            $shiftOutput->update([
                'stage_count' => $shiftOutput->shiftOutputWorkers()->sum('stage_count'),
                'defect_amount' => $shiftOutput->shiftOutputWorkers()->sum('defect_amount'),
                'source_stage_id'   => $sourceStageId,
                'source_section_id' => $sourceSectionId,
            ]);

            // 3. Har bir ishchi uchun alohida limit tekshiruvi va Telegram
            foreach ($shiftOutput->shiftOutputWorkers as $worker) {
                $wQty = $worker->stage_count;
                $wDefect = $worker->defect_amount;

                if ($wQty <= 0 && $wDefect <= 0) {
                    DefectReport::where([
                        'shift_id' => $shiftOutput->shift_id,
                        'stage_id' => $shiftOutput->stage_id,
                        'user_id' => $worker->user_id,
                    ])->delete();
                    continue;
                }

                if ($stage->defect_type === StatusService::DEFECT_RAW_MATERIAL) {
                    $workerMaterialWeight = 0;
                    foreach ($stage->stageMaterials as $mat) {
                        $workerMaterialWeight += $shiftOutput->calculateRequired($mat, $wQty, 0);
                    }
                    $limitAmount = $workerMaterialWeight * 0.02;

                    if ($wDefect > $limitAmount) {
                        // Yangi yaratmaydi, borini update qiladi va xabar yuboradi
                        $this->sendMaterialDefectNotify($shiftOutput->shift, $worker->user, $stage, $wQty, $wDefect, $limitAmount, true);
                    } else {
                        DefectReport::where([
                            'shift_id' => $shiftOutput->shift_id,
                            'stage_id' => $stageId,
                            'user_id'  => $worker->user_id
                        ])->delete();                    }
                } elseif ($stage->defect_type === StatusService::DEFECT_PREVIOUS_STAGE) {
                    if ($wDefect > 0) {
                        $this->sendPreviousDefectNotify($shiftOutput->shift, $worker->user, $stage, $wQty, $wDefect, 0, true);
                    } else {
                        DefectReport::where([
                            'shift_id' => $shiftOutput->shift_id,
                            'stage_id' => $stageId,
                            'user_id'  => $worker->user_id
                        ])->delete();
                    }
                }
            }
        });

        // return redirect()->route('shift-output-worker.list', $shiftOutput->id)->with('success', 'Смена махсулоти янгиланди!');
        return redirect()->route('shift-output.index')->with('success', 'Смена махсулоти янгиланди!');
    }


    private function sendMaterialDefectNotify($shift, $w, $stage, $qty, $defect, $limit, $isUpdate = false)
    {
        $excess = $defect - $limit;
        $percent = ($defect / $qty) * 100;

        // Dublikatni oldini olish: shift, stage va ishchi bo'yicha qidiradi
        DefectReport::updateOrCreate(
            [
                'shift_id' => $shift->id,
                'stage_id' => $stage->id,
                'user_id'  => $w->id
            ],
            [
                'organization_id'     => $shift->section->organization_id,
                'section_id'          => $shift->section_id,
                'stage_count'         => $qty,
                'total_defect_amount' => round($defect, 3),
                'defect_amount'       => round($excess, 3),
                'defect_type'         => $stage->defect_type,
                'defect_percent'      => round($percent, 2),
            ]
        );

        $header = $isUpdate ? "<b>♻️ Лимитидан ошган брак янгиланди!</b>" : "<b>❗ Брак лимитидан ошди!</b>";

        $msg = $header . "\n\n"
            . "👤 Ишчи: <b>{$w->username}</b>\n"
            . "🏭 Филиал: <b>{$shift->organization?->title}</b>\n"
            . "📍 Бўлим: <b>{$shift->section?->title}</b>\n"
            . "⚙️ Махсулот: <b>{$stage->title}</b>\n"
            . "🔢 Чиқарилган: <b>{$qty} дона</b>\n"
            . "🔻 Ишчи браки: <b>" . round($defect, 3) . " кг</b>\n"
            . "⚠️ Лимит (2%): <b>" . round($limit, 3) . " кг</b>\n"
            . "🚫 Ортиқча: <b>" . round($excess, 3) . " кг</b>\n"
            . "📊 Фоиз: <b>" . round($percent, 2) . "%</b>";

        TelegramHelper::notifyDefect($msg);
    }

    private function sendPreviousDefectNotify($shift, $w, $stage, $qty, $defect, $limit, $isUpdate = false)
    {
        $percent = ($defect / $qty) * 100;

        // Dublikatni oldini olish: shift, stage va ishchi bo'yicha qidiradi
        DefectReport::updateOrCreate(
            [
                'shift_id' => $shift->id,
                'stage_id' => $stage->id,
                'user_id'  => $w->id
            ],
            [
                'organization_id'     => $shift->section->organization_id,
                'section_id'          => $shift->section_id,
                'stage_count'         => $qty,
                'total_defect_amount' => round($defect, 3),
                'defect_amount'       => round($defect, 3),
                'defect_type'         => $stage->defect_type,
                'defect_percent'      => round($percent, 2),
            ]
        );

        $header = $isUpdate ? "<b>♻️ Аниқланган брак янгиланди!</b>" : "<b>❗ Брак аниқланди!</b>";

        $msg = $header . "\n\n"
            . "👤 Ишчи: <b>{$w->username}</b>\n"
            . "🏭 Филиал: <b>{$shift->organization?->title}</b>\n"
            . "📍 Бўлим: <b>{$shift->section?->title}</b>\n"
            . "⚙️ Махсулот: <b>{$stage->title}</b>\n"
            . "🔢 Чиқарилган: <b>{$qty} дона</b>\n"
            . "🔻 Ишчи браки: <b>" . round($defect, 3) . " кг/дона</b>\n"
            . "⚠️ Лимит (0%): <b>" . round($limit, 3) . " кг/дона</b>\n"
            . "📊 Фоиз: <b>" . round($percent, 2) . "%</b>";

        TelegramHelper::notifyDefect($msg);
    }


    public function destroy(ShiftOutput $shiftOutput)
    {
        $shiftOutput->delete();

        return response()->json([
            'message' => 'Смена махсулоти ўчирилди!',
            'type' => 'delete',
            // 'redirect' => route('shift-output.list', $shiftOutput->shift->id),
            'redirect' => route('shift-output.index'),
        ]);
    }
}
