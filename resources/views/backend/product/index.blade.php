<x-backend.layouts.main title="{{ 'Махсулот турлари' }}">

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
                        <x-backend.action route="product" :back="true" :create="true"/>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="productFilterForm" method="GET" action="{{ route('product.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('warehouse_id', 'Омбор') !!}</th>
                                <th class="col-title">{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('image', 'Расм') !!}</th>
                                <th>{!! sortLink('category_id', 'Категория') !!}</th>
                                <th>{!! sortLink('user_id', 'Ҳодим') !!}</th>
                                {{-- <th>{!! sortLink('type', 'Тури') !!}</th>--}}
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[warehouse_id]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($warehouses as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.warehouse_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th><input type="text" name="filters[image]" value="{{ request('filters.image') }}"
                                           class="form-control form-control-sm w-100" style="display: none;"></th>
                               <th>
                                   <select name="filters[category_id]"
                                           class="form-control form-control-sm w-100">
                                       <option value="">Барчаси</option>
                                       @foreach($categories as $id => $title)
                                           <option
                                               value="{{ $id }}" {{ request('filters.category_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
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
                                {{-- <th><input type="text" name="filters[type]" value="{{ request('filters.type') }}" class="form-control form-control-sm w-100"></th>--}}
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Services\StatusService::getList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.status') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                            @forelse($products as $product)
                                <tr class="text-center" id="row-{{ $product->id }}">
                                    <td class="col-id">{{ $product->id }}</td>
                                    <td>{{ optional($product->warehouse)->title }}</td>
                                    <td class="col-title">{{ $product->title }}</td>
                                    <td>
                                        @if(optional($product->file)->path)
                                            <img src="{{ asset('storage/' . $product->file->path) }}" alt="Image"
                                                 style="width: 50px; height: auto;">
                                        @endif
                                    </td>
                                    <td>{{ optional($product->category)->title }}</td>
                                    <td>{{ optional($product->user)->username }}</td>
                                    {{--                                <td>{{ $product->type }}</td>--}}
                                    <td style="width: 100px">{{ \App\Services\StatusService::getList()[$product->status] ?? '-' }}</td>
                                    <td class="col-date">{{ $product->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="product" listRoute="product-variation" :id="$product->id"
                                            subRoute="variations"
                                            :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                            createTitle="Маҳсулот яратиш"
                                            listTitle="Маҳсулотларни кўриш"
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
                                   class="form-control form-control-sm me-1" placeholder="Маҳсулот номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($products as $product)
                            <div class="card border">
                                <div class="card-body">
                                    @if(optional($product->file)->path)
                                        <div class="text-center mb-2">
                                            <img src="{{ asset('storage/' . $product->file->path) }}" alt="Image"
                                                 class="img-fluid" style="max-width: 256px;">
                                        </div>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $product->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('warehouse_id', 'Омбор:') !!} </strong>{{ optional($product->warehouse)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!} </strong>{{ $product->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('category_id', 'Категория') !!} </strong>{{ optional($product->category)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('status', 'Статус:') !!} </strong> {{ \App\Services\StatusService::getList()[$product->status] ?? '-' }}
                                    </p>

                                    <x-backend.action
                                        route="product" listRoute="product-variation" :id="$product->id"
                                        subRoute="variations"
                                        :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                        createTitle="Маҳсулот яратиш"
                                        listTitle="Маҳсулотларни кўриш"
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

                <div class="d-flex justify-content-center">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 mb-3">
                        <div class="card-stats count">
                            <div class="w-100">
                                <p>Махсулот тури</p>
                                <h5><strong>{{ number_format($productCount, 0, '', ' ') }} та</strong></h5>
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
        document.getElementById('productFilterForm').addEventListener('submit', function (e) {
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
