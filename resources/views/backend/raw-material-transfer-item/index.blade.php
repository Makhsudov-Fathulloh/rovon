@php
    use App\Helpers\CountHelper;
    use App\Helpers\PriceHelper;
@endphp

<x-backend.layouts.main title="{{ 'Хомашё трансфер элементлари' }}">

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
        .card-stats.uzs {
            background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
            border-left: 5px solid #00d68f;
        }

        .card-stats.usd {
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
                <form id="rawMaterialTransferItemFilterForm" method="GET"
                      action="{{ route('raw-material-transfer-item.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('raw_material_transfer_id', 'Трансфер') !!}</th>
                                <th>{!! sortLink('raw_material_variation_id', 'Хомашё') !!}</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th>{!! sortLink('count', 'Микдори') !!}</th>
                                <th>{!! sortLink('total_price', 'Умумий') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs--}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[raw_material_transfer_id]"
                                            class="form-control form-control-sm w-100 filter-select2">
                                        <option value="">Барчаси</option>
                                        @foreach($rawMaterialTransfer as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.raw_material_transfer_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[raw_material_variation_id]"
                                            class="form-control form-control-sm w-100 filter-select2">
                                        <option value="">Барчаси</option>
                                        @foreach($rawMaterialVariation as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.raw_material_variation_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[count]" value="{{ request('filters.count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal">
                                </th>
                                <th>
                                  <input type="text" name="filters[total_price]"
                                           value="{{ request('filters.total_price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
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
                            @forelse($transferItems as $transferItem)
                                <tr class="text-center" id="row-desktop-{{ $transferItem->id }}">
                                    <td class="col-id">{{ $transferItem->id }}</td>
                                    <td class="col-title">{{ optional($transferItem->rawMaterialTransfer)->title }}</td>
                                    <td class="col-title">{{ optional($transferItem->rawMaterialVariation)->title }}</td>
                                    <td class="price fw-bold text-success tex">
                                        {{ PriceHelper::format($transferItem->price, $transferItem->rawMaterialVariation->currency) }}
                                    </td>
                                    <td class="count fw-bold text-primary">
                                        {{ CountHelper::format($transferItem->count, $transferItem->unit) }}
                                    </td>
                                    <td class="total_price fw-bold text-info text-nowrap">
                                        {{ PriceHelper::format($transferItem->total_price, $transferItem->rawMaterialVariation->currency) }}
                                    </td>
                                    <td>{{ $transferItem->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('raw-material-transfer-item.show', $transferItem->id) }}"
                                           class="btn btn-info btn-sm" title="Кўриш">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('raw-material-transfer.edit', $transferItem->rawMaterialTransfer->id) }}"
                                           class="btn btn-warning btn-sm" title="Таҳрирлаш"> <i class="fa fa-edit"></i>
                                        </a>
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
                            <select name="filters[raw_material_transfer_id]"
                                    class="form-control form-control-sm w-100 filter-select2" data-placeholder="Трансфер номини киритинг">
                                <option value="">Трансфер номини киритинг)</option>
                                @foreach($rawMaterialTransfer as $id => $title)
                                    <option
                                        value="{{ $id }}" {{ request('filters.raw_material_transfer_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($transferItems as $transferItem)
                            <div class="card border" id="row-mobile-{{ $transferItem->id }}">
                                <div class="card-body">
                                    <div class="text-center mb-2">
                                        @if(optional($transferItem->file)->path)
                                            <img src="{{ asset('storage/' . $transferItem->file->path) }}"
                                                 alt="Image"
                                                 style="width: 50px; height: auto;">
                                        @endif
                                    </div>
                                    <p class="card-text ">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $transferItem->id }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('raw_material_transfer_id', 'Трансфер:') !!} </strong>{{ optional($transferItem->rawMaterialTransfer)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('raw_material_variation_id', 'Хомашё:') !!} </strong>{{ optional($transferItem->rawMaterialVariation)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('price', 'Нархи:') !!}</strong>
                                        <span
                                            class="price fw-bold text-success">{{ PriceHelper::format($transferItem->price, $transferItem->rawMaterialVariation->currency) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('count', 'Сони:') !!}</strong>
                                        <span
                                            class="count fw-bold text-primary">{{ CountHelper::format($transferItem->count, $transferItem->unit) }}</span>
                                    </p>

                                    <div class="btn-group w-100">
                                        <a href="{{ route('raw-material-transfer-item.show', $transferItem->id) }}"
                                           class="btn btn-info btn-sm" title="Кўриш">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('raw-material-transfer.edit', $transferItem->rawMaterialTransfer->id) }}"
                                           class="btn btn-warning btn-sm" title="Таҳрирлаш"> <i class="fa fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                <div class="d-flex justify-content-center mt-3">
                    {{ $transferItems->links('pagination::bootstrap-4') }}
                </div>

               <div class="row mt-4">
                   <div class="col-md-6 mb-3">
                       <div class="card-stats uzs">
                           <div class="w-100">
                               <h5>🇺🇿 UZS</h5>
                               <p>Хомашёлар: <strong>{{ number_format($allCountUzs, 0, '', ' ') }} та</strong></p>
                               <p>Умумий сумма: <strong>{{ number_format($totalPriceUzs, 0, '', ' ') }} сўм</strong></p>
                           </div>
                           <div>
                               <i class="bi bi-wallet2"></i>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-6 mb-3">
                       <div class="card-stats usd">
                           <div class="w-100">
                               <h5>🇺🇸 USD</h5>
                               <p>Хомашёлар: <strong>{{ number_format($allCountUsd, 0, '', ' ') }} та</strong></p>
                               <p>Умумий сумма: <strong>{{ number_format($totalPriceUsd, 2, '.', ' ') }} $</strong></p>
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
        document.getElementById('rawMaterialTransferItemFilterForm').addEventListener('submit', function () {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('select[name="filters[raw_material_transfer_id]"]').forEach(select => {
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
