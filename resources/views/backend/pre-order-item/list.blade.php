<x-backend.layouts.main title="{{ 'Навбатдаги буюртма ( ' . $order->user->username . ' ) тури:' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="preOrderItemListFilterForm" method="GET" action="{{ route('pre-order-item.list', $order) }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('code', 'Код') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('count', 'Сони') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm"></th>
                                <th><input type="text" name="filters[code]" value="{{ request('filters.code') }}"
                                           class="form-control form-control-sm"></th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                           class="form-control form-control-sm"></th>
                                <th><input type="text" name="filters[count]" value="{{ request('filters.count') }}"
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
                            @forelse($orderItems as $item)
                                <tr class="text-center" id="row-{{ $item->id }}">
                                    <td class="col-id">{{ $item->id }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($item->count, 0, '', ' ') }} та</td>
                                    <td class="col-date">{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="pre-order-item" :id="$item->id" :view="true" :delete="true"/>
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
                            <input type="text" name="filters[product_variation_title]" value="{{ request('filters.product_variation_title') }}"
                                   class="form-control form-control-sm me-1" placeholder="Маҳсулот номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($orderItems as $item)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text"><strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $item->id }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('code', 'Код:') !!} </strong>{{ $item->code }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!} </strong>{{ $item->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('count', 'Сони:') !!} </strong> <span
                                            class="fw-bold text-primary">{{ number_format($item->count, 0, '', ' ') }} та</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!} </strong> {{ $item->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="pre-order-item" :id="$item->id" :view="true" :delete="true"/>
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
                   <div class="col-md-6 mb-3">
                       <div class="card-stats count">
                           <div class="w-100">
                               <p>Буюртма элементи сони:</p>
                               <h5>{{ number_format($orderCount, 0, '', ' ') }} та</h5>
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
        document.getElementById('preOrderItemListFilterForm').addEventListener('submit', function(e) {
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
