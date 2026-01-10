{{--<h3 class="text-center mt-4">{{ $title }}:</h3>--}}

{{--<div class="text-center">--}}
{{--    <div class="row">--}}
{{--        @foreach (['daily' => 'Бугунги', 'monthly' => 'Ойлик', 'yearly' => 'Йиллик'] as $period => $label)--}}
{{--            @php--}}
{{--                $key = $period . $prefix; // masalan: dailyPayment, monthlyPayment, yearlyPayment--}}
{{--                $uzsData = $data[$key]['UZS'] ?? [];--}}
{{--                $usdData = $data[$key]['USD'] ?? [];--}}

{{--                $baseClass = match ($period) {--}}
{{--                    'daily' => 'alert alert-info',--}}
{{--                    'monthly' => 'alert alert-warning',--}}
{{--                    'yearly' => 'alert alert-success',--}}
{{--                };--}}
{{--            @endphp--}}

{{--            <div class="col-md-4 mb-3">--}}
{{--                <div class="{{ $baseClass }} shadow-sm">--}}
{{--                    <div class="fw-bold mb-2">{{ $label }}:</div>--}}

{{--                    <table class="table table-sm table-bordered bg-white text-start">--}}
{{--                        <thead class="table-light">--}}
{{--                        <tr>--}}
{{--                            <th>Тури</th>--}}
{{--                            <th>UZS</th>--}}
{{--                            <th>USD</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @foreach (['cash_paid' => 'Нақд', 'card_paid' => 'Карта', 'transfer_paid' => 'Ўтказма', 'bank_paid' => 'Банк'] as $field => $name)--}}
{{--                            @php--}}
{{--                                $uzs = $uzsData[$field] ?? 0;--}}
{{--                                $usd = $usdData[$field] ?? 0;--}}
{{--                            @endphp--}}
{{--                            <tr>--}}
{{--                                <td>{{ $name }}</td>--}}
{{--                                <td class="{{ $uzs < 0 ? 'text-danger' : '' }}">--}}
{{--                                    {{ number_format($uzs, 0, '', ' ') }}--}}
{{--                                </td>--}}
{{--                                <td class="{{ $usd < 0 ? 'text-danger' : '' }}">--}}
{{--                                    {{ number_format($usd, 2, '.', ',') }}--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--</div>--}}

<h3 class="text-center mt-2">{{ $title }}:</h3>

<div class="text-center">
    <div class="row">
        @foreach (['daily' => 'Бугунги', 'monthly' => 'Ойлик', 'yearly' => 'Йиллик'] as $period => $label)
            @php
                $key = $period . $prefix;

                $uzsData = $data[$key]['UZS'] ?? [];
                $usdData = $data[$key]['USD'] ?? [];

                $totalUzs = array_sum($uzsData);
                $totalUsd = array_sum($usdData);

                $count = array_reduce($uzsData, fn($c, $v) => $c + ($v != 0 ? 1 : 0), 0);

                $baseClass = match ($period) {
                    'daily' => 'alert alert-info',
                    'monthly' => 'alert alert-warning',
                    'yearly' => 'alert alert-success',
                };

                $textClass = ($totalUzs < 0 || $totalUsd < 0) ? 'text-danger fw-bold' : '';
            @endphp

            <div class="col-md-4 mb-2">
                <div class="{{ $baseClass }} shadow-sm">
                    <div class="fw-bold">{{ $label }}:</div>

                    <!-- UZS umumiy -->
                    <div class="{{ $textClass }}">
                        <strong class="h4">{{ number_format($totalUzs, 0, '', ' ') }}</strong> сўм
                    </div>

                    <!-- USD umumiy -->
                    <div class="{{ $textClass }}">
                        <strong class="h4">{{ number_format($totalUsd, 2, '.', ',') }}</strong> $
                    </div>

                    <!-- To'lov turlari jadvali -->
                    <table class="table table-sm table-bordered bg-white mt-2 text-start">
                        <thead class="table-light small">
                        <tr>
                            <th>Тури</th>
                            <th>Сўм</th>
                            <th>$</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ([
                            'cash_paid' => 'Нақд',
                            'card_paid' => 'Карта',
                            'transfer_paid' => 'Ўтказма',
                            'bank_paid' => 'Банк'
                        ] as $field => $name)
                            <tr>
                                <td class="small">{{ $name }}</td>
                                <td class="small {{ ($uzsData[$field] ?? 0) < 0 ? 'text-danger' : '' }}">
                                    {{ number_format($uzsData[$field] ?? 0, 0, '', ' ') }}
                                </td>
                                <td class="small {{ ($usdData[$field] ?? 0) < 0 ? 'text-danger' : '' }}">
                                    {{ number_format($usdData[$field] ?? 0, 2, '.', ',') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Soni -->
                    <div class="mt-2 small text-muted">
                        <strong>Сони:</strong> {{ number_format($count, 0, '', ' ') }} та
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>

