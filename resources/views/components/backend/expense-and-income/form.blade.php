@php use App\Models\ExpenseAndIncome; @endphp
<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">

                        {{-- Nomi --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Номи</label>
                            <input type="text" id="title" name="title" class="form-control"
                                   value="{{ old('title', $expenseAndIncome->title ?? '') }}">
                            @error('title')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tavsif --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Тавсифи</label>
                            <textarea id="description"
                                      name="description"
                                      class="form-control ckeditor"
                                      rows="10">{!! old('description', $expenseAndIncome->description ?? '') !!}</textarea>
                        </div>

                        {{-- To‘lov turi --}}
                        <div class="mb-3">
                            <label for="type_payment">Тўлов тури</label>
                            <select id="type_payment" name="type_payment" class="form-control">
                                @foreach(ExpenseAndIncome::getTypePaymentList() as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('type_payment', $expenseAndIncome->type_payment ?? ExpenseAndIncome::TYPE_PAYMENT_CASH) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                        {{ match($key) {
                                             \App\Models\ExpenseAndIncome::TYPE_PAYMENT_CASH=> 'Нақд',
                                             \App\Models\ExpenseAndIncome::TYPE_PAYMENT_TRANSFER => 'Ўтказма',
                                             \App\Models\ExpenseAndIncome::TYPE_PAYMENT_BANK => 'Ҳисоб рақам',
                                             default => '',
                                         } }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Миқдори</label>
                                    <input type="text" id="amount" name="amount"
                                           class="form-control filter-numeric-decimal"
                                           value="{{ old('amount', $expenseAndIncome->amount ?? '') }}">
                                    @error('amount')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="currency">Валюта</label>
                                    <select name="currency" id="currency" class="form-control">
                                        @foreach (\App\Services\StatusService::getCurrency() as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('currency', $expenseAndIncome->currency ?? 'UZS') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type">Тури</label>
                            <select id="type" class="form-control" disabled>
                                @foreach(ExpenseAndIncome::getTypeList() as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('type', $expenseAndIncome->type ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                        {{ match($key) {
                                            ExpenseAndIncome::TYPE_INCOME => 'Кирим',
                                            ExpenseAndIncome::TYPE_EXPENSE => 'Харажат',
                                            ExpenseAndIncome::TYPE_DEBT => 'Қарзни сўндириш',
                                            default => '',
                                        } }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="type"
                                   value="{{ old('type', $expenseAndIncome->type ?? '') }}">
                        </div>

                        <div class="mb-3" id="debt-user-wrapper"
                             style="display:none;">
                            <label for="user_id">Қарздор</label>
                            <select name="user_id" id="user_id" class="form-control filter-select2"
                                    data-placeholder="Қарздорни танланг">
                                <option value=""></option>
                                {{-- AJAX orqali yuklanadi --}}
                            </select>
                            @error('user_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            @if (Route::currentRouteName() == 'expense-and-income.create')
                                <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                            @elseif (Route::currentRouteName() == 'expense-and-income.edit')
                                <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function loadUsersByCurrency(currency, selectedUserId = null) {
        const type = $('input[name="type"]').val();
        $.get('{{ route('expense-and-income.users-by-currency') }}', {currency, type}, function (res) {
            $('#user_id').html(res.options);

            // Agar avval tanlangan user bo‘lsa — uni selectda belgilaymiz
            if (selectedUserId) {
                $('#user_id').val(selectedUserId).trigger('change');
            }
        });
    }

    $(document).ready(function () {
        const type = parseInt($('input[name="type"]').val());
        const initialCurrency = $('#currency').val();
        const selectedUserId = '{{ old('user_id', $expenseAndIncome->user_id ?? '') }}';

        if (type === {{ ExpenseAndIncome::TYPE_DEBT }}) {
            $('#debt-user-wrapper').show();
            loadUsersByCurrency(initialCurrency, selectedUserId);
        }

        $('#currency').change(function () {
            if (type === {{ ExpenseAndIncome::TYPE_DEBT }}) {
                loadUsersByCurrency($(this).val(), selectedUserId);
            }
        });
    });
</script>
