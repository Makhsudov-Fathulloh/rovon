@php
    use App\Helpers\PriceHelper;
    use App\Models\ExpenseAndIncome;
@endphp

<x-backend.layouts.main title="{{ 'Кирим ва харажат (Касса)' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    @if(!$todayReport || $todayReport->isClose())
                        <x-backend.action route="cash-report" :report="true" :todayReport="$todayReport"/>
                    @elseif($todayReport->isOpen())
                        <div class="col-sm-12 col-md-auto text-start btn-group">
                            <a href="{{ route('expense-and-income.create', ['type' => ExpenseAndIncome::TYPE_DEBT]) }}"
                               class="btn btn-success w-md-auto">
                                Қарзни сўндириш
                            </a>
                            <a href="{{ route('expense-and-income.create', ['type' => ExpenseAndIncome::TYPE_INCOME]) }}"
                               class="btn btn-primary w-md-auto ms-2">
                                Приход (Кирим)
                            </a>
                            <a href="{{ route('expense-and-income.create', ['type' => ExpenseAndIncome::TYPE_EXPENSE]) }}"
                               class="btn btn-danger w-md-auto ms-2">
                                Расход (Харажат)
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="expenseAndIncomeFilterForm" method="GET" action="{{ route('expense-and-income.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th class="nowrap">{!! sortLink('type_payment', 'Тўлов тури') !!}</th>
                                <th>{!! sortLink('amount', 'Миқдори') !!}</th>
                                <th>{!! sortLink('type', 'Тури') !!}</th>
                                <th>{!! sortLink('user_id', 'Қарздор') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th>
                                    <select name="filters[type_payment]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach( ExpenseAndIncome::getTypePaymentList() as $key => $label)
                                            {{--                                            <option value="{{ $key }}" {{ (string) request('filters.type_payment') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>--}}
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.type_payment') === (string) $key ? 'selected' : '' }}>
                                                {{ $label }}
                                                @if($key == ExpenseAndIncome::TYPE_PAYMENT_CASH)
                                                    Нақд
                                                @elseif($key == ExpenseAndIncome::TYPE_PAYMENT_TRANSFER)
                                                    Ўтказма
                                                @elseif($key == ExpenseAndIncome::TYPE_PAYMENT_BANK)
                                                    Ҳисоб рақам
                                                @elseif($key == ExpenseAndIncome::TYPE_PAYMENT_DEBT_RETURN)
                                                    Қарздан айриш
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[amount]" value="{{ request('filters.amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[type]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(ExpenseAndIncome::getTypeList() as $key => $label)
                                            {{--                                            <option value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>--}}
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>
                                                {{ $label }}
                                                @if($key == ExpenseAndIncome::TYPE_INCOME)
                                                    Кирим
                                                @elseif($key == ExpenseAndIncome::TYPE_EXPENSE)
                                                    Харажат
                                                @elseif($key == ExpenseAndIncome::TYPE_DEBT)
                                                    Қарзни сўндириш
                                                @elseif($key == ExpenseAndIncome::TYPE_RETURN)
                                                    Қайтим
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>{{ $username }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                  <div class="d-flex">
                                      <input type="date" name="filters[created_from]"
                                             value="{{ request('filters.created_from') }}"
                                             class="form-control form-control-sm me-1" placeholder="From">
                                      <input type="date" name="filters[created_to]"
                                             value="{{ request('filters.created_to') }}"
                                             class="form-control form-control-sm" placeholder="To">
                                  </div>
                                </th>

                                @if(session('date_format_errors'))
                                    <div class="alert alert-danger mt-2">
                                        <ul>
                                            @foreach(session('date_format_errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <th>
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($expenseAndIncomes as $expenseAndIncome)
                                <tr class="text-center" id="row-{{ $expenseAndIncome->id }}">
                                    <td class="col-id">{{ $expenseAndIncome->id }}</td>
                                    <td>{{ $expenseAndIncome->title }}</td>
                                    <td style="text-align: center; width: 100px">{{ ExpenseAndIncome::getTypePaymentList()[$expenseAndIncome->type_payment] }}</td>
                                    <td>{{ PriceHelper::format($expenseAndIncome->amount, $expenseAndIncome->currency) }}</td>
                                    <td style="text-align: center; width: 100px">{{ ExpenseAndIncome::getTypeList()[$expenseAndIncome->type] }}</td>
                                    <td>{{ optional($expenseAndIncome->user)->username }}</td>
                                    <td class="col-date">{{ $expenseAndIncome->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="expense-and-income" :id="$expenseAndIncome->id"
                                                          :view="true" :edit="true" :delete="true"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                   class="form-control form-control-sm me-1" placeholder="Харажат номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($expenseAndIncomes as $expenseAndIncome)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $expenseAndIncome->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!} </strong>{{ $expenseAndIncome->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('amount', 'Миқдори:') !!} </strong>{{ PriceHelper::format($expenseAndIncome->amount, $expenseAndIncome->currency) }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('type', 'Тури:') !!} </strong>{{ ExpenseAndIncome::getTypeList()[$expenseAndIncome->type] }}
                                    </p>
                                    @if(optional($expenseAndIncome->user)->username)
                                      <p class="card-text">
                                          <strong>{!! sortLink('user_id', 'Қарздор:') !!} </strong>{{ optional($expenseAndIncome->user)->username }}
                                      </p>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $expenseAndIncome->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="expense-and-income" :id="$expenseAndIncome->id"
                                                          :view="true" :edit="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                <div class="d-flex mt-3 justify-content-center">
                    {{ $expenseAndIncomes->links('pagination::bootstrap-4') }}
                </div>

                @can('hasAccess')
                    @include('partials.backend._stats_payment_block', ['data' => $payment, 'prefix' => 'Payment', 'title' => 'Касса (Нақд тўлов)'])
                    {{--@include('partials.backend._stats_block', ['data' => $amount, 'prefix' => 'AmountPaid', 'title' => 'Касса (Нақд тўлов)'])--}}
                    @include('partials.backend._stats_block', ['data' => $order, 'prefix' => 'Order', 'title' => 'Савдолар'])
                    @include('partials.backend._stats_block', ['data' => $expense, 'prefix' => 'Expense', 'title' => 'Харажат'])
                    @include('partials.backend._stats_block', ['data' => $income, 'prefix' => 'Income', 'title' => 'Кирим'])
                    @include('partials.backend._stats_block', ['data' => $debt, 'prefix' => 'Debt', 'title' => 'Қарз сўндириш'])
                    @include('partials.backend._stats_block', ['data' => $remaining, 'prefix' => 'RemainingDebt', 'title' => 'Қарздорлик'])
                @endcan

                <div class="card-header d-flex justify-content-end align-items-center">
                    <x-backend.action route="cash-report" :report="true" :todayReport="$todayReport"/>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('expenseAndIncomeFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('input[name="filters[title]"]').forEach(select => {
                if (select.offsetParent === null) {
                    select.disabled = true;
                }
            });

            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
