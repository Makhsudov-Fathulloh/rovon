<x-backend.layouts.main title="Фирмалар">

    <div class="container-fluid">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body d-flex justify-content-between align-items-center bg-white rounded">
                <form action="{{ route('supplier.index') }}" method="GET" class="d-flex gap-2 w-50">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-end-0" placeholder="Номи ёки телефон">
                        <button type="submit" class="btn btn-primary px-4"><i class="fa fa-search"></i></button>
                    </div>
                </form>
                <button class="btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                    <i class="fa fa-plus-circle me-1"></i> Яратиш
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th class="fw-bold text-info ps-4">№</th>
                            <th class="fw-bold text-info">Номи</th>
                            <th class="fw-bold text-info">Телефон</th>
                            <th class="fw-bold text-info">Баланс</th>
                            <th class="fw-bold text-info">Статус</th>
                            <th class="fw-bold text-info">Амаллар</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr class="text-center">
                            <td>{{ $supplier->id ?? '—' }}</td>
                            <td class="fw-bold">{{ $supplier->title ?? '—' }}</td>
                             <td class="ps-4">
                                <div class="fw-bold">{{ $supplier->phone }}</div>
                                <small class="text-muted">Aдрес: {{ $supplier->address }}</small>
                            </td>
                            <td class="fw-bold
                                {{ $supplier->calculated_balance < 0 ? 'text-success' : ($supplier->calculated_balance > 0 ? 'text-danger' : '') }}">
                                {{ number_format($supplier->calculated_balance, 0, '.', ' ') }} сўм
                            </td>
                            <td>{{ \App\Services\StatusService::getList()[$supplier->status] }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-sm btn-primary px-3 shadow-sm">
                                        <i class="fa fa-calculator me-1"></i> Ҳисоб-китоб
                                    </a>
                                    <button
                                        class="btn btn-sm btn-light border ms-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSupplierModal{{ $supplier->id }}"
                                        title="Tahrirlash">
                                        <i class="fa fa-edit text-warning"></i>
                                    </button>
                                    <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('O\'chirilsinmi?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light border"><i class="fa fa-trash text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                                    @csrf
                                    @method('PUT') <div class="modal-header">
                                        <h5 class="modal-title">Таҳрирлаш: {{ $supplier->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Номи:</label>
                                            <input type="text" name="title" class="form-control" value="{{ $supplier->title }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Aдрес:</label>
                                            <input type="text" name="address" class="form-control" value="{{ $supplier->address }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Телефон:</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                                        </div>
                                        <div class="mb-3 mt-3">
                                            <label class="form-label">Статус:</label>
                                            <select name="status" class="form-control">
                                                @foreach (\App\Services\StatusService::getList() as $key => $label)
                                                    <option value="{{ $key }}" {{ $supplier->status == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Сақлаш</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Маълумот топилмади</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">{{ $suppliers->links() }}</div>
        </div>
    </div>

    <div class="modal fade" id="createSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('supplier.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 bg-light">
                        <h5 class="modal-title fw-bold text-primary">Янги фирма яратиш:</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Номи:</label>
                            <input type="text" name="title" class="form-control shadow-sm" placeholder="" required>
                        </div>
                         <div class="mb-3">
                            <label class="form-label fw-bold small">Aдрес:</label>
                            <input type="text" name="address" class="form-control shadow-sm" placeholder="" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Телефон:</label>
                            <input type="text" name="phone" class="form-control shadow-sm" placeholder="+998">
                        </div>
                         <div class="col-12">
                                <label class="form-label fw-bold small">Баланс:</label>
                                <input type="number" name="amount" class="form-control shadow-sm" value="0" step="0.001">
                            </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Валюта</label>
                                <select name="currency" id="currencySelect" class="form-select shadow-sm">
                                    @foreach(\App\Services\StatusService::getCurrency() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-bold small">Бошланғич курс:</label>
                                <input type="number" name="rate" id="rateInput"
                                    class="form-control shadow-sm"
                                    value="1" step="0.001">
                            </div>
                        </div>
                         <div class="mb-12">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Services\StatusService::getList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('status', $supplier->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Бекор қилиш</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Сақлаш</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@php
    $usdRate = \App\Models\ExchangeRates::where('currency', 'USD')->value('rate') ?? 1;
@endphp

<script>
    const currencySelect = document.getElementById('currencySelect');
    const rateInput = document.getElementById('rateInput');

    const usdRate = {{ $usdRate }};

    currencySelect.addEventListener('change', function() {
        if(this.value == "{{ \App\Services\StatusService::CURRENCY_USD }}") {
            rateInput.value = usdRate;
        } else {
            rateInput.value = 1; // default UZS
        }
    });

    // Sahifa yuklanganda ham USD bo‘lsa kursni set qilish
    window.addEventListener('DOMContentLoaded', function() {
        if(currencySelect.value == "{{ \App\Services\StatusService::CURRENCY_USD }}") {
            rateInput.value = usdRate;
        }
    });
</script>

</x-backend.layouts.main>
