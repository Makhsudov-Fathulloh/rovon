@php
    use App\Services\StatusService;
    use App\Helpers\PriceHelper;
@endphp

<x-backend.layouts.main title="{{ 'Хомашё трансферлари' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <a href="{{ route('raw-material-transfer.create') }}" class="btn btn-primary w-100 w-md-auto">
                            {{ 'Яратиш' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="rawMaterialTransferFilterForm" method="GET"
                      action="{{ route('raw-material-transfer.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('organization_id', 'Филиал') !!}</th>
                                <th>{!! sortLink('warehouse_id', 'Омбор') !!}</th>
                                <th>{!! sortLink('section_id', 'Бўлим') !!}</th>
                                <th>{!! sortLink('shift_id', 'Смена') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('sender_id', 'Юборувчи') !!}</th>
                                <th>{!! sortLink('receiver_id', 'Кабулкилувчи') !!}</th>
                                <th>{!! sortLink('total_item_price', 'Умумий') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th>  {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs--}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[organization_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($organizations as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.organization_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[warehouse_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($warehouses as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.warehouse_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[section_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($sections as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.section_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[shift_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($shifts as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.shift_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th>
                                    <select name="filters[sender_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($senders as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.sender_id') == $id ? 'selected' : '' }}>{{ $username }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[receiver_id]"
                                            class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($receivers as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.receiver_id') == $id ? 'selected' : '' }}>{{ $username }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[total_item_price]" value="{{ request('filters.total_item_price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(StatusService::getList() as $key => $label)
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
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($transfers as $transfer)
                                <tr class="text-center" id="row-desktop-{{ $transfer->id }}">
                                    <td class="col-id">{{ $transfer->id }}</td>
                                    <td>{{ optional($transfer->organization)->title }}</td>
                                    <td>{{ optional($transfer->warehouse)->title }}</td>
                                    <td>{{ optional($transfer->section)->title }}</td>
                                    <td>{{ optional($transfer->shift)->title }}</td>
                                    <td>{{ $transfer->title }}</td>
                                    <td>{{ optional($transfer->sender)->username }}</td>
                                    <td>{{ optional($transfer->receiver)->username }}</td>
                                    <td class="total_item_price fw-bold text-info">{{ number_format($transfer->total_item_price, 0, '', ' ') }} сўм</td>
                                    <td style="width: 100px">{{ StatusService::getList()[$transfer->status] ?? '-' }}</td>
                                    <td>{{ $transfer->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="raw-material-transfer" listRoute="raw-material-transfer-item" :id="$transfer->id"
                                              subRoute="elements" :list="true" :view="true" :edit="true" :delete="true"
                                              listTitle="Хомашё трансфер элементлари кўриш" viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                   class="form-control form-control-sm me-1" placeholder="Элемент номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($transfers as $transfer)
                            <div class="card border" id="row-mobile-{{ $transfer->id }}">
                                <div class="card-body">
                                    <div class="text-center mb-2">
                                        @if(optional($transfer->file)->path)
                                            <img src="{{ asset('storage/' . $transfer->file->path) }}"
                                                 alt="Image"
                                                 style="width: 50px; height: auto;">
                                        @endif
                                    </div>
                                    <p class="card-text ">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $transfer->id }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('organization_id', 'Филиал:') !!} </strong>{{ optional($transfer->organization)->title }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('warehouse_id', 'Омбор:') !!} </strong>{{ optional($transfer->warehouse)->title }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('section_id', 'Бўлим:') !!} </strong>{{ optional($transfer->section)->title }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('shift_id', 'Смена:') !!} </strong>{{ optional($transfer->shift)->title }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!} </strong>{{ $transfer->title }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('sender_id', 'Юборувчи:') !!} </strong>{{ optional($transfer->sender)->username }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('receiver_id', 'Кабулкилувчи:') !!} </strong>{{ optional($transfer->receiver)->username }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_item_price', 'Умумий:') !!} </strong><span class="total_item_price fw-bold text-info">{{ number_format($transfer->total_item_price, 0, '', ' ') }} сўм</span></p>

                                    <div class="btn-group w-100">
                                        <x-backend.action route="raw-material-transfer" listRoute="raw-material-transfer-item" :id="$transfer->id"
                                              subRoute="elements" :list="true" :view="true" :edit="true" :delete="true"
                                              listTitle="Хомашё трансфер элементлари кўриш" viewClass="btn btn-secondary btn-sm"
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

                {{-- Pagination--}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $transfers->links('pagination::bootstrap-4') }}
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
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card-stats uzs">
                            <div class="w-100">
                                <h5>🇺🇿 UZS</h5>
                                <p>Сони: <strong>{{ number_format($allCountUzs, 0, '', ' ') }} та</strong></p>
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
                                <p>Сони: <strong>{{ number_format($allCountUsd, 0, '', ' ') }} та</strong></p>
                                <p>Умумий сумма: <strong>{{ number_format($totalPriceUsd, 2, '.', ' ') }} $</strong></p>
                            </div>
                            <div>
                                <i class="bi bi-currency-exchange"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <style>
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

                    /* .card-stats.uzs { background: linear-gradient(135deg, #00b894, #55efc4); border-left: 5px solid #00d68f; }
                    .card-stats.usd { background: linear-gradient(135deg, #0984e3, #74b9ff); border-left: 5px solid #00a8ff; }
                    .card-stats.eur { background: linear-gradient(135deg, #6c5ce7, #a29bfe); border-left: 5px solid #8e76ff; }
                    .card-stats.gbp { background: linear-gradient(135deg, #fd79a8, #ffb8b8); border-left: 5px solid #ff6b81; }
                    .card-stats.jpy { background: linear-gradient(135deg, #fdcb6e, #ffeaa7); border-left: 5px solid #f6d860; } */

                    .card-stats.uzs { background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%); border-left: 5px solid #00d68f; }
                    .card-stats.usd { background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%); border-left: 5px solid #00a8ff; }
                    .card-stats.eur { background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%); border-left: 5px solid #8e76ff; }
                    .card-stats.gbp { background: linear-gradient(135deg, #fd79a8 30%, #e84393 90%); border-left: 5px solid #ff6b81; }
                    .card-stats.jpy { background: linear-gradient(135deg, #fdcb6e 30%, #f6b93b 90%); border-left: 5px solid #f6d860; }

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
                <div class="d-flex flex-wrap gap-3 mt-4"> -->
                    <!-- UZS -->
                    <!-- <div class="card-stats uzs">
                        <div class="w-100">
                            <h5>🇺🇿 UZS</h5>
                            <p>A<strong>b</strong></p>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div> -->

                    <!-- USD -->
                    <!-- <div class="card-stats usd">
                        <div class="w-100">
                            <h5>🇺🇸 USD</h5>
                            <p>A<strong>b</strong></p>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div> -->

                    <!-- EUR -->
                    <!-- <div class="card-stats eur">
                        <div class="w-100">
                            <h5>💶 EUR</h5>
                            <p>A<strong>b</strong></p>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div> -->

                    <!-- GBP -->
                    <!-- <div class="card-stats gbp">
                        <div class="w-100">
                            <h5>💷 GBP</h5>
                            <p>A<strong>b</strong></p>
                        </div>
                        <div>
                            <i class="bi bi-currency-pound"></i>
                        </div>
                    </div> -->

                    <!-- JPY -->
                    <!-- <div class="card-stats jpy">
                        <div class="w-100">
                            <h5>💴 JPY</h5>
                            <p>A<strong>b</strong></p>
                        </div>
                        <div>
                            <i class="bi bi-currency-yen"></i>
                        </div>
                    </div>
                </div>-->
        </div>
    </div>

    <script>
        document.getElementById('rawMaterialTransferFilterForm').addEventListener('submit', function () {
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
