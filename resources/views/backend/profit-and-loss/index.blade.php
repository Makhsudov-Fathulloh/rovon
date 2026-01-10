@php
    use App\Models\ProfitAndLoss;
@endphp

<x-backend.layouts.main title="{{ 'Фойда ва зарар савдолар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="profitAndLossFilterForm" method="GET" action="{{ route('profit-and-loss.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('order_item_id', 'Буюртма элементи') !!}</th>
                                <th>{!! sortLink('product_variation_id', 'Маҳсулот') !!}</th>
                                <th>{!! sortLink('original_price', 'Асл нарх(сўм) ') !!}</th>
                                <th>{!! sortLink('sold_price', 'Сотилган нарх(сўм) ') !!}</th>
                                <th>{!! sortLink('profit_amount', 'Фойда(сўм) ') !!}</th>
                                <th>{!! sortLink('loss_amount', 'Зарар(сўм) ') !!}</th>
                                <th>{!! sortLink('count', 'Миқдори') !!}</th>
                                <th>{!! sortLink('type', 'Тури') !!}</th>
                                <th>{!! sortLink('total_amount', 'Умумий(сўм) ') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[order_item_id]" value="{{ request('filters.order_item_id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[product_variation_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($productVariations as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.product_variation_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[original_price]"
                                           value="{{ request('filters.original_price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[sold_price]"
                                           value="{{ request('filters.sold_price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[profit_amount]"
                                           value="{{ request('filters.profit_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[loss_amount]"
                                           value="{{ request('filters.loss_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[count]" value="{{ request('filters.count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[type]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(ProfitAndLoss::getTypeList() as $key => $label)
                                        {{--<option value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>--}}
                                            <option value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>
                                                {{ $label }} {{ $key == ProfitAndLoss::TYPE_PROFIT ? 'Фойда' : 'Зарар' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[total_amount]"
                                           value="{{ request('filters.total_amount') }}"
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
                            @forelse($profitAndLosses as $profitAndLoss)
                                <tr class="text-center" id="row-{{ $profitAndLoss->id }}">
                                    <td class="col-id">{{ $profitAndLoss->id }}</td>
                                    <td>{{ $profitAndLoss->order_item_id }}</td>
                                    <td>{{ $profitAndLoss->orderItem->productVariation->title }}</td>
                                    <td>{{ number_format($profitAndLoss->original_price, 0, '', ' ') }}</td>
                                    <td>{{ number_format($profitAndLoss->sold_price, 0, '', ' ') }}</td>
                                    <td class="text-success fw-bold">{{ number_format($profitAndLoss->profit_amount, 0, '', ' ') }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($profitAndLoss->loss_amount, 0, '', ' ') }}</td>
                                    <td>{{ number_format($profitAndLoss->count, 0, '', ' ') }}</td>
                                    <td style="text-align: center; width: 100px">{{ ProfitAndLoss::getTypeList()[$profitAndLoss->type] }}</td>
                                    <td>{{ number_format($profitAndLoss->total_amount, 0, '', ' ') }}</td>
                                    <td>{{ $profitAndLoss->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="profit-and-loss" :id="$profitAndLoss->id" :view="true"/>
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
                            <select name="filters[type]" class="form-control form-control-sm w-100">
                                <option value="">Барчаси</option>
                                @foreach(ProfitAndLoss::getTypeList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($profitAndLosses as $profitAndLoss)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $profitAndLoss->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('order_item_id', 'Буюртма элементи:') !!} </strong>{{ $profitAndLoss->order_item_id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('product_variation_id', 'Маҳсулот:') !!} </strong>{{ $profitAndLoss->variation->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('original_price', 'Асл нарх:') !!} </strong>{{ number_format($profitAndLoss->original_price , 0, '', ' ')}} сўм
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('sold_price', 'Сотилган нарх:') !!} </strong>{{ number_format($profitAndLoss->sold_price, 0, '', ' ') }} сўм
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('profit_amount', 'Фойда:') !!} </strong><strong
                                            style="color: green">{{ number_format($profitAndLoss->profit_amount, 0, '', ' ') }}</strong> сўм</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('loss_amount', 'Зарар:') !!} </strong><strong
                                            style="color: red">{{ number_format($profitAndLoss->loss_amount, 0, '', ' ') }}</strong> сўм</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('count', 'Миқдори:') !!} </strong>{{ number_format($profitAndLoss->count, 0, '', ' ') }} та
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('type', 'Тури:') !!} </strong>{{ ProfitAndLoss::getTypeList()[$profitAndLoss->type] }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_amount', 'Умумий:') !!} </strong>{{ number_format($profitAndLoss->total_amount, 0, '', ' ') }} сўм
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $profitAndLoss->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="profit-and-loss" :id="$profitAndLoss->id" :view="true"/>
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
                <div class="d-flex mt-3 justify-content-center">
                    {{ $profitAndLosses->links('pagination::bootstrap-4') }}
                </div>

                <div class="text-center mt-3">
                    @include('partials.backend._profit_and_loss', ['title' => 'Фойда', 'data' => $data, 'type' => 'profit'])
                    @include('partials.backend._profit_and_loss', ['title' => 'Зарар', 'data' => $data, 'type' => 'loss'])
                    @include('partials.backend._profit_and_loss', ['title' => 'Соф фойда', 'data' => $data, 'type' => 'net'])
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('profitAndLossFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('select[name="filters[type]"]').forEach(select => {
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
