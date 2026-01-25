<x-backend.layouts.main title="{{ 'Буюртма элементлари' }}">

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
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }

        .card-stats.count {
            background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
            border-left: 5px solid #00d68f;
        }

        .card-stats.total {
            background: linear-gradient(135deg, #0984e3 35%, #0984e3 65%);
            border-left: 5px solid #00a8ff;
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

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <x-backend.action :back="true"/>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="orderItemFilterForm" method="GET" action="{{ route('order-item.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th class="col-title">{!! sortLink('order_id', 'Буюртмачи') !!}</th>
                                <th>{!! sortLink('product_variation_id', 'Маҳсулот') !!}</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th>{!! sortLink('quantity', 'Сони') !!}</th>
                                <th>{!! sortLink('total_price', 'Умумий') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[order_id]"
                                            class="form-control form-control-sm filter-select2 me-2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.order_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[product_variation_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($productVariations as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.product_variation_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[quantity]"
                                           value="{{ request('filters.quantity') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[total_price]"
                                           value="{{ request('filters.total_price') }}"
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
                            @forelse($orderItems as $orderItem)
                                <tr class="text-center" id="row-{{ $orderItem->id }}">
                                    <td class="col-id">{{ $orderItem->id }}</td>
                                    <td class="col-title">{{ $orderItem->order->user->username }}</td>
                                    <td class="col-title">{{ $orderItem->productVariation->title }}</td>
                                    <td class="fw-bold text-success">{{ \App\Helpers\PriceHelper::format($orderItem->price, $orderItem->order->currency) }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($orderItem->quantity, 0, '', ' ') }}
                                        та
                                    </td>
                                    <td class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($orderItem->total_price, $orderItem->order->currency) }}</td>
                                    <td class="col-date">{{ $orderItem->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="order-item" :id="$orderItem->id" :view="true"
                                                          :delete="true"/>
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
                            <select name="filters[order_id]"
                                    class="form-control form-control-sm filter-select2 me-2 w-100"
                                    data-placeholder="Клиентни танланг">
                                <option value="">Барчаси</option>
                                @foreach($users as $id => $username)
                                    <option
                                        value="{{ $id }}" {{ request('filters.order_id') == $id ? 'selected' : '' }}>
                                        {{ $username }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($orderItems as $orderItem)
                            <div class="card border">
                                <div class="card-body">
                                    @if($orderItem->image)
                                        <div class="text-center mb-2">
                                            <img src="{{ asset('storage/' . $orderItem->file->path) }}" alt="Image"
                                                 class="img-fluid" style="max-width: 256px;">
                                        </div>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $orderItem->id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('user_id', 'Клиент:') !!} </strong>{{ $orderItem->order->user->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('product_variation_id', 'Маҳсулот:') !!} </strong>{{ $orderItem->productVariation->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('price', 'Нархи:') !!} </strong> <span
                                            class="fw-bold text-success">{{ \App\Helpers\PriceHelper::format($orderItem->price, $orderItem->order->currency) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('quantity', 'Сони:') !!} </strong> <span
                                            class="fw-bold text-primary">{{ number_format($orderItem->quantity, 0, '', ' ') }}   та</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_price', 'Умумий сумма:') !!} </strong> <span
                                            class="fw-bold text-info">{{ \App\Helpers\PriceHelper::format($orderItem->total_price, $orderItem->order->currency) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди(сана):') !!} </strong> {{ $orderItem->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="order-item" :id="$orderItem->id" :view="true"
                                                          :delete="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                <div class="d-flex justify-content-center">
                    {{ $orderItems->links('pagination::bootstrap-4') }}
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card-stats count">
                            <div class="w-100">
                                <p>Буюртма элементи сони:</p>
                                <h5>
                                    @foreach ($orderItemCount as $currency => $count)
                                        {{ $count }} та <br>
                                    @endforeach
                                </h5>
                            </div>
                            <div>
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card-stats total">
                            <div class="w-100">
                                <p>Умумий нарх:</p>
                                <h5>
                                    @foreach ($orderItemPrice as $currency => $amount)
                                        {{ \App\Helpers\PriceHelper::format($amount, $currency) }} <br>
                                    @endforeach
                                </h5>
                            </div>
                            <div>
                                <i class="bi bi-currency-exchange"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('orderItemFilterForm').addEventListener('submit', function () {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('select[name="filters[order_id]"]').forEach(select => {
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
