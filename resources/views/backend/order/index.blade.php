<x-backend.layouts.main title="{{ 'Буюртмалар' }}">

    <style>
        .card-stats {
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            transition: 0.3s ease;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-width: 180px; /* minimal kenglik */
            flex: 1 1 200px; /* responsive */
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.3);
        }

        .card-stats.count { background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%); border-left: 5px solid #00d68f; }
        .card-stats.total { background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%); border-left: 5px solid #00a8ff; }
        .card-stats.paid { background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%); border-left: 5px solid #8e76ff; }
        .card-stats.debt { background: linear-gradient(135deg, #fd79a8 30%, #e84393 90%); border-left: 5px solid #ff6b81; }

        .card-stats h5 {
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1.25rem;
        }
        .card-stats p {
            margin: 2px 0;
            font-size: 0.95rem;
        }
        .card-stats i {
            font-size: 2.2rem;
            opacity: 0.7;
        }
    </style>

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    @if(!$todayReport || $todayReport->isClose())
                        <x-backend.action route="cash-report" :report="true" :todayReport="$todayReport"/>
                    @elseif($todayReport->isOpen())
                        <div class="col-sm-12 col-md-auto text-start">
                            <x-backend.action route="order" :back="true" :create="true"/>
                        </div>
                    @endif
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="orderFilterForm" method="GET" action="{{ route('order.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th class="col-title">{!! sortLink('user_id', 'Клиент') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th>{!! sortLink('total_price', 'Умумий') !!}</th>
                                <th>{!! sortLink('total_amount_paid', 'Тўланган') !!}</th>
                                <th>{!! sortLink('remaining_debt', 'Қарздорлик') !!}</th>
                                <th class="col-title">{!! sortLink('seller_id', 'Сотувчи') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                {{--                                <th>{!! sortLink('updated_at_exact', 'Янгиланди(сана)') !!}</th>--}}
                                {{--                                <th>{!! sortLink('updated_at', 'Янгиланди') !!}</th>--}}
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100 mb-4">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Models\Order::getStatusList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.status') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[total_price]"
                                           value="{{ request('filters.total_price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[total_amount_paid]"
                                           value="{{ request('filters.total_amount_paid') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[remaining_debt]"
                                           value="{{ request('filters.remaining_debt') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[seller_id]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($sellers as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.seller_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
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
                            @forelse($orders as $order)
                                <tr class="text-center" id="row-{{ $order->id }}">
                                    <td class="col-id">{{ $order->id }}</td>
                                    <td class="col-title">{{ $order->user->username }}</td>
                                    <td>{{ \App\Models\Order::getStatusList()[$order->status] ?? '-' }}</td>
                                    <td class="fw-bold text-info text-nowrap">
                                        {{ \App\Helpers\PriceHelper::format($order->total_price, $order->currency) }}
                                    </td>
                                    <td class="fw-bold text-success text-nowrap">
                                        {{ \App\Helpers\PriceHelper::format($order->total_amount_paid, $order->currency) }}
                                    </td>
                                    <td class="fw-bold text-danger text-nowrap">
                                        {{ \App\Helpers\PriceHelper::format($order->remaining_debt, $order->currency) }}
                                    </td>
                                    <td>{{ $order->seller->username }}</td>
                                    <td class="col-date">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="order" listRoute="order-item" :id="$order->id" subRoute="items"
                                            :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                            createTitle="Буюртма элементини яратиш"
                                            listTitle="Буюртма элементлнарини кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
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
                            <select name="filters[user_id]"
                                    class="form-control form-control-sm filter-select2 w-100 mb-4" data-placeholder="Клиентни танланг">
                                <option value="">Барчаси</option>
                                @foreach($users as $id => $username)
                                    <option value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                        {{ $username }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($orders as $order)
                            <div class="card border">
                                <div class="card-body">
                                    @if($order->image)
                                        <div class="text-center mb-2">
                                            <img src="{{ asset('storage/' . $order->file->path) }}" alt="Image"
                                                 class="img-fluid" style="max-width: 256px;">
                                        </div>
                                    @endif
                                    <p class="card-text"><strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $order->id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('user_id', 'Клиент:') !!} </strong>{{ $order->user->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('status', 'Статус:') !!} </strong>{{ \App\Models\Order::getStatusList()[$order->status] ?? '-' }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_price', 'Умумий:') !!} </strong> <span
                                            class="fw-bold text-info">
                                            {{ \App\Helpers\PriceHelper::format($order->total_price, $order->currency) }}
                                        </span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_amount_paid', 'Тўланган:') !!} </strong> <span
                                            class="fw-bold text-success">
                                           {{ \App\Helpers\PriceHelper::format($order->total_amount_paid, $order->currency) }}
                                        </span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('remaining_debt', 'Қарздорлик:') !!} </strong> <span
                                            class="fw-bold text-danger">
                                            {{ \App\Helpers\PriceHelper::format($order->remaining_debt, $order->currency) }}
                                        </span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('seller_id', 'Сотувчи:') !!} </strong>{{ $order->seller->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $order->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <x-backend.action
                                        route="order" listRoute="order-item" :id="$order->id" subRoute="items"
                                        :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                        createTitle="Буюртма элементини яратиш" listTitle="Буюртма элементлнарини кўриш"
                                        viewClass="btn btn-secondary btn-sm"
                                    />
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <!-- Count -->
                    <div class="card-stats count">
                        <div class="w-100">
                            <p>Буюртмалар сони:<strong></strong></p>
                            <h5>🇺🇿 {{ number_format($orderCountUzs, 0, '', ' ') }} та</h5>
                            <h5>🇺🇸 {{ number_format($orderCountUsd, 0, '', ' ') }} та</h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="card-stats total">
                        <div class="w-100">
                            <p>Умумий сумма:<strong></strong></p>
                            <h5>{{ number_format($orderTotalPriceUzs, 0, '', ' ') }} сўм</h5>
                            <h5>{{ number_format($orderTotalPriceUsd, 2, '.', ' ') }} $</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div>

                    <!-- Paid -->
                    <div class="card-stats paid">
                        <div class="w-100">
                            <p>Тўланган сумма:<strong></strong></p>
                            <h5>{{ number_format($orderAmountPaidUzs, 0, '', ' ') }} сўм</h5>
                            <h5>{{ number_format($orderAmountPaidUsd, 2, '.', ' ') }} $</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>

                    <!-- Debt -->
                    <div class="card-stats debt">
                        <div class="w-100">
                            <p>Умумий қарздорлик:<strong></strong></p>
                            <h5>{{ number_format($orderRemainingDebtUzs, 0, '', ' ') }} сўм</h5>
                            <h5>{{ number_format($orderRemainingDebtUsd, 2, '.', ' ') }} $</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-pound"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('orderFilterForm').addEventListener('submit', function () {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('select[name="filters[user_id]"]').forEach(select => {
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
