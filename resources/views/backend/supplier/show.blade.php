@php
    use App\Helpers\PriceHelper;
@endphp

<x-backend.layouts.main title="Фирма: {{ $supplier->title }}">
    <div class="container-fluid">
        <div class="row g-3 mb-4">
            @foreach($balances as $bal)
            @php $debt = $bal->total_yuk - $bal->total_tolov; @endphp
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-top border-4 {{ $debt > 0 ? 'border-danger' : 'border-success' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-muted">Валюта: {{ \App\Services\StatusService::getCurrency()[$bal->currency] }}</span>
                            <i class="fa {{ $debt > 0 ? 'fa-arrow-up text-danger' : 'fa-check text-success' }}"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ PriceHelper::format($debt, $bal->currency, false) }}</h3>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Юк: {{ PriceHelper::format($bal->total_yuk, $bal->currency, false) }}</span>
                            <span>Тўлов: {{ PriceHelper::format($bal->total_tolov, $bal->currency, false) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fa fa-plus-circle me-1"></i> Янги амалиёт қайд етиш</h6>
            </div>
            <div class="card-body bg-light">
                <form action="{{ route('supplier.item.store', $supplier->id) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-2">
                        <label class="small fw-bold">Aмалиёт тури:</label>
                        <select name="type" class="form-select border-0 shadow-sm" required>
                            <option value="1">📦 Юк келди (-)</option>
                            <option value="2">💸 Тўлов қилинди (+)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Валюта:</label>
                        <select name="currency" id="currency" class="form-select border-0 shadow-sm" required>
                            @foreach(\App\Services\StatusService::getCurrency() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Сумма:</label>
                        <input type="number" name="amount" step="0.001" class="form-control border-0 shadow-sm" placeholder="0.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Курс:</label>
                        <input type="number" name="rate" id="rate" step="0.01" class="form-control border-0 shadow-sm" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold">Изоҳ:</label>
                        <input type="text" name="description" class="form-control border-0 shadow-sm" placeholder="">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold">OK</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <form action="{{ route('supplier.show', $supplier->id) }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm border-secondary-subtle shadow-sm">
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm border-secondary-subtle shadow-sm">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Филтрлаш</button>
                        <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-sm btn-outline-secondary px-3">Тозалаш</a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 small fw-bold">№</th>
                            <th class="small fw-bold">Сана</th>
                            <th class="small fw-bold">Тури</th>
                            <th class="small fw-bold">Сумма</th>
                            <th class="small fw-bold">Қурс</th>
                            <th class="small fw-bold">Изоҳ</th>
                            <th class="small fw-bold">Ҳаражат рақами</th>
                            <th class="pe-4 small fw-bold">Aмаллар</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td class="small">{{ $item->id }}</td>
                            <td class="small">{{ $item->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $item->type == 1 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} px-3 rounded-pill">
                                    {{ $item->type == 1 ? 'Юк келди' : 'Тўлов қилинди' }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ PriceHelper::format($item->amount, $item->currency) }}</td>
                            <td class="text-muted small">{{ PriceHelper::format($item->rate, $item->currency, false) }}</td>
                            <td class="small">{{ $item->description }}</td>
                            <td class="small">{{ optional($item->expense)->title ?? '-' }}</td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}">
                                        <i class="fa fa-edit text-warning"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <form action="{{ route('supplier.item.update', $item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h6 class="modal-title fw-bold">Aмалиётни таҳрирлаш #{{ $item->id }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="small fw-bold">Aмалиёт тури:</label>
                                                    <select name="type" class="form-select border-0 shadow-sm">
                                                        <option value="1" {{ $item->type == 1 ? 'selected' : '' }}>Юк келди</option>
                                                        <option value="2" {{ $item->type == 2 ? 'selected' : '' }}>Тўлов қилинди</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="small fw-bold">Валюта:</label>
                                                    <select name="currency" class="form-select border-0 shadow-sm js-currency">
                                                        @foreach(\App\Services\StatusService::getCurrency() as $id => $name)
                                                            <option value="{{ $id }}" {{ $item->currency == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="small fw-bold">Сумма:</label>
                                                    <input type="number" name="amount" step="0.001" class="form-control border-0 shadow-sm" value="{{ $item->amount }}" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="small fw-bold">Курс:</label>
                                                    <input type="number" name="rate" step="0.001" class="form-control border-0 shadow-sm js-rate" value="{{ $item->rate }}" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small fw-bold">Изох:</label>
                                                    <input type="text" name="description" class="form-control border-0 shadow-sm" value="{{ $item->description }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Ёпиш</button>
                                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Янгилаш</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-muted">Ҳозирча амалиётлар мавжуд емас.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">{{ $items->links() }}</div>
        </div>
    </div>

    @php
        $usdRate = \App\Models\ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;
    @endphp

    <script>
        const currencySelect = document.getElementById('currency');
        const rateInput = document.getElementById('rate');

        const USD_ID = "{{ \App\Services\StatusService::CURRENCY_USD }}";
        const usdRate = {{ $usdRate }};

        currencySelect.addEventListener('change', function () {
            if (this.value == USD_ID) {
                rateInput.value = usdRate;
                rateInput.readOnly = true;
            } else {
                rateInput.value = 1;
                rateInput.readOnly = false;
            }
        });

        function handleCurrencyChange(currencySelect) {
            const modal = currencySelect.closest('.modal, form');
            const rateInput = modal.querySelector('.js-rate');

            if (!rateInput) return;

            if (currencySelect.value == USD_ID) {
                rateInput.value = usdRate;
                rateInput.readOnly = true;
            } else {
                rateInput.value = 1;
                rateInput.readOnly = false;
            }
        }

        // Yangi amal (asosiy forma)
        document.getElementById('currency')?.addEventListener('change', function () {
            handleCurrencyChange(this);
        });

        // Modal’lar uchun
        document.querySelectorAll('.js-currency').forEach(select => {
            select.addEventListener('change', function () {
                handleCurrencyChange(this);
            });

            // modal ochilganda ham to‘g‘ri qiymat qo‘yish
            handleCurrencyChange(select);
        });
    </script>

</x-backend.layouts.main>
