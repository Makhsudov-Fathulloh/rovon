<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DefectReport;
use App\Models\Organization;
use App\Models\Search\DefectReportSearch;
use App\Models\Search\ShiftOutputSearch;
use App\Models\Section;
use App\Models\Shift;
use App\Models\ShiftOutput;
use App\Models\ShiftOutputWorker;
use App\Models\Stage;
use App\Services\DateFilterService;
use App\Services\StatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DefectReportController extends Controller
{
    public function index(Request $request)
    {
        $searchModel = new DefectReportSearch(new DateFilterService());
        $query = $searchModel->search($request);

        $sort = $request->get('sort');
        if (!empty($sort)) {
            $direction = 'asc';
            if (Str::startsWith($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            if (Schema::hasColumn('defect_report', $sort)) {
                $query->orderBy($sort, $direction);
            }
        }

        $organizations = Organization::whereIn('id', DefectReport::select('organization_id')->distinct())->pluck('title', 'id');
        $sections = Section::whereIn('id', DefectReport::select('section_id')->distinct())->pluck('title', 'id');
        $shifts = Shift::whereIn('id', DefectReport::select('shift_id')->distinct())->pluck('title', 'id');
        $stages = Stage::whereIn('id', DefectReport::select('stage_id')->distinct())->pluck('title', 'id');

        $isFiltered = count($request->get('filters', [])) > 0;

        $queryBase = $isFiltered
            ? DefectReport::whereMonth('created_at', now()->month)
            : DefectReport::query();

        $rawAmount = (clone $queryBase)
            ->where('defect_type', StatusService::DEFECT_RAW_MATERIAL)
            ->sum('defect_amount');

        $rawCount = (clone $queryBase)
            ->where('defect_type', StatusService::DEFECT_RAW_MATERIAL)
            ->count();

        $prevAmount = (clone $queryBase)
            ->where('defect_type', StatusService::DEFECT_PREVIOUS_STAGE)
            ->sum('defect_amount');

        $prevCount = (clone $queryBase)
            ->where('defect_type', StatusService::DEFECT_PREVIOUS_STAGE)
            ->count();

        $defectReports = $query->paginate(20)->withQueryString();

        return view('backend.defect-report.index', compact(
            'defectReports',
            'organizations',
            'sections',
            'shifts',
            'stages',
            'rawAmount',
            'rawCount',
            'prevAmount',
            'prevCount',
        ));
    }


    public function show(DefectReport $defectReport)
    {
        return view('backend.defect-report.show', compact('defectReport'));
    }
}
