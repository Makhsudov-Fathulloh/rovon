@php
    use App\Helpers\PriceHelper;
@endphp

<x-backend.layouts.main title="Кунлик касса ҳисоботлари">

    <div class="card shadow">
        <div class="card-header d-flex justify-content-start align-items-center">
            <x-backend.action route="cash-report" :back="true" :report="true" :todayReport="$todayReport"/>
        </div>

        <div class="card-body table-responsive">

            {{-- Desktop table --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                    <tr class="text-center align-middle">
                        <th>№</th>
                        <th>Жами буюртма</th>
                        <th>Жами тўланган</th>
                        <th>Жами қолган қарз</th>
                        <th>Жами қайтиш</th>
                        <th>Жами харажат</th>
                        <th>Жами кирим</th>
                        <th>Жами қарз сўндириш</th>
                        <th>Ҳолат</th>
                        <th>
                            <form method="GET">
                                <input type="date" name="created_at" value="{{ request('created_at') }}" onchange="this.form.submit()" class="form-control form-control-sm mt-1">
                            </form>
                        </th>
                    </tr>
                    </thead>

                    {{-- <tbody>
                    @forelse($cashReports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->report_date->format('Y-m-d') }}</td>
                            <td>{{ PriceHelper::format($report->total_order_amount, $report->currency) }}</td>
                            <td>{{ PriceHelper::format($report->total_amount_paid, $report->currency) }}</td>
                            <td>{{ PriceHelper::format($report->total_remaining_debt, $report->currency) }}</td>
                            <td>{{ PriceHelper::format($report->total_expense, $report->currency) }}</td>
                            <td>{{ PriceHelper::format($report->total_income, $report->currency) }}</td>
                            <td>{{ PriceHelper::format($report->total_debt_paid, $report->currency) }}</td>
                            <td>
                                @if($report->isOpen())
                                    <span class="badge bg-success">Очиқ</span>
                                @else
                                    <span class="badge bg-secondary">Ёпиқ</span>
                                @endif
                            </td>
                            <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Маълумот топилмади</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div> --}}

                    {{-- Mobile version start --}}
                    {{-- <div class="d-md-none">
                        <form method="GET">
                            <input type="date" name="created_at" value="{{ request('created_at') }}" onchange="this.form.submit()" class="form-control form-control-sm mt-1">
                        </form>
                        @forelse($cashReports as $report)
                            <div class="card border mb-2">
                                <div class="card-body">
                                    <p><strong>№ </strong>{{ $report->id }}</p>
                                    <p><strong>Сана: </strong>{{ $report->report_date->format('Y-m-d') }}</p>
                                    <p><strong>Жами буюртма: </strong>{{ PriceHelper::format($report->total_order_amount, $report->currency) }}</p>
                                    <p><strong>Жами тўланган: </strong>{{ PriceHelper::format($report->total_amount_paid, $report->currency) }}</p>
                                    <p><strong>Жами қолган қарз: </strong>{{ PriceHelper::format($report->total_remaining_debt, $report->currency) }}</p>
                                    <p><strong>Жами харажат: </strong>{{ PriceHelper::format($report->total_expense, $report->currency) }}</p>
                                    <p><strong>Жами кирим: </strong>{{ PriceHelper::format($report->total_income, $report->currency) }}</p>
                                    <p><strong>Жами қарз сўндириш: </strong>{{ PriceHelper::format($report->total_debt_paid, $report->currency) }}</p>
                                    <p><strong>Ҳолат: </strong>
                                        @if($report->isOpen())
                                            <span class="badge bg-success">Очиқ</span>
                                        @else
                                            <span class="badge bg-secondary">Ёпиқ</span>
                                        @endif
                                    </p>
                                    <p><strong>Яратилган вақт: </strong>{{ $report->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div> --}}
                    {{-- Mobile version end --}}

                    <tbody>
                    @forelse($cashReports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{!! PriceHelper::formatArray($report->total_order_amount, $currencies) !!}</td>
                            <td>{!! PriceHelper::formatArray($report->total_amount_paid, $currencies) !!}</td>
                            <td>{!! PriceHelper::formatArray($report->total_remaining_debt, $currencies) !!}</td>
                            <td>{!! PriceHelper::formatArray($report->total_return_amount, $currencies) !!}</td>
                            <td>{!! PriceHelper::formatArray($report->total_expense, $currencies) !!}</td>
                            <td>{!! PriceHelper::formatArray($report->total_income, $currencies) !!}</td>
                            <td>{!! PriceHelper::formatArray($report->total_debt_paid, $currencies) !!}</td>
                            <td>
                                @if($report->isOpen())
                                    <span class="badge bg-success">Очиқ</span>
                                @else
                                    <span class="badge bg-secondary">Ёпиқ</span>
                                @endif
                            </td>
                            <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Маълумот топилмади</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile version --}}
            <div class="d-md-none">
                <form method="GET">
                    <input type="date" name="created_at" value="{{ request('created_at') }}"
                           onchange="this.form.submit()" class="form-control form-control-sm mt-1">
                </form>

                @forelse($cashReports as $report)
                    <div class="card border mb-2">
                        <div class="card-body">
                            <p><strong>№ </strong>{{ $report->id }}</p>
                            <p><strong>Сана: </strong>{{ $report->report_date->format('Y-m-d') }}</p>
                            <p><strong>Жами буюртма: </strong>{!! PriceHelper::formatArray($report->total_order_amount, $currencies, false) !!}</p>
                            <p><strong>Жами тўланган: </strong>{!! PriceHelper::formatArray($report->total_amount_paid, $currencies, false) !!}</p>
                            <p><strong>Жами қолган қарз: </strong>{!! PriceHelper::formatArray($report->total_remaining_debt, $currencies, false) !!}</p>
                            <p><strong>Жами қайтиш: </strong>{!! PriceHelper::formatArray($report->total_return_amount, $currencies, false) !!}</p>
                            <p><strong>Жами харажат: </strong>{!! PriceHelper::formatArray($report->total_expense, $currencies, false) !!}</p>
                            <p><strong>Жами кирим: </strong>{!! PriceHelper::formatArray($report->total_income, $currencies, false) !!}</p>
                            <p><strong>Жами қарз сўндириш: </strong>{!! PriceHelper::formatArray($report->total_debt_paid, $currencies, false) !!}</p>
                            <p><strong>Ҳолат: </strong>
                                @if($report->isOpen())
                                    <span class="badge bg-success">Очиқ</span>
                                @else
                                    <span class="badge bg-secondary">Ёпиқ</span>
                                @endif
                            </p>
                            <p><strong>Яратилган вақт: </strong>{{ $report->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center">Маълумот топилмади</p>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $cashReports->links('pagination::bootstrap-4') }}
            </div>

        </div>
    </div>

</x-backend.layouts.main>
