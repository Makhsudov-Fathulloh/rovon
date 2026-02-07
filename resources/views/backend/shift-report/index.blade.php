<x-backend.layouts.main title="Смена ҳисоботлари">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action route="shift-report" :back="true" :report="true" :todayReport="$todayReport"
                                  isOpenLabel="Смена ҳисоботини очиш" isCloseLabel="Смена ҳисоботини ёпиш"
                                  isOpenTextLabel="Смена ҳисоботини очишни истайсизми?"
                                  isCloseTextLabel="Смена ҳисоботини ёпишни истайсизми?"
                />
            </div>

            <div class="card-body p-0">
                <div class="table-responsive d-none d-md-block">
                    <table class="table text-center mb-0">
                        <thead>
                        <tr class="text-center">
                            <th class="col-id align-middle">№</th>
                            <th class="align-middle">Смена</th>
                            <th class="align-middle">Махсулот</th>
                            <th class="align-middle">Брак</th>
                            <th class="align-middle">Ҳолат</th>
                            <th>
                                <form method="GET">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="me-1">
                                            <input type="date"
                                                   name="report_date_from"
                                                   value="{{ request('report_date_from') }}"
                                                   max="{{ request('report_date_to') }}"
                                                   onchange="this.form.submit()"
                                                   class="form-control form-control-sm"
                                                   placeholder="From">
                                        </div>

                                        <div class="me-1">
                                            <input type="date"
                                                   name="report_date_to"
                                                   value="{{ request('report_date_to') }}"
                                                   min="{{ request('report_date_from') }}"
                                                   onchange="this.form.submit()"
                                                   class="form-control form-control-sm"
                                                   placeholder="To">
                                        </div>

                                        @if(request('report_date_from') || request('report_date_to'))
                                            <a href="{{ route('shift-report.index') }}"
                                               class="btn btn-link btn-sm text-danger">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ '(' . $report->organization->title . ') (' .  $report->section->title . ') ' . $report->shift->title }}</td>
                                <td>
                                    @foreach($report->stage_product as $p)
                                        {{ $p['product_title'] }} — {{ $p['stage_count'] }} <br>
                                    @endforeach
                                </td>
                                <td>{{ $report->defect_amount }}</td>
                                <td>
                                    @if($report->status == \App\Models\ShiftReport::SHIFT_OPEN)
                                        <span class="badge bg-success">Очиқ</span>
                                    @else
                                        <span class="badge bg-info">Ёпиқ</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('d.m.Y H:i')  }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-center">
                                    <img src="{{ asset('images/systems/reference-not-found.png') }}" width="60"
                                         class="mb-3 opacity-20" alt="">
                                    <p class="text-muted">Маълумот топилмади</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Version --}}
                <div class="d-md-none">
                    <form method="GET" class="m-4">
                        <div class="d-flex">
                            <input type="date"
                                   name="report_date_from"
                                   value="{{ request('report_date_from') }}"
                                   max="{{ request('report_date_to') }}"
                                   onchange="this.form.submit()"
                                   class="form-control form-control-sm"
                                   placeholder="From">

                            <input type="date"
                                   name="report_date_to"
                                   value="{{ request('report_date_to') }}"
                                   min="{{ request('report_date_from') }}"
                                   onchange="this.form.submit()"
                                   class="form-control form-control-sm"
                                   placeholder="To">

                            @if(request('report_date_from') || request('report_date_to'))
                                <a href="{{ route('shift-report.index') }}"
                                   class="btn btn-link btn-sm text-danger">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    @forelse($reports as $report)
                        <div class="mobile-card shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                        @if(optional($report->file)->path)
                                            <img src="{{ asset('storage/' . $report->file->path) }}" alt="">
                                        @else
                                            <div
                                                class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                <i class="bi bi-image"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div
                                            class="fw-bold mb-0 text-dark">{{ '(' . $report->organization->title . ') (' .  $report->section->title . ') ' . $report->shift->title }}</div>
                                        <span class="text-muted small">ID: {{ $report->id }}</span>
                                    </div>
                                </div>
                                <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div
                                                class="text-success">
                                                @foreach($report->stage_product as $p)
                                                    {{ $p['product_title'] }} — {{ $p['stage_count'] }} <br>
                                                @endforeach
                                            </div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-danger">{{ number_format($report->defect_amount, 3, '.', ' ') }}</div>
                                        </div>
                                    </span>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-center d-block" style="font-size: 1rem;">
                                        @if($report->status == \App\Models\ShiftReport::SHIFT_OPEN)
                                            <span class="badge bg-success">Open</span>
                                        @else
                                            <span class="badge bg-info">Closed</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                    <span
                                        class="small fw-medium">{{ $report->created_at?->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-5 text-center">
                            <img src="{{ asset('images/systems/reference-not-found.png') }}" width="45"
                                 class="mb-3 opacity-20" alt="">
                            <div class="py-4">Маълумот топилмади</div>
                        </div>
                    @endforelse
                </div>
                {{-- Mobile version end --}}

                <div class="card-footer bg-white border-top-0 p-4">
                    <div class="d-flex justify-content-center">
                        {{ $reports->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-backend.layouts.main>
