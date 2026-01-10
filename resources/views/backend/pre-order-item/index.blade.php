<x-backend.layouts.main title="{{ 'Навбатдаги буюртма элементлари' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="preOrderItemFilterForm" method="GET" action="{{ route('pre-order-item.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('pre_order_id', 'Буюртма') !!}</th>
                                <th>{!! sortLink('product_variation_id', 'Маҳсулот') !!}</th>
                                <th>{!! sortLink('code', 'Код') !!}</th>
                                <th>{!! sortLink('count', 'Сони') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[pre_order_id]"
                                            class="form-control form-control-sm filter-select2 me-2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.pre_order_id') == $id ? 'selected' : '' }}>
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
                                <th><input type="text" name="filters[code]" value="{{ request('filters.code') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th><input type="text" name="filters[count]"
                                           value="{{ request('filters.count') }}"
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
                                    <td class="col-title">{{ $orderItem->preOrder->user->username }}</td>
                                    <td class="col-title">{{ $orderItem->productVariation->title }}</td>
                                    <td>{{ $orderItem->code }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($orderItem->count, 0, '', ' ') }}
                                        та
                                    </td>
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
                            <select name="filters[pre_order_id]"
                                    class="form-control form-control-sm filter-select2 me-2 w-100"
                                    data-placeholder="Клиентни танланг">
                                <option value="">Барчаси</option>
                                @foreach($users as $id => $username)
                                    <option
                                        value="{{ $id }}" {{ request('filters.pre_order_id') == $id ? 'selected' : '' }}>
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
                                            <img
                                                src="{{ asset('storage/' . $orderItem->$productVariation->file->path) }}"
                                                alt="Image"
                                                class="img-fluid" style="max-width: 256px;">
                                        </div>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $orderItem->id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('user_id', 'Клиент:') !!} </strong>{{ $orderItem->preOrder->user->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('product_variation_id', 'Маҳсулот:') !!} </strong>{{ $orderItem->productVariation->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('count', 'Сони:') !!} </strong> <span
                                            class="fw-bold text-primary">{{ number_format($orderItem->count, 0, '', ' ') }}   та</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $orderItem->created_at?->format('Y-m-d H:i') }}
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

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $orderItems->links('pagination::bootstrap-4') }}
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
                        box-shadow: 0 12px 24px rgba(0,0,0,0.3);
                    }

                    .card-stats.new { background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%); border-left: 5px solid #00d68f; }
                    .card-stats.inprogress { background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%); border-left: 5px solid #00a8ff; }
                    .card-stats.done { background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%); border-left: 5px solid #8e76ff; }
                    .card-stats.cenceled { background: linear-gradient(135deg, #fd79a8 30%, #e84393 90%); border-left: 5px solid #ff6b81; }

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
                    <!-- New -->
                    <div class="card-stats new">
                        <div class="w-100">
                            <p>Янги буюртма элеменлари сони:</p>
                            <h5>{{ $newCount }} та </h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <!-- Inprogress -->
                    <div class="card-stats inprogress">
                        <div class="w-100">
                          <p>Жараёндаги буюртма элеменлари сони:</p>
                          <h5>{{ $inProgressCount }} та </h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div>

                    <!-- Done -->
                    <div class="card-stats done">
                        <div class="w-100">
                          <p>Таййор буюртма элеменлари сони:</p>
                          <h5>{{ $doneCount }} та </h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>

                    <!-- Canceled -->
                    <div class="card-stats cenceled">
                        <div class="w-100">
                          <p>Бекор килинган буюртма элеменлари сони:</p>
                          <h5>{{ $canceledCount }} та </h5>
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
        document.getElementById('preOrderItemFilterForm').addEventListener('submit', function () {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('input[name="filters[pre_order_id]"], select[name="filters[pre_order_id]"]').forEach(select => {
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
