<x-backend.layouts.main title="{{ 'Қайтарилган махсулотлар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        @if(!$todayReport || $todayReport->isClose())
                            <x-backend.action route="cash-report" :report="true" :todayReport="$todayReport"/>
                        @elseif($todayReport->isOpen())
                            <x-backend.action route="product-return" :back="true" :create="true" createLabel="Қайтариш"/>
                        @endif
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="returnFilterForm" method="GET" action="{{ route('product-return.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('expense_id', 'Харажат номи') !!}</th>
                                <th>{!! sortLink('total_amount', 'Микдори') !!}</th>
                                <th>{!! sortLink('user_id', 'Ҳодим') !!}</th>
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
                                    <select name="filters[title]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($expenses as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.title') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <input type="text" name="filters[total_amount]" value="{{ request('filters.total_amount') }}"
                                           class="form-control form-control-sm w-100">
                                </th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
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
                            @forelse($returns as $return)
                                <tr class="text-center" id="row-{{ $return->id }}">
                                    <td class="col-id">{{ $return->id }}</td>
                                    <td>{{ $return->title }}</td>
                                    <td>{{ optional($return->expense)->title }}</td>
                                    <td>{{ \App\Helpers\PriceHelper::format($return->total_amount, $return->currency, false) }}</td>
                                    <td>{{ optional($return->user)->username }}</td>
                                    <td class="col-date">{{ $return->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="product-return" listRoute="product-return-item" :id="$return->id"
                                            subRoute="items"
                                            :list="true" :view="true" :edit="true" :delete="true"
                                            listTitle="Қайтиш маҳсулотларни кўриш"
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
                            <input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                   class="form-control form-control-sm me-1" placeholder="Қайтиш номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($returns as $return)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text ">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $return->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!} </strong>{{ $return->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_amount', 'Микдори :') !!} </strong>{{ \App\Helpers\PriceHelper::format($return->total_amount, $return->currency) }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $return->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action
                                            route="product-return" listRoute="product-return-item" :id="$return->id"
                                            subRoute="items"
                                            :list="true" :view="true" :edit="true" :delete="true"
                                            listTitle="Қайтиш маҳсулотларни кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </div>
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
                    {{ $returns->links('pagination::bootstrap-4') }}
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
                    }
                    .card-stats:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 12px 24px rgba(0,0,0,0.3);
                    }
                    .card-stats.count {
                        background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
                        border-left: 5px solid #00d68f;
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
                <div class="row mt-4">
                    <div class="col-md-12 mb-3">
                        <div class="card-stats count">
                            <div class="w-100">
                                <p>Қайтишлар</p>
                                <h5><strong>{{ number_format($productReturnCount, 0, '', ' ') }} та</strong></h5>
                            </div>
                            <div>
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('returnFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[title]"]').forEach(select => {
                if (select.offsetParent === null) {
                    select.disabled = true;
                }
            });

            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value.trim()) {
                    input.remove();
                }
            });
        });
    </script>

</x-backend.layouts.main>
