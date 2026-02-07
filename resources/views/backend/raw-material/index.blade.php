@php
    use App\Services\StatusService;
@endphp

<x-backend.layouts.main title="{{ 'Хомашё турлари' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <div class="col-sm-12 col-md-auto text-start">
                    <x-backend.action route="raw-material" :back="true" :create="true"/>
                </div>
            </div>

            <div class="card-body p-0">
                <form id="rawMaterialFilterForm" method="GET" action="{{ route('raw-material.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('organization_warehouse_raw_material', 'Номи') !!}</th>
                                <th>{!! sortLink('category_id', 'Категория') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№...">
                                </th>
                                <th><input type="text" name="filters[organization_warehouse_raw_material]" value="{{ request('filters.organization_warehouse_raw_material') }}"
                                           class="form-control form-control-sm w-100" placeholder="Қидирув...">
                                </th>
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

                                <th class="p-0">
                                    <div class="d-flex justify-content-center align-items-center"
                                         style="min-height: 75px;">
                                        <button type="submit" class="btn btn-custom-search" title="Филтрлаш">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($rawMaterials as $rawMaterial)
                                <tr class="text-center" id="row-{{ $rawMaterial->id }}">
                                    <td class="col-id">{{ $rawMaterial->id }}</td>
                                    <td>{{ '('. $rawMaterial->warehouse->organization->pluck('title')->implode(', ') . ') (' . optional($rawMaterial->warehouse)->title . ') ' . $rawMaterial->title  }}</td>
                                    <td>{{ optional($rawMaterial->category)->title }}</td>
                                    <td>
                                        <span
                                            class="badge-custom {{ StatusService::getListClass()[$rawMaterial->status] }}">{{ StatusService::getList()[$rawMaterial->status] }}</span>
                                    </td>
                                    <td class="col-date">{{ $rawMaterial->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action
                                            route="raw-material" listRoute="raw-material-variation"
                                            :id="$rawMaterial->id"
                                            subRoute="variations"
                                            :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                            createTitle="Хомашё элементини яратиш"
                                            listTitle="Хомашё элементлнарини кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-5 text-center">
                                        <img src="{{ asset('images/systems/reference-not-found.png') }}" width="60"
                                             class="mb-3 opacity-20" alt="">
                                        <p class="text-muted">Маълумот топилмади</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none p-3">
                        <div class="search-box-mobile mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"
                                      style="border-radius: 12px 0 0 12px;"><i
                                        class="fa fa-search text-muted"></i></span>
                                <input type="text" name="filters[organization_warehouse_raw_material]"
                                       value="{{ request('filters.organization_warehouse_raw_material') }}"
                                       class="form-control border-start-0 ps-0" placeholder="     Қидирув..."
                                       style="border-radius: 0 12px 12px 0; height: 48px;">
                                <button type="submit" class="btn btn-primary ms-2"
                                        style="border-radius: 12px; width: 48px;"><i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        @forelse($rawMaterials as $rawMaterial)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($rawMaterial->file)->path)
                                                <img src="{{ asset('storage/' . $rawMaterial->file->path) }}" alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ $rawMaterial->title }}</div>
                                            <span class="text-muted small">ID: {{ $rawMaterial->id }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="badge-custom {{ StatusService::getListClass()[$rawMaterial->status] }}">{{ StatusService::getList()[$rawMaterial->status] }}</span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Номи</small>
                                        <span
                                            class="small fw-medium">{{ '('. $rawMaterial->warehouse->organization->pluck('title')->implode(', ') . ') ' . optional($rawMaterial->warehouse)->title }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span class="small fw-medium">{{ $rawMaterial->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i><span class="badge bg-info">{{ optional($rawMaterial->category)->title }}</span></span>
                                    <div class="action-btns">
                                        <x-backend.action
                                            route="raw-material" listRoute="raw-material-variation"
                                            :id="$rawMaterial->id"
                                            subRoute="variations"
                                            :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                            createTitle="Хомашё элементини яратиш"
                                            listTitle="Хомашё элементлнарини кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-5 text-center">
                                <img src="{{ asset('images/systems/reference-not-found.png') }}" width="45"
                                     class="mb-3 opacity-20" alt="">
                                <div class="py-4">Маълумот топилмади</div>
                            </div>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>
            </div>

            <div class="card-footer bg-white border-top-0 p-4">
                <div class="d-flex justify-content-center">
                    {{ $rawMaterials->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <div class="card-stats count">
                    <div class="w-100">
                        <p>Хомашёлар</p>
                        <h5><strong>{{ number_format($rawMaterialCount, 0, '', ' ') }} та</strong></h5>
                    </div>
                    <div>
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('rawMaterialFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('input[name="filters[organization_warehouse_raw_material]"]').forEach(select => {
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
