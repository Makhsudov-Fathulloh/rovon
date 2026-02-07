<x-backend.layouts.main title="{{ 'Смена ходимлари' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action :back="true"/>
            </div>

            <div class="card-body p-0">
                <form id="shiftOutputWorkerFilterForm" method="GET" action="{{ route('shift-output-worker.index') }}">

                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th style="width: 25%">{!! sortLink('user_id', 'Ходим') !!}</th>
                                <th>{!! sortLink('shift_output_stage', 'Смена махсулоти') !!}</th>
                                <th>{!! sortLink('count_defect_price', 'Микдори/Брак/Нархи') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№..."></th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[shift_or_stage]"
                                           value="{{ request('filters.shift_or_stage') }}"
                                           class="form-control form-control-sm w-100" placeholder="Қидирув...">
                                </th>
                                <th><input type="text" name="filters[stage_defect_price]"
                                           value="{{ request('filters.stage_defect_price') }}"
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
                            @forelse($shiftOutputWorkers as $shiftOutputWorker)
                                <tr class="text-center" id="row-{{ $shiftOutputWorker->id }}">
                                    <td class="col-id">{{ $shiftOutputWorker->id }}</td>
                                    <td class="col-title">{{ $shiftOutputWorker->user->username }}</td>
                                    <td>{{ '(' . optional($shiftOutputWorker->shiftOutput->shift)->title . ') ' . optional($shiftOutputWorker->shiftOutput->stage)->title }}</td>
                                    <td class="fw-bold text-center" style="line-height: 1;">
                                        <div
                                            class="text-success">{{ number_format($shiftOutputWorker->stage_count, 0, '', ' ') }}</div>
                                        <div class="line"></div>
                                        <div
                                            class="text-danger">{{ number_format($shiftOutputWorker->defect_amount, 3, '.', ' ') }}</div>
                                        <div class="line"></div>
                                        <div
                                            class="text-info">{{ number_format($shiftOutputWorker->price, 0, '', ' ') }}</div>
                                    </td>
                                    <td class="col-date">{{ $shiftOutputWorker->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action route="shift-output-worker" :id="$shiftOutputWorker->id"
                                                          :view="true"/>
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
                            <select name="filters[user_id]"
                                    class="form-control form-control-sm filter-select2 w-100"
                                    data-placeholder="Смена ходимнини танланг">
                                <option value="">Барчаси</option>
                                @foreach($users as $id => $username)
                                    <option
                                        value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                        {{ $username }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($shiftOutputWorkers as $shiftOutputWorker)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($shiftOutputWorker->file)->path)
                                                <img src="{{ asset('storage/' . $shiftOutputWorker->file->path) }}"
                                                     alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ optional($shiftOutputWorker->user)->username }}</div>
                                            <span class="text-muted small">ID: {{ $shiftOutputWorker->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div
                                                class="text-success">{{ number_format($shiftOutputWorker->stage_count, 0, '', ' ') }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-danger">{{ number_format($shiftOutputWorker->defect_amount, 3, '.', ' ') }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-info">{{ number_format($shiftOutputWorker->price, 0, '', ' ') }}</div>
                                        </div>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Смена</small>
                                        <span
                                            class="small fw-medium">{{ '(' . optional($shiftOutputWorker->shiftOutput->shift)->title . ') ' . optional($shiftOutputWorker->shiftOutput->stage)->title }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $shiftOutputWorker->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i><span
                                            class="badge bg-info"></span></span>
                                    <div class="action-btns">
                                        <x-backend.action route="shift-output" :id="$shiftOutputWorker->id"
                                                          :view="true"/>
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
                    {{ $shiftOutputWorkers->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <h3 class="text-center mt-3 mb-3">Махсулотлар</h3>
        @foreach($userStatistics as $statistics)
            <div class="row text-center">
                <div class="col-md-3 alert alert-info">
                    <div class="h-100 d-flex justify-content-center align-items-center">
                                <span
                                    class="fw-bold">{{ $statistics['username'] . ' (' . $statistics['section_title'] . ')' }}</span>
                    </div>
                </div>
                <div class="col-md-3 alert alert-warning">
                    <div class="fw-bold">Кунлик</div>
                    Маҳсулот: <strong
                        class="h4">{{ number_format($statistics['daily_product'], 0, '', ' ') }}</strong> та<br>
                    Брак: <strong
                        class="h4">{{ number_format($statistics['daily_defect'], 3, '.', ' ') }}</strong> кг<br>
                    Нархи: <strong
                        class="h4">{{ number_format($statistics['daily_price'], 0, '', ' ') }}</strong> сўм
                </div>
                <div class="col-md-3 alert alert-success">
                    <div class="fw-bold">Ойлик</div>
                    Маҳсулот: <strong
                        class="h4">{{ number_format($statistics['monthly_product'], 0, '', ' ') }}</strong>
                    та<br>
                    Брак: <strong
                        class="h4">{{ number_format($statistics['monthly_defect'], 3, '.', ' ') }}</strong> кг
                    <br>
                    Нархи: <strong
                        class="h4">{{ number_format($statistics['monthly_price'], 0, '', ' ') }}</strong> сўм
                </div>
                <div class="col-md-3 alert alert-secondary">
                    <div class="fw-bold">Йиллик</div>
                    Маҳсулот: <strong
                        class="h4">{{ number_format($statistics['yearly_product'], 0, '', ' ') }}</strong>
                    та<br>
                    Брак: <strong
                        class="h4">{{ number_format($statistics['yearly_defect'], 3, '.', ' ') }}</strong> кг
                    <br>
                    Нархи: <strong
                        class="h4">{{ number_format($statistics['yearly_price'], 0, '', ' ') }}</strong> сўм
                </div>
            </div>
        @endforeach

            </div>
        </div>
    </div>

    <script>
        document.getElementById('shiftOutputWorkerFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[user_id]"], select[name="filters[user_id]"]').forEach(select => {
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
