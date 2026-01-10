<x-backend.layouts.main title="{{ 'Брак ҳисоботлари' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="defectReportFilterForm" method="GET" action="{{ route('defect-report.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('organization_title', 'Филиал') !!}</th>
                                <th>{!! sortLink('section_title', 'Бўлим') !!}</th>
                                <th>{!! sortLink('shift_id', 'Смена') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('stage_count', 'Микдори') !!}</th>
                                <th>{!! sortLink('total_defect_amount', 'Умумий') !!}</th>
                                <th>{!! sortLink('defect_amount', 'Ортикча') !!}</th>
                                <th>{!! sortLink('defect_percent', '%') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
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
                                <th>
                                    <select name="filters[section_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($sections as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.section_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[shift_id]" class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($shifts as $id => $title)
                                            <option value="{{ $id }}" {{ request('filters.shift_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
{{--                                    <select name="filters[stage_id_desktop]" class="form-control form-control-sm filter-select2 w-100">--}}
                                    <select name="filters[stage_id]" class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($stages as $id => $title)
                                            <option value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[stage_count]" value="{{ request('filters.stage_count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[total_defect_amount]" value="{{ request('filters.total_defect_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[defect_amount]" value="{{ request('filters.defect_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[defect_percent]" value="{{ request('filters.defect_percent') }}"
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
                            @forelse($defectReports as $defectReport)
                                <tr class="text-center" id="row-{{ $defectReport->id }}">
                                    <td class="col-id">{{ $defectReport->id }}</td>
                                    <td>{{ optional($defectReport->organization)->title }}</td>
                                    <td>{{ optional($defectReport->section)->title }}</td>
                                    <td>{{ optional($defectReport->shift)->title }}</td>
                                    <td>{{ optional($defectReport->stage)->title }}</td>
                                    <td class="text-success fw-bold">{{ $defectReport->stage_count }} </td>
                                    <td class="text-primary fw-bold">{{ $defectReport->total_defect_amount }} кг</td>
                                    <td class="text-danger fw-bold">{{ $defectReport->defect_amount }} кг</td>
                                    <td class="text-info fw-bold">{{ $defectReport->defect_percent }} %</td>
                                    <td>{{ $defectReport->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="defect-report" :id="$defectReport->id" :view="true"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
{{--                            <select name="filters[stage_id_mobile]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг">--}}
                            <select name="filters[stage_id]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг">
                                <option value="">Барчаси</option>
                                @foreach($stages as $id => $title)
                                    <option value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($defectReports as $defectReport)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong>{{ $defectReport->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('organization_title', 'Филиал:') !!}</strong>{{ optional($defectReport->organization)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('section_title', 'Бўлим:') !!}</strong>{{ optional($defectReport->section)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('shift_id', 'Смена:') !!}</strong>{{ optional($defectReport->shift)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_id', 'Махсулот:') !!}</strong>{{ optional($defectReport->stage)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_count', 'Микдори:') !!}</strong><span class="text-success fw-bold">{{ number_format($defectReport->stage_count, 0, '', ' ') }} та</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_defect_amount', 'Умумий:') !!}</strong><span class="text-danger fw-bold">{{ $defectReport->total_defect_amount }} кг</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('defect_amount', 'Брак:') !!}</strong><span class="text-danger fw-bold">{{ $defectReport->defect_amount }} кг</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('defect_percent', '%:') !!}</strong><span class="text-danger fw-bold">{{ $defectReport->defect_percent }} %</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди:') !!}</strong> {{ $defectReport->created_at?->format('Y-m-d H:i') }}</p>
                                    <x-backend.action route="defect-report" :id="$defectReport->id" :view="true" />
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
                    {{ $defectReports->links('pagination::bootstrap-4') }}
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
                                   <p>Браклар микдори:</p>
                                   <h5>{{ number_format($defectAmount, 3, '.', ' ') }} кг</h5>
                               </div>
                               <div>
                                   <i class="bi bi-wallet2"></i>
                               </div>
                           </div>
                       </div>
                       <div class="col-md-6 mb-3">
                           <div class="card-stats usd">
                               <div class="w-100">
                                 <p>Браклар сони:</strong></p>
                                <h5>{{ number_format($defectCount, 0, '', ' ') }} та</h5>
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
