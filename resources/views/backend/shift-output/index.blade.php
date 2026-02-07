<x-backend.layouts.main title="{{ 'Смена махсулотлари' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action :back="true"/>
            </div>

            <div class="card-body p-0">
                <form id="shiftOutputFilterForm" method="GET" action="{{ route('shift-output.index') }}">

                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('organization_section_shift', 'Смена') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('count_or_defect', 'Микдори/Брак') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№...">
                                </th>
                                <th><input type="text" name="filters[organization_section_shift]"
                                           value="{{ request('filters.organization_section_shift') }}"
                                           class="form-control form-control-sm w-100" placeholder="Қидирув...">
                                </th>
                                <th>
                                    <select name="filters[stage_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($stages as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[count_or_defect]"
                                           value="{{ request('filters.count_or_defect') }}"
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
                            @forelse($shiftOutputs as $shiftOutput)
                                <tr class="text-center" id="row-{{ $shiftOutput->id }}">
                                    <td class="col-id">{{ $shiftOutput->id }}</td>
                                    <td>{{ '(' . optional($shiftOutput->shift->section->organization)->title . ') (' . optional($shiftOutput->shift->section)->title . ') ' . optional($shiftOutput->shift)->title }}</td>
                                    <td>{{ optional($shiftOutput->stage)->title }}</td>
                                    <td class="fw-bold text-center" style="line-height: 1;">
                                        <div
                                            class="text-success">{{ number_format($shiftOutput->stage_count, 0, '', ' ') }}</div>
                                        <div class="line"></div>
                                        <div
                                            class="text-danger">{{ number_format($shiftOutput->defect_amount, 3, '.', ' ') }}</div>
                                    </td>
                                    <td>{{ $shiftOutput->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action
                                            route="shift-output" listRoute="shift-output-worker" :id="$shiftOutput->id"
                                            :list="true" :view="true" :edit="true" :delete="true"
                                            subRoute="workers"
                                            listTitle="Смена ходимлари кўриш"
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
                            <div class="d-flex m-4">
                                {{-- <select name="filters[stage_id_mobile]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг"> --}}
                                <select name="filters[stage_id]"
                                        class="form-control form-control-sm filter-select2 w-100"
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
                            @forelse($shiftOutputs as $shiftOutput)
                                <div class="mobile-card shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                                @if(optional($shiftOutput->file)->path)
                                                    <img src="{{ asset('storage/' . $shiftOutput->file->path) }}"
                                                         alt="">
                                                @else
                                                    <div
                                                        class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                        <i class="bi bi-image"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <div
                                                    class="fw-bold mb-0 text-dark">{{ optional($shiftOutput->stage)->title }}</div>
                                                <span class="text-muted small">ID: {{ $shiftOutput->id }}</span>
                                            </div>
                                        </div>
                                        <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div
                                                class="text-success">{{ number_format($shiftOutput->stage_count, 0, '', ' ') }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-danger">{{ number_format($shiftOutput->defect_amount, 3, '.', ' ') }}</div>
                                        </div>
                                    </span>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block text-uppercase"
                                                   style="font-size: 0.65rem;">Смена</small>
                                            <span
                                                class="small fw-medium">{{ '(' . optional($shiftOutput->shift->section->organization)->title . ') (' . optional($shiftOutput->shift->section)->title . ') ' . optional($shiftOutput->shift)->title }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block text-uppercase"
                                                   style="font-size: 0.65rem;">Яратилди</small>
                                            <span
                                                class="small fw-medium">{{ $shiftOutput->created_at?->format('d.m.Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>
                                        @foreach($shiftOutput->shift->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </span>
                                        <div class="action-btns">
                                            <x-backend.action route="shift-output" :id="$shiftOutput->id" :view="true"
                                                              :edit="true"/>
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
                    </div>
                    {{-- Mobile version end --}}
                </form>
            </div>

            <div class="card-footer bg-white border-top-0 p-4">
                <div class="d-flex justify-content-center">
                    {{ $shiftOutputs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        @php
            $exportWhere = $stageIds->isEmpty() ? [] : ['id' => $stageIds->toArray()];
        @endphp

        <h3 class="text-center mt-3 mb-3">
            Махсулотлар
            <x-backend.export
                :model="\App\Models\Stage::class"
                :where="$exportWhere"
                :columns="[
                            'index' => '№',
                            'full_title' => 'Маҳсулот номи',
                            'monthly_count' => 'Ойлик ҳажм',
                            'monthly_defect' => 'Ойлик брак',
                        ]"
                :totals="[
                            'Жами маҳсулот:' => number_format(collect($productStatistics)->sum('monthly_product'), 0, '', ' ') . ' дона',
                            'Жами брак (Хомашё):' => number_format(collect($productStatistics)->sum('monthly_defect_raw'), 3, '.', ' ') . ' кг',
                            'Жами брак (Олдинги босқич):' => number_format(collect($productStatistics)->sum('monthly_defect_prev'), 0, '', ' ') . ' дона',
                        ]"
                :header="[
                            'title' => 'Rovon textile',
                            'subtitle' => 'Маҳсулотлар бўйичa ойлик ҳисобот',
                            'date' => now()->format('d.m.Y'),
                            'logo_left' => 'images/logo-text-.png',
                            'logo_right' => 'images/logo-text-.png',
                        ]"
            />
        </h3>

        @foreach($productStatistics as $statistics)
            <div class="row text-center">
                <div class="col-md-3 alert alert-info">
                    <div class="h-100 d-flex justify-content-center align-items-center">
                                <span
                                    class="fw-bold">{{ $statistics['title'] . ' (' . $statistics['section_title'] . ')' }}</span>
                    </div>
                </div>
                <div class="col-md-3 alert alert-warning">
                    <div class="fw-bold">Кунлик</div>
                    Маҳсулот: <strong
                        class="h4">{{ number_format($statistics['daily_product'], 0, '', ' ') }}</strong> та<br>
                    Брак: <strong
                        class="h4">{{ number_format($statistics['daily_defect'], 3, '.', ' ') }}</strong> кг
                </div>
                <div class="col-md-3 alert alert-success">
                    <div class="fw-bold">Ойлик</div>
                    Маҳсулот: <strong
                        class="h4">{{ number_format($statistics['monthly_product'], 0, '', ' ') }}</strong>
                    та<br>
                    Брак: <strong
                        class="h4">{{ number_format($statistics['monthly_defect'], 3, '.', ' ') }}</strong> кг
                </div>
                <div class="col-md-3 alert alert-secondary">
                    <div class="fw-bold">Йиллик</div>
                    Маҳсулот: <strong
                        class="h4">{{ number_format($statistics['yearly_product'], 0, '', ' ') }}</strong>
                    та<br>
                    Брак: <strong
                        class="h4">{{ number_format($statistics['yearly_defect'], 3, '.', ' ') }}</strong> кг
                </div>
            </div>
        @endforeach
    </div>

    {{--    <script>--}}
    {{--        document.getElementById('shiftOutputFilterForm').addEventListener('submit', function () {--}}
    {{--            let isMobile = window.innerWidth < 768;--}}

    {{--            let desktopStage = this.querySelector('select[name="filters[stage_id_desktop]"]');--}}
    {{--            let mobileStage  = this.querySelector('select[name="filters[stage_id_mobile]"]');--}}

    {{--            // Mobile bo‘lsa → desktopni o‘chirib tashlaymiz--}}
    {{--            if (isMobile) {--}}
    {{--                if (desktopStage) desktopStage.removeAttribute('name');--}}
    {{--                if (mobileStage) mobileStage.setAttribute('name', 'filters[stage_id]');--}}
    {{--            }--}}
    {{--            // Desktop bo‘lsa → mobileni o‘chirib tashlaymiz--}}
    {{--            else {--}}
    {{--                if (mobileStage) mobileStage.removeAttribute('name');--}}
    {{--                if (desktopStage) desktopStage.setAttribute('name', 'filters[stage_id]');--}}
    {{--            }--}}

    {{--            // Bo‘sh input/selectlarni olib tashlaymiz--}}
    {{--            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {--}}
    {{--                if (!input.value || !input.value.trim()) {--}}
    {{--                    input.removeAttribute('name'); // name olib tashlanadi--}}
    {{--                }--}}
    {{--            });--}}
    {{--        });--}}
    {{--    </script>--}}

    <script>
        document.getElementById('shiftOutputFilterForm').addEventListener('submit', function () {
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
