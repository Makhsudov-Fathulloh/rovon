<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\TelegramHelper;
use App\Http\Controllers\Controller;
use App\Models\ShiftReport;
use App\Models\Shift;
use App\Models\User;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftReportController extends Controller
{
    public function index(Request $request)
    {

        $query = ShiftReport::with(['organization', 'section', 'shift']);

        if ($request->filled('report_date')) {
            $query->whereDate('report_date', $request->report_date);
        }

        $reports = $query->orderBy('shift_id')->get();

        $overallStatus = $reports->every(fn($r) => $r->status == ShiftReport::SHIFT_CLOSE) ? ShiftReport::SHIFT_CLOSE : ShiftReport::SHIFT_OPEN;

        $now = now();

        $todayReport = $reports->first(function($report) use ($now) {
            $reportDate = Carbon::parse($report->report_date);
            $reportStart = $reportDate->setHour(6)->setMinute(0)->setSecond(0);
            $reportEnd   = $reportStart->copy()->addDay(); // keyingi 06:00
            return $now->between($reportStart, $reportEnd);
        });

        return view('backend.shift-report.index', compact(
            'reports',
            'overallStatus',
            'todayReport',
        ));
    }


    public function openShiftReport(Request $request)
    {
        $reportDate = $request->report_date ?? now()->format('Y-m-d');

        // Kunlik interval: bugun 6:00 → ertaga 6:00
        $dayStart = Carbon::parse($reportDate)->setTime(10, 0, 0);
        $dayEnd   = $dayStart->copy()->addDay()->setTime(10, 0, 0);

        // Barcha smenalarni yuklab olamiz
        $shifts = Shift::with(['shiftOutputs.stage', 'section.organization'])->get();

        $updated = false;

        foreach ($shifts as $shift) {
            // Shu smena uchun intervaldagi stage_outputlarni olamiz
            $outputs = $shift->shiftOutputs->filter(function ($o) use ($dayStart, $dayEnd) {
                return $o->created_at >= $dayStart && $o->created_at < $dayEnd;
            });

            // Agar output bo'lmasa, bu smenani o'tkazib yuboramiz
            if ($outputs->isEmpty()) continue;

            $stageProduct = [];
            foreach ($outputs as $o) {
                $title = $o->stage->title ?? '-';
                if (!isset($stageProduct[$title])) {
                    $stageProduct[$title] = [
                        'product_title' => $title,
                        'stage_count'   => 0,
                        'defect_amount' => 0,
                    ];
                }

                $stageProduct[$title]['stage_count']  += $o->stage_count ?? 0;
                $stageProduct[$title]['defect_amount'] += $o->defect_amount ?? 0;
            }

            // Reportni yaratish yoki olish
            $report = ShiftReport::updateOrCreate(
                [
                    'report_date' => $reportDate,
                    'shift_id'    => $shift->id,
                ],
                [
                    'organization_id' => $shift->section->organization_id,
                    'section_id'      => $shift->section_id,
                    'status'          => ShiftReport::SHIFT_OPEN,
                    'stage_product'   => [],
                    'defect_amount'   => 0,
                ]
            );

            // Eski stage_product bilan yangi outputs ni birlashtirish
            $oldStageProduct = collect($report->stage_product ?? []);
            $newStageProduct = collect($stageProduct);

            $merged = $oldStageProduct->mapWithKeys(function ($item) {
                return [$item['product_title'] => $item];
            });

            foreach ($newStageProduct as $item) {
                $title = $item['product_title'];
                if (isset($merged[$title])) {
                    $merged[$title]['stage_count']  += $item['stage_count'];
                    $merged[$title]['defect_amount'] += $item['defect_amount'];
                } else {
                    $merged[$title] = $item;
                }
            }

            // Update qilamiz
            $report->update([
                'stage_product' => array_values($merged->toArray()),
                'defect_amount' => array_sum(array_column($merged->toArray(), 'defect_amount')),
            ]);

            $updated = true;
        }

        if (!$updated) {
            return back()->with('info', 'Смена маҳсулоти мавжуд эмас, ҳисоботи очилмади!');
        }

        return back()->with('success', 'Смена ҳисоботи очилди!');
    }

    public function closeShiftReport(Request $request)
    {
        $reportDate = $request->report_date ?? now()->format('Y-m-d');

        $dayStart = Carbon::parse($reportDate)->setTime(10, 0, 0);
        $dayEnd   = $dayStart->copy()->addDay()->setTime(10, 0, 0);

        // Shu intervaldagi barcha shiftlarni olamiz
        $shifts = Shift::with(['shiftOutputs.stage', 'section.organization'])->get();

        $updated = false;

        foreach ($shifts as $shift) {
            // Shu smena uchun intervaldagi outputlarni olamiz
            $outputs = $shift->shiftOutputs->filter(function ($o) use ($dayStart, $dayEnd) {
                return $o->created_at >= $dayStart && $o->created_at < $dayEnd;
            });

            // Agar output bo‘lmasa, bu smenani o'tkazib yuboramiz
            if ($outputs->isEmpty()) continue;

            $stageProduct = [];
            foreach ($outputs as $o) {
                $title = $o->stage->title ?? '-';
                $defectType = $o->stage->defect_type ?? StatusService::DEFECT_PREVIOUS_STAGE;

                if (!isset($stageProduct[$title])) {
                    $stageProduct[$title] = [
                        'product_title' => $title,
                        'stage_count'   => 0,
                        'defect_amount' => 0,
                        'defect_type'   => $defectType,
                    ];
                }

                $stageProduct[$title]['stage_count']  += $o->stage_count ?? 0;
                $stageProduct[$title]['defect_amount'] += $o->defect_amount ?? 0;
            }

            $stageProduct = array_values($stageProduct);
            $totalDefect = array_sum(array_column($stageProduct, 'defect_amount'));

            // Reportni yaratish yoki yangilash
            $report = ShiftReport::updateOrCreate(
                [
                    'report_date' => $reportDate,
                    'shift_id'    => $shift->id,
                ],
                [
                    'organization_id' => $shift->section->organization_id,
                    'section_id'      => $shift->section_id,
                    'status'          => ShiftReport::SHIFT_CLOSE,
                    'stage_product'   => $stageProduct,
                    'defect_amount'   => $totalDefect,
                ]
            );

            $updated = true;
        }

        if (!$updated) {
            return back()->with('info', 'Смена маҳсулоти мавжуд эмас, ҳисоботи ёпилмади!');
        }

        // Telegramga yuborish
        $finalReports = ShiftReport::with(['shift','section','organization'])
            ->whereDate('report_date', $reportDate)
            ->get();

        $this->sendDailyReportToTelegram($finalReports, $reportDate);

        return back()->with('success', 'Смена ҳисоботи ёпилди!');
    }

    private function sendDailyReportToTelegram($reports, $reportDate)
    {
        // $users = User::whereHas('role', function ($query) {
        //     $query->whereIn('title', ['Developer', 'Admin', 'Manager']);
        // })->whereNotNull('telegram_chat_id')->get();

        // if ($users->isEmpty()) {
        //     return;
        // }

        $adminChatIds = array_filter(
            array_map('trim', explode(',', env('TELEGRAM_ADMINS')))
        );

        if (empty($adminChatIds)) {
            return;
        }

        $message = "<b>📊 Смена ҳисоботи ({$reportDate})</b>\n\n";

        foreach ($reports as $report) {

            $message .= "<b>🏭 Филиал:</b> {$report->organization->title}\n";
            $message .= "<b>📍 Бўлим:</b> {$report->section->title}\n";
            $message .= "<b>⏱ Смена:</b> {$report->shift->title}\n\n";

            $message .= "<b>📦 Махсулотлар:</b>\n";

            $totalDefectKg = 0;
            $totalDefectPcs = 0;

            if ($report->stage_product) {
                foreach ($report->stage_product as $p) {
                    $title  = $p['product_title'];
                    $count  = $p['stage_count'];
                    $defect = $p['defect_amount'];
                    $type   = $p['defect_type'] ?? '';

                    // Бирликни аниқлаш
                    if ($type === StatusService::DEFECT_PREVIOUS_STAGE) {
                        $unit = "дона";
                        $totalDefectPcs += $defect;
                    } else {
                        $unit = "кг";
                        $totalDefectKg += $defect;
                    }

                    $message .= "• {$title} — <b>{$count} дона</b>\n";
                    $message .= "   <i>Брак:</i> <b>{$defect} {$unit}</b>\n";
                }
            } else {
                $message .= "• Маълумот йўқ\n";
            }

            $message .= "\n<b>❗ Умумий брак:</b>\n";
            if ($totalDefectKg > 0) {
                $message .= "— Хом-ашё (кг): <b>{$totalDefectKg} кг</b>\n";
            }
            if ($totalDefectPcs > 0) {
                $message .= "— Тайёр маҳсулот (дона): <b>{$totalDefectPcs} дона</b>\n";
            }

            $message .= "-------------------------------------------------\n\n";
        }

        // foreach ($users as $user) {
        //     TelegramHelper::send($user->telegram_chat_id, $message);
        // }

        foreach ($adminChatIds as $chatId) {
            TelegramHelper::send($chatId, $message);
        }
    }
}
