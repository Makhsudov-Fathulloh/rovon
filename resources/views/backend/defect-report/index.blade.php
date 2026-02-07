<x-backend.layouts.main title="{{ 'Брак ҳисоботлари' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action :back="true"/>
            </div>

            <div class="card-body p-0">
                <form id="defectReportFilterForm" method="GET" action="{{ route('defect-report.index') }}">

                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('organization_title', 'Филиал') !!}</th>
                                <th>{!! sortLink('section_shift_stage', 'Смена') !!}</th>
                                <th>{!! sortLink('stage_defect_amount_percent', 'Микдори') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№...">
                                </th>
                                <th>
                                    <select name="filters[organization_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($organizations as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.organization_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[section_shift_stage]"
                                           value="{{ request('filters.section_shift_stage') }}"
                                           class="form-control form-control-sm w-100" placeholder="Қидирув..."></th>
                                <th><input type="text" name="filters[stage_defect_amount_percent]"
                                           value="{{ request('filters.stage_defect_amount_percent') }}"
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
                            @forelse($defectReports as $defectReport)
                                <tr class="text-center" id="row-{{ $defectReport->id }}">
                                    <td class="col-id">{{ $defectReport->id }}</td>
                                    <td>
                                        <span
                                            class="badge bg-info">{{ optional($defectReport->organization)->title }}</span>
                                    </td>
                                    <td>{{ '(' . optional($defectReport->section)->title . ') (' . optional($defectReport->shift)->title . ') ' . optional($defectReport->stage)->title }}</td>
                                    <td class="fw-bold text-center" style="line-height: 1;">
                                        <span
                                            class="text-success">{{ number_format($defectReport->stage_count, 0, '', ' ') }}</span>
                                        /
                                        <span class="text-warning">
                                            {{ number_format($defectReport->total_defect_amount, $defectReport->defect_type === \App\Services\StatusService::DEFECT_RAW_MATERIAL ? 3 : 0, '.', ' ') }}
                                        </span>
                                        <div class="line"></div>
                                        <span class="text-danger">
                                            {{ number_format($defectReport->defect_amount, $defectReport->defect_type === \App\Services\StatusService::DEFECT_RAW_MATERIAL ? 3 : 0, '.', ' ') }}
                                        </span>
                                        /
                                        <span class="text-info">{{ $defectReport->defect_percent }} %</span>
                                    </td>
                                    <td>{{ $defectReport->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action route="defect-report" :id="$defectReport->id" :view="true"/>
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
                    <div class="d-md-none">
                        <div class="d-flex m-4">
                            {{--<select name="filters[stage_id_mobile]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг">--}}
                            <select name="filters[stage_id]" class="form-control form-control-sm filter-select2 w-100"
                                    data-placeholder="Смена махсулотини танланг">
                                <option value="">Барчаси</option>
                                @foreach($stages as $id => $title)
                                    <option
                                        value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($defectReports as $defectReport)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($defectReport->file)->path)
                                                <img src="{{ asset('storage/' . $defectReport->file->path) }}" alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ optional($defectReport->stage)->title }}</div>
                                            <span class="text-muted small">ID: {{ $defectReport->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <td class="fw-bold text-center" style="line-height: 1;">
                                        <span class="text-success">{{ number_format($defectReport->stage_count, 0, '', ' ') }}</span> /
                                        <span class="text-warning">
                                            {{ number_format($defectReport->total_defect_amount, $defectReport->defect_type === \App\Services\StatusService::DEFECT_RAW_MATERIAL ? 3 : 0, '.', ' ') }}
                                        </span>
                                        <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                        <span class="text-danger">
                                            {{ number_format($defectReport->defect_amount, $defectReport->defect_type === \App\Services\StatusService::DEFECT_RAW_MATERIAL ? 3 : 0, '.', ' ') }}
                                        </span>
                                        /
                                        <span class="text-info">{{ $defectReport->defect_percent }} %</span>
                                    </td>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Смена</small>
                                        <span
                                            class="small fw-medium">{{ '(' . optional($defectReport->shift->section->organization)->title . ') (' . optional($defectReport->shift->section)->title . ') ' . optional($defectReport->shift)->title }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $defectReport->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>
                                            <span class="badge bg-info">{{ $defectReport->user->username }}</span>
                                    </span>
                                    <div class="action-btns">
                                        <x-backend.action route="defect-report" :id="$defectReport->id" :view="true"/>
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
                    {{ $defectReports->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3 mb-3">
                <div class="card-stats uzs">
                    <div class="w-100">
                        <p>Браклар (Хомашё):</p>
                        <h5>{{ number_format($rawAmount, 3, '.', ' ') }} кг</h5>
                    </div>
                    <div>
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card-stats usd">
                    <div class="w-100">
                        <p>Браклар сони:</p>
                        <h5>{{ number_format($rawCount, 0, '', ' ') }} та</h5>
                    </div>
                    <div>
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card-stats eur">
                    <div class="w-100">
                        <p>Браклар (Баланс):</p>
                        <h5>{{ number_format($prevAmount, 0, '', ' ') }} дона</h5>
                    </div>
                    <div>
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card-stats gbp">
                    <div class="w-100">
                        <p>Браклар сони:</p>
                        <h5>{{ number_format($prevCount, 0, '', ' ') }} та</h5>
                    </div>
                    <div>
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('defectReportFilterForm').addEventListener('submit', function () {
            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name="filters[stage_id]"], select[name="filters[stage_id]"]').forEach(select => {
                if (select.offsetParent === null) {
                    select.disabled = true;
                }
            });

            // Bo‘sh input/selectlarni name’siz qilamiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
