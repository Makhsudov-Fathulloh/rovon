<x-backend.layouts.main title="Смена ҳисоботлари">

    <div class="card shadow">
        <div class="card-header d-flex justify-content-end align-items-center">
            <x-backend.action route="shift-report" :report="true" :todayReport="$todayReport"
                              isOpenLabel="Смена ҳисоботини очиш" isCloseLabel="Смена ҳисоботини ёпиш"
                              isOpenTextLabel="Смена ҳисоботини очишни истайсизми?"
                              isCloseTextLabel="Смена ҳисоботини ёпишни истайсизми?"
            />
        </div>

        <div class="card-body table-responsive">

            {{-- Desktop Table --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                    <tr class="text-center align-middle">
                        <th>№</th>
                        <th>Сана</th>
                        <th>Филиал</th>
                        <th>Бўлим</th>
                        <th>Смена</th>
                        <th>Сони</th>
                        <th>Брак</th>
                        <th>Ҳолат</th>
                        <th>
                            <form method="GET">
                                <input type="date" name="report_date" value="{{ request('report_date') }}"
                                       onchange="this.form.submit()" class="form-control form-control-sm mt-1">
                            </form>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->report_date }}</td>
                            <td>{{ $report->organization->title ?? '-' }}</td>
                            <td>{{ $report->section->title ?? '-' }}</td>
                            <td>{{ $report->shift->title ?? '-' }}</td>
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
                                    <span class="badge bg-secondary">Ёпиқ</span>
                                @endif
                            </td>
                            <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Version --}}
            <div class="d-md-none">
                <form method="GET">
                    <input type="date" name="report_date" value="{{ request('report_date') }}"
                           onchange="this.form.submit()"
                           class="form-control form-control-sm mt-1">
                </form>

                @foreach($reports as $report)
                    <div class="card border mb-2">
                        <div class="card-body">
                            <p><strong>№</strong> {{ $report->id }}</p>
                            <p><strong>Сана:</strong> {{ $report->report_date }}</p>
                            <p><strong>Филиал:</strong> {{ $report->organization->title ?? '-' }}</p>
                            <p><strong>Бўлим:</strong> {{ $report->section->title ?? '-' }}</p>
                            <p><strong>Смена:</strong> {{ $report->shift->title ?? '-' }}</p>
                            <p><strong>Сони:</strong>
                                @foreach($report->stage_product as $p)
                                    {{ $p['product_title'] }} — {{ $p['stage_count'] }} <br>
                                @endforeach
                            </p>
                            <p><strong>Брак:</strong> {{ $report->defect_amount }}</p>
                            <p><strong>Ҳолат:</strong>
                                @if($report->status == \App\Models\ShiftReport::SHIFT_OPEN)
                                    <span class="badge bg-success">Open</span>
                                @else
                                    <span class="badge bg-secondary">Closed</span>
                                @endif
                            </p>
                            <p><strong>Яратилган вақт:</strong> {{ $report->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

</x-backend.layouts.main>
