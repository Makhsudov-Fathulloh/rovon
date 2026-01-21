@php
    use App\Helpers\CountHelper;
@endphp

<x-backend.layouts.main title="{{ 'Махсулотлар' }}">

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
                <form id="productLogFilterForm" method="GET" action="{{ route('log.product') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('code', 'Код') !!}</th>
                                <th>{!! sortLink('product_variation_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('old_count', 'Олдинги') !!}</th>
                                <th>{!! sortLink('added_count', 'Қўшилди ') !!}</th>
                                <th>{!! sortLink('new_count', 'Жами') !!}</th>
                                <th>{!! sortLink('user_id', 'Ҳодим') !!}</th>
                                <th class="col-date">{!! sortLink('created_at_exact', 'Яратилди(сана)') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[code]" value="{{ request('filters.code') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th>
                                    <select name="filters[product_variation_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($products as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.product_variation_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[old_count]" value="{{ request('filters.old_count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[added_count]" value="{{ request('filters.added_count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[new_count]" value="{{ request('filters.new_count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
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
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                               @forelse($productLogs as $product)
                                <tr class="text-center" id="row-{{ $product->id }}">
                                    <td class="col-id">{{ $product->id }}</td>
                                    <td>{{ optional($product->productVariation)->code }}</td>
                                    <td>{{ optional($product->productVariation)->title }}</td>
                                    <td class="fw-bold text-primary">{{ CountHelper::format($product->old_count, $product->productVariation->unit) }}</td>
                                    <td class="fw-bold text-success">{{ CountHelper::format($product->added_count, $product->productVariation->unit) }}</td>
                                    <td class="fw-bold text-info">{{ CountHelper::format($product->new_count, $product->productVariation->unit) }}</td>
                                    <td>{{ optional($product->user)->username }}</td>
                                    <td class="col-date">{{ $product->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="product-variation" :id="optional($product->productVariation)->id" :view="true"/>
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
                            <select name="filters[product_variation_id]" class="form-control form-control-sm filter-select2 w-100">
                                <option value="">Барчаси</option>
                                @foreach($products as $id => $title)
                                    <option
                                        value="{{ $id }}" {{ request('filters.product_variation_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                         @forelse($productLogs as $product)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text ">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $product->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('code', 'Код:') !!} </strong>{{ optional($product->productVariation)->code }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('product_variation_id', 'Хомашё:') !!} </strong>{{ optional($product->productVariation)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('old_count', 'Олдинги') !!} </strong><span
                                            class="count fw-bold text-primary">{{ CountHelper::format($product->old_count, $product->productVariation->unit) }}</span>
                                    </p>
                                     <p class="card-text">
                                        <strong>{!! sortLink('added_count', 'Қўшилди') !!} </strong><span
                                            class="count fw-bold text-success">{{ CountHelper::format($product->added_count, $product->productVariation->unit) }}</span>
                                    </p>
                                     <p class="card-text">
                                        <strong>{!! sortLink('new_count', 'Жами') !!} </strong><span
                                            class="count fw-bold text-info">{{ CountHelper::format($product->new_count, $product->productVariation->unit) }}</span>
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="product-variation" :id="optional($product->productVariation)->id" :view="true"/>
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
                    {{ $productLogs->links('pagination::bootstrap-4') }}
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 mb-3">
                        <div class="card-stats count">
                            <div class="w-100">
                                <p>Қатегориялар</p>
                                <h5><strong>{{ number_format($productLogCount, 0, '', ' ') }} та</strong></h5>
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
        document.getElementById('productLogFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
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
