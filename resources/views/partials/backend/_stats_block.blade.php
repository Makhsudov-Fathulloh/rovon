<h3 class="text-center mt-2">{{ $title }}:</h3>

<div class="text-center">
    <div class="row">
        @foreach (['daily' => 'Бугунги', 'monthly' => 'Ойлик', 'yearly' => 'Йиллик'] as $period => $label)
            @php
                $key = $period . $prefix; // masalan: dailyExpense, monthlyIncome, yearlyDebt
                $uzs = $data[$key]['UZS'] ?? 0;
                $usd = $data[$key]['USD'] ?? 0;
                $count = $data[$key]['count'] ?? 0;

                // 🔹 Har bir davr uchun fon rangi
                $baseClass = match ($period) {
                    'daily' => 'alert alert-info',     // ko‘k
                    'monthly' => 'alert alert-warning', // sariq
                    'yearly' => 'alert alert-success',  // yashil
                };

                // 🔸 Faqat text uchun danger class
                $textClass = ($uzs < 0 || $usd < 0) ? 'text-danger fw-bold' : '';
            @endphp

            <div class="col-md-4 mb-2">
                <div class="{{ $baseClass }} shadow-sm">
                    <div class="fw-bold">{{ $label }}:</div>

                    <div class="{{ $textClass }}">
                        <strong class="h4">{{ number_format($uzs, 0, '', ' ') }}</strong> сўм
                    </div>
                    <div class="{{ $textClass }}">
                        <strong class="h4">{{ number_format($usd, 2, '.', ',') }}</strong> $
                    </div>

                    <div class="mt-2 small text-muted">
                        <strong>Сони:</strong> {{ number_format($count, 0, '', ' ') }} та
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
