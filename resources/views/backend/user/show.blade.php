@php
    use App\Services\StatusService;
    use App\Models\Order;
    use App\Helpers\PriceHelper;
    use App\Models\ShiftOutputWorker;

    switch ($user->role->title) {
        case 'Client':
            $titlePrefix = 'Клиент';
            break;
        case 'Worker':
        case 'Master':
            $titlePrefix = 'Ходим';
            break;
        default:
            $titlePrefix = 'Фойдаланувчи';
            break;
    }
@endphp

<x-backend.layouts.main title="{{ $titlePrefix . ': ' . ucfirst($user->username) }}">
    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">

            <x-backend.action
                route="user"
                :id="$user->id"
                :back="true"
                :edit="true"
                :delete="true"
                editClass="btn btn-primary sm"
                editLabel="Янгилаш"
                deleteLabel="Ўчириш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Расм</th>
                    <td>
                        @if($user->photo)
                            <img style="width: 300px; object-fit: contain"
                                 src="{{ asset('storage/' . $user->avatar->path) }}" alt="{{ $user->username }}">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Id</th>
                    <td>{{ $user->id }}</td>
                </tr>
                <tr>
                    <th>Фойдаланувчи</th>
                    <td>{{ $user->username }}</td>
                </tr>
                @if($user->telegram_chat_id)
                    <tr>
                        <th>E-mail</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Телефон</th>
                    <td>{{ $user->phone }}</td>
                </tr>
                <tr>
                    <th>Даража</th>
                    <td>{{ $user->role->title }}</td>
                </tr>
                @if($user->telegram_chat_id)
                    <tr>
                        <th>Telegram ID</th>
                        <td class="text-info fw-bold">{{ $user->telegram_chat_id }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Статус</th>
                    <td>{{ StatusService::getList()[$user->status] }}</td>
                </tr>
                <tr>
                    <th>Қарздорлик</th>
                    <td>
                        @forelse($debts as $debt)
                            <div class="text-danger fw-bold">
                                {{ PriceHelper::format($debt['total_amount'], $debt['currency']) }}
                            </div>
                        @empty
                            <span class="text-success fw-bold">0</span>
                        @endforelse
                    </td>
                </tr>
                <tr>
                    <th>Яратилди</th>
                    <td>{{ $user->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th>
                    <td>{{ $user->updated_at }}</td>
                </tr>
                </tbody>
            </table>

            <br>

            {{-- Agar Client bo‘lsa --}}
            @if($user->role->title === 'Client')
                <h3 class="text-center">Буюртмалари:</h3>
                <form id="orderFilterForm" method="GET" action="{{ route('user.show', $user->id) }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th>{!! sortLink('total_price', 'Умумий нарх') !!}</th>
                                <th>{!! sortLink('total_amount_paid', 'Тўланган сумма') !!}</th>
                                <th>{!! sortLink('remaining_debt', 'Қолган қарз') !!}</th>
                                <th>{!! sortLink('seller_id', 'Сотувчи') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th>
                                    <x-backend.export
                                        :model="Order::class"
                                        :where="['id' => $exportOrders]"
                                        :columns="[
                                            'id' => 'ID',
                                            'user.username' => 'Мижоз',
                                            'total_price:price_format' => 'Умумий нарх',
                                            'total_amount_paid:price_format' => 'Тўланган сумма',
                                            'remaining_debt:price_format' => 'Қолган қарз',
                                            'seller.username' => 'Сотувчи',
                                            'created_at:datetime' => 'Яратилди',
                                        ]"
                                        :totals="[
                                            'Буюртмалар сони:' => number_format($orderCountUzs, 0, '', ' ') . ' та (сўм)' . ' • ' . number_format($orderCountUsd, 2, '.', ' ') . ' та ($)',
                                            'Умумий сумма:'     => number_format($orderTotalPriceUzs, 0, '', ' ') . ' сўм' . ' • ' . number_format($orderTotalPriceUsd, 2, '.', ' ') . ' $',
                                            'Тўланган сумма:'    => number_format($orderAmountPaidUzs, 0, '', ' ') . ' сўм' . ' • ' . number_format($orderAmountPaidUsd, 2, '.', ' ') . ' $',
                                            'Умумий қарздорлик:'    => number_format($remainingDebtUzs, 0, '', ' ') . ' сўм' . ' • ' . number_format($remainingDebtUsd, 2, '.', ' ') . ' $',
                                        ]"
                                        :header="[
                                            'title' => 'Rovon textile',
                                            'subtitle' => 'Савдо ҳисоботи',
                                            'date' => now()->format('d.m.Y'),
                                            'logo_left' => 'images/logo-text-.png',
                                            'logo_right' => 'images/logo-text-.png',
                                        ]"
                                    />
                                </th>
                            </tr>
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm"></th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm">
                                        <option value="">Барчаси</option>
                                        @foreach( Order::getStatusList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ request('filters.status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[total_price]"
                                           value="{{ request('filters.total_price') }}"
                                           class="form-control form-control-sm filter-numeric"></th>
                                <th><input type="text" name="filters[total_amount_paid]"
                                           value="{{ request('filters.total_amount_paid') }}"
                                           class="form-control form-control-sm filter-numeric"></th>
                                <th><input type="text" name="filters[remaining_debt]"
                                           value="{{ request('filters.remaining_debt') }}"
                                           class="form-control form-control-sm filter-numeric"></th>
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
                                <th>
                                    <button type="submit" class="btn btn-sm btn-primary w-100"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr class="text-center">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ Order::getStatusList()[$order->status] ?? '-' }}</td>
                                    <td class="fw-bold text-info text-nowrap">
                                        {{ PriceHelper::format($order->total_price, $order->currency) }}
                                    </td>
                                    <td class="fw-bold text-success text-nowrap">
                                        {{ PriceHelper::format($order->total_amount_paid, $order->currency) }}
                                    </td>
                                    <td class="fw-bold text-danger text-nowrap">
                                        {{ PriceHelper::format($order->remaining_debt, $order->currency) }}
                                    </td>
                                    <td>{{ $order->seller->username }}</td>
                                    <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="order"
                                            :id="$order->id"
                                            listRoute="order-item"
                                            subRoute="items"
                                            :add="true"
                                            :list="true"
                                            :view="true"
                                            :edit="true"
                                            createTitle="Буюртма элементини яратиш"
                                            listTitle="Буюртма элементлнарини кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <div class="mt-1">
                                <x-backend.export
                                    :model="Order::class"
                                    :where="['id' => $exportOrders]"
                                    :columns="[
                                            'id' => 'ID',
                                            'user.username' => 'Мижоз',
                                            'total_price:price_format' => 'Умумий нарх',
                                            'total_amount_paid:price_format' => 'Тўланган сумма',
                                            'remaining_debt:price_format' => 'Қолган қарз',
                                            'seller.username' => 'Сотувчи',
                                            'created_at:datetime' => 'Яратилди',
                                        ]"
                                    :totals="[
                                            'Буюртмалар сони:' => number_format($orderCountUzs, 0, '', ' ') . ' та (сўм)' . ' • ' . number_format($orderCountUsd, 2, '.', ' ') . ' та ($)',
                                            'Умумий сумма:'     => number_format($orderTotalPriceUzs, 0, '', ' ') . ' сўм' . ' • ' . number_format($orderTotalPriceUsd, 2, '.', ' ') . ' $',
                                            'Тўланган сумма:'    => number_format($orderAmountPaidUzs, 0, '', ' ') . ' сўм' . ' • ' . number_format($orderAmountPaidUsd, 2, '.', ' ') . ' $',
                                            'Умумий қарздорлик:'    => number_format($remainingDebtUzs, 0, '', ' ') . ' сўм' . ' • ' . number_format($remainingDebtUsd, 2, '.', ' ') . ' $',
                                        ]"
                                    :header="[
                                            'title' => 'Rovon textile',
                                            'subtitle' => 'Савдо ҳисоботи',
                                            'date' => now()->format('d.m.Y'),
                                            'logo_left' => 'images/logo-text-.png',
                                            'logo_right' => 'images/logo-text-.png',
                                        ]"
                                />
                            </div>
                            <input type="date" name="filters[created_from]"
                                   value="{{ request('filters.created_from') }}"
                                   class="form-control form-control-sm me-1" placeholder="From">
                            <input type="date" name="filters[created_to]"
                                   value="{{ request('filters.created_to') }}"
                                   class="form-control form-control-sm" placeholder="To">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($orders as $order)
                            <div class="card border mb-2">
                                <div class="card-body">
                                    <p class="card-text"><strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $order->id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('user_id', 'Клиент:') !!} </strong>{{ $order->user->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('status', 'Статус:') !!} </strong>{{ StatusService::getList()[$order->status] ?? '-' }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_price', 'Умумий сумма:') !!} </strong> <span
                                            class="fw-bold text-info">{{ number_format($order->total_price, 0, '', ' ') }}</span>
                                        сўм
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_amount_paid', 'Тўланган сумма:') !!} </strong> <span
                                            class="fw-bold text-success">{{ number_format($order->total_amount_paid, 0, '', ' ') }}</span>
                                        сўм
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('remaining_debt', 'Қарздорлик:') !!} </strong> <span
                                            class="fw-bold text-danger">
                                            {{ PriceHelper::format($order->remaining_debt, $order->currency) }}
                                        </span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('seller_id', 'Сотувчи:') !!} </strong>{{ $order->seller->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $order->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <x-backend.action
                                        route="order"
                                        :id="$order->id"
                                        listRoute="order-item"
                                        subRoute="items"
                                        :add="true"
                                        :list="true"
                                        :view="true"
                                        :edit="true"
                                        createTitle="Буюртма элементини яратиш"
                                        listTitle="Буюртма элементлнарини кўриш"
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

                <div class="d-flex mt-3 justify-content-center">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>

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
                        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
                    }

                    .card-stats.count {
                        background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%);
                        border-left: 5px solid #00d68f;
                    }

                    .card-stats.total {
                        background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%);
                        border-left: 5px solid #00a8ff;
                    }

                    .card-stats.paid {
                        background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%);
                        border-left: 5px solid #8e76ff;
                    }

                    .card-stats.debt {
                        background: linear-gradient(135deg, #fd79a8 30%, #e84393 90%);
                        border-left: 5px solid #ff6b81;
                    }

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
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <!-- Count -->
                    <div class="card-stats count">
                        <div class="w-100">
                            <p>Буюртмалар сони:</p>
                            <h5>
                                {{ number_format($orderCountUzs, 0, '', ' ') }} та<br>
                                {{ number_format($orderCountUsd, 0, '', ' ') }} та
                            </h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="card-stats total">
                        <div class="w-100">
                            <p>Умумий сумма:</p>
                            <h5>
                                {{ number_format($orderTotalPriceUzs, 0, '', ' ') }} сўм<br>
                                {{ number_format($orderTotalPriceUsd, 2, '.', ' ') }} $
                            </h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div>

                    <!-- Paid -->
                    <div class="card-stats paid">
                        <div class="w-100">
                            <p>Тўланган сумма:</p>
                            <h5>
                                {{ number_format($orderAmountPaidUzs, 0, '', ' ') }} сўм<br>
                                {{ number_format($orderAmountPaidUsd, 2, '.', ' ') }} $
                            </h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>

                    <!-- Debt -->
                    <div class="card-stats debt">
                        <div class="w-100">
                            <p>Умумий қарздорлик:</p>
                            <h5>
                                {{ number_format($remainingDebtUzs, 0, '', ' ') }} сўм<br>
                                {{ number_format($remainingDebtUsd, 2, '.', ' ') }} $
                            </h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-pound"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Agar Worker bo‘lsa --}}
            @if($user->role->title === 'Worker')
                <form id="workerFilterForm" method="GET" action="{{ route('user.show', $user->id) }}">
                    <h3 class="text-center">Иш натижалари:</h3>

                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('shift_output_id', 'Смена') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('stage_count', 'Микдори') !!}</th>
                                <th>{!! sortLink('defect_amount', 'Брак') !!}</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th>
                                    <x-backend.export
                                        :model="ShiftOutputWorker::class"
                                        :where="['id' => $exportShiftOutputWorkers]"
                                        :columns="[
                                            'id' => 'ID',
                                            'user.username' => 'Ходим',
                                            'shiftOutput.shift.title' => 'Смена',
                                            'shiftOutput.stage.title' => 'Маҳсулот',
                                            'stage_count' => 'Миқдори',
                                            'defect_amount' => 'Брак',
                                            'price' => 'Нархи',
                                            'created_at:datetime' => 'Яратилди',
                                        ]"
                                        :totals="[
                                            'Жами маҳсулот:' => number_format($totalStageCount, 0, '', ' ') . ' та',
                                            'Жами брак:'     => number_format($totalDefectAmount, 3, '.', ' ') . ' кг',
                                            'Жами сумма:'    => number_format($totalPrice, 0, '', ' ') . ' сўм',
                                        ]"
                                        :header="[
                                            'title' => 'Rovon textile',
                                            'subtitle' => 'Смена ҳисоботи',
                                            'date' => now()->format('d.m.Y'),
                                            'logo_left' => 'images/logo-text-.png',
                                            'logo_right' => 'images/logo-text-.png',
                                        ]"
                                    />
                                </th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[shift_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($shifts as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.shift_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[stage_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($stages as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[stage_count]"
                                           value="{{ request('filters.stage_count') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th><input type="text" name="filters[defect_amount]"
                                           value="{{ request('filters.defect_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
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
                            @forelse($shiftOutputWorkers as $s)
                                <tr class="text-center">
                                    <td>{{ $s->id }}</td>
                                    <td>{{ $s->shiftOutput->shift->title ?? '-' }}</td>
                                    <td>{{ $s->shiftOutput->stage->title ?? '-' }}</td>
                                    <td class="fw-bold text-success">{{ number_format($s->stage_count, 0, '', ' ') }}</td>
                                    <td class="fw-bold text-danger">{{ number_format($s->defect_amount, 3, '.', ' ') }}</td>
                                    <td class="fw-bold text-info">{{ number_format($s->price, 0, '', ' ') }}</td>
                                    <td>{{ $s->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="shift-output-worker" :id="$s->id" :view="true"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <div class="mt-1">
                                <x-backend.export
                                    :model="ShiftOutputWorker::class"
                                    :where="['id' => $exportShiftOutputWorkers]"
                                    :columns="[
                                      'id' => 'ID',
                                      'user.username' => 'Ходим',
                                      'shiftOutput.shift.title' => 'Смена',
                                      'shiftOutput.stage.title' => 'Маҳсулот',
                                      'stage_count' => 'Миқдори',
                                      'defect_amount' => 'Брак',
                                      'price' => 'Нархи',
                                      'created_at:datetime' => 'Яратилди',
                                  ]"
                                    :totals="[
                                      'Жами маҳсулот:' => number_format($totalStageCount, 0, '', ' ') . ' та',
                                      'Жами брак:'     => number_format($totalDefectAmount, 3, '.', ' ') . ' кг',
                                      'Жами сумма:'    => number_format($totalPrice, 0, '', ' ') . ' сўм',
                                  ]"
                                    :header="[
                                      'title' => config('app.name'),
                                      'subtitle' => 'Смена ҳисоботи',
                                      'date' => now()->format('d.m.Y'),
                                      'logo_left' => 'images/logo-text-.png',
                                      'logo_right' => 'images/logo-text-.png',
                                  ]"
                                />
                            </div>
                            <select name="filters[stage_id]"
                                    class="form-control form-control-sm w-100">
                                <option value="">Барчаси</option>
                                @foreach($stages as $id => $title)
                                    <option
                                        value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($shiftOutputWorkers as $s)
                            <div class="card border mb-2">
                                <div class="card-body">
                                    <p><strong>ID:</strong> {{ $s->id }}</p>
                                    <p><strong>Смена:</strong> {{ $s->shiftOutput->shift->title ?? '-' }}</p>
                                    <p><strong>Махсулот:</strong> {{ $s->shiftOutput->stage->title ?? '-' }}</p>
                                    <p><strong>Миқдор:</strong> <span
                                            class="fw-bold text-success">{{ number_format($s->stage_count, 0, '', ' ') }}</span>
                                        та</p>
                                    <p><strong>Брак:</strong> <span
                                            class="fw-bold text-danger">{{ number_format($s->defect_amount, 3, '.', ' ') }}</span>
                                        кг</p>
                                    <p><strong>Нарх:</strong> <span
                                            class="fw-bold text-info">{{ number_format($s->price, 0, '', ' ') }}</span>
                                        сўм</p>
                                    <p><strong>Яратилди:</strong> {{ $s->created_at?->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                <div class="d-flex mt-3 justify-content-center">
                    {{ $shiftOutputWorkers->links('pagination::bootstrap-4') }}
                </div>

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
                        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
                    }

                    .card-stats.product {
                        background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%);
                        border-left: 5px solid #00d68f;
                    }

                    .card-stats.defect {
                        background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%);
                        border-left: 5px solid #00a8ff;
                    }

                    .card-stats.total {
                        background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%);
                        border-left: 5px solid #8e76ff;
                    }

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
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <!-- Product -->
                    <div class="card-stats product">
                        <div class="w-100">
                            <p>Жами маҳсулот:</p>
                            <h5>{{ number_format($totalStageCount, 0, '', ' ') }} та</h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <!-- Defect -->
                    <div class="card-stats defect">
                        <div class="w-100">
                            <p>Жами брак:</p>
                            <h5>{{ number_format($totalDefectAmount, 3, '.', ' ') }} кг</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="card-stats total">
                        <div class="w-100">
                            <p>Жами сумма:</p>
                            <h5>{{ number_format($totalPrice, 0, '', ' ') }} сўм</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('orderFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            const form = this;
            form.querySelectorAll('input[name^="filters["], select[name^="filters["]').forEach(el => {
                if (el.offsetParent === null) { // DOMda mavjud lekin ko‘rinmaydi
                    el.disabled = true;
                } else {
                    el.disabled = false;
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

    <script>
        document.getElementById('workerFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('input[name="filters[stage_id]"], select[name="filters[stage_id]"]').forEach(select => {
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
