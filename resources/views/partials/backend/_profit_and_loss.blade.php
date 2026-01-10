<h3 class="text-center mt-2">{{ $title }}</h3>

<div class="text-center">
    <div class="row">
        @foreach (['daily' => 'Бугунги', 'monthly' => 'Ойлик', 'yearly' => 'Йиллик'] as $period => $label)
            @php
                $item = $data[$period][$type] ?? ['UZS' => 0, 'count' => 0];
                $uzs = $item['UZS'];
                $count = $item['count'];

                $baseClass = match ($period) {
                    'daily' => 'alert alert-info',
                    'monthly' => 'alert alert-warning',
                    'yearly' => 'alert alert-success',
                };

                $textClass = $uzs < 0 ? 'text-danger fw-bold' : '';
            @endphp

            <div class="col-md-4 mb-3">
                <div class="{{ $baseClass }} shadow-sm">
                    <div class="fw-bold">{{ $label }}:</div>
                    <div class="{{ $textClass }}">
                        <strong class="h4">{{ number_format($uzs, 0, '', ' ') }}</strong> сўм
                    </div>
                    <div class="mt-2 small text-muted">
                        <strong>Сони:</strong> {{ number_format($count, 0, '', ' ') }} та
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
