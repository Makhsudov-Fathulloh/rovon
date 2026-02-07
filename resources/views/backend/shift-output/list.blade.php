<x-backend.layouts.main title="{!! 'Смена ( ' . $shift->title . ' ) маҳсулотлари:' !!}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action :back="true"/>
            </div>

            <div class="card-body p-0">
                <form id="shiftOutputListFilterForm" method="GET" action="{{ route('shift-output.list', $shift->id) }}">

                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('users', 'Ходимлар') !!}</th>
                                <th>{!! sortLink('count_or_defect', 'Микдори/Брак') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№..."></th>
                                <th>
                                    <select name="filters[stage_id_desktop]"
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
                                <th><input type="text" name="filters[users]"
                                           value="{{ request('filters.users') }}"
                                           class="form-control form-control-sm w-100" style="display: none;">
                                </th>
                                <th><input type="text" name="filters[count_or_defect]"
                                           value="{{ request('filters.count_or_defect') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal">
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
                            @forelse($outputs as $output)
                                <tr class="text-center" id="row-{{ $output->id }}">
                                    <td class="col-id">{{ $output->id }}</td>
                                    <td style="width: 30%">{{ optional($output->stage)->title }}</td>
                                    <td>
                                        @foreach($shift->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </td>
                                    <td class="fw-bold text-center" style="line-height: 1;">
                                        <div
                                            class="text-success">{{ number_format($output->stage_count, 0, '', ' ') }}</div>
                                        <div class="line"></div>
                                        <div
                                            class="text-danger">{{ number_format($output->defect_amount, 3, '.', ' ') }}</div>
                                    </td>
                                    <td class="col-date">{{ $output->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action
                                            route="shift-output" listRoute="shift-output-worker" :id="$output->id"
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
                    <div class="d-md-none">
                        <div class="d-flex m-4">
                            <select name="filters[stage_id_mobile]"
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
                        @forelse($outputs as $output)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($output->file)->path)
                                                <img src="{{ asset('storage/' . $output->file->path) }}" alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ optional($output->stage)->title }}</div>
                                            <span class="text-muted small">ID: {{ $output->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div
                                                class="text-success">{{ number_format($output->stage_count, 0, '', ' ') }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-danger">{{ number_format($output->defect_amount, 3, '.', ' ') }}</div>
                                        </div>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase"
                                               style="font-size: 0.65rem;"></small>
                                        <span class="small fw-medium"></span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $output->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>
                                        @foreach($output->shift->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </span>
                                    <div class="action-btns">
                                        <x-backend.action
                                            route="shift-output" listRoute="shift-output-worker" :id="$output->id"
                                            :list="true" :view="true" :edit="true" :delete="true"
                                            subRoute="workers"
                                            listTitle="Смена ходимлари кўриш"
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
                    {{ $outputs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <h3 class="text-center mt-3 mb-3">Махсулотлар</h3>
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

    <script>
        // Index dagi bilan ikkalasi bitta vazifa bajaradi
        document.getElementById('shiftOutputListFilterForm').addEventListener('submit', function () {
            let isMobile = window.innerWidth < 768;

            let desktopStage = this.querySelector('select[name="filters[stage_id_desktop]"]');
            let mobileStage = this.querySelector('select[name="filters[stage_id_mobile]"]');

            // Avval ikkalasidan ham "filters[stage_id]" ni olib tashlaymiz
            if (desktopStage) desktopStage.removeAttribute('name');
            if (mobileStage) mobileStage.removeAttribute('name');

            // So‘ngra ekranga mosini "filters[stage_id]" qilib qo‘yamiz
            if (isMobile && mobileStage) {
                mobileStage.setAttribute('name', 'filters[stage_id]');
            } else if (!isMobile && desktopStage) {
                desktopStage.setAttribute('name', 'filters[stage_id]');
            }

            // Bo‘sh input/selectlarni name’siz qilamiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // <-- remove emas, name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
