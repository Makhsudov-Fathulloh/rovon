<x-backend.layouts.main title="{!! 'Смена ( ' . $shiftOutput->shift->title . ' ) ходимлари:' !!}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action :back="true"/>
            </div>

            <div class="card-body p-0">
                <form id="shiftOutputWorkerListFilterForm" method="GET"
                      action="{{ route('shift-output-worker.list', $shiftOutput->id) }}">

                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('user_id', 'Ходим') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
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
                                <th><input type="text" name="filters[count_defect_price]"
                                           value="{{ request('filters.count_defect_price') }}"
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
                            @forelse($workers as $worker)
                                <tr class="text-center" id="row-{{ $worker->id }}">
                                    <td class="col-id">{{ $worker->id }}</td>
                                    <td style="width: 25%">{{ optional($worker->user)->username }}</td>
                                    <td style="width: 20%">{{ optional($worker->shiftOutput->stage)->title }}</td>
                                    <td class="fw-bold text-center" style="line-height: 1;">
                                        <div class="text-success">{{ number_format($worker->stage_count, 0, '', ' ') }}</div>
                                        <div class="line"></div>
                                        <div class="text-danger">{{ number_format($worker->defect_amount, 3, '.', ' ') }}</div>
                                        <div class="line"></div>
                                        <div class="text-info">{{ number_format($worker->price, 0, '', ' ') }}</div>
                                    </td>
                                    <td class="col-date">{{ $worker->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action route="shift-output-worker" :id="$worker->id" :view="true"/>
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
                                    data-placeholder="Смена  ходимини танланг">
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
                        @forelse($workers as $worker)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($worker->file)->path)
                                                <img src="{{ asset('storage/' . $worker->file->path) }}" alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{  optional($worker->user)->username }}</div>
                                            <span class="text-muted small">ID: {{ $worker->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div class="text-success">{{ number_format($worker->stage_count, 0, '', ' ') }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div class="text-danger">{{ number_format($worker->defect_amount, 3, '.', ' ') }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div class="text-info">{{ number_format( $worker->price , 0, '', ' ') }}</div>
                                        </div>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase"
                                               style="font-size: 0.65rem;"></small>
                                        <span class="small fw-medium">{{ optional($worker->shiftOutput->stage)->title }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $worker->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>
                                            <span class="badge bg-info"></span>
                                    </span>
                                    <div class="action-btns">
                                        <x-backend.action route="shift-output-worker" :id="$worker->id" :view="true"/>
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
                        {{ $workers->links('pagination::bootstrap-4') }}
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
                                class="h4">{{ number_format($statistics['monthly_defect'], 3, '.', ' ') }}</strong>
                            кг<br>
                            Нархи: <strong
                                class="h4">{{ number_format($statistics['monthly_price'], 0, '', ' ') }}</strong> сўм
                        </div>
                        <div class="col-md-3 alert alert-secondary">
                            <div class="fw-bold">Йиллик</div>
                            Маҳсулот: <strong
                                class="h4">{{ number_format($statistics['yearly_product'], 0, '', ' ') }}</strong>
                            та<br>
                            Брак: <strong
                                class="h4">{{ number_format($statistics['yearly_defect'], 3, '.', ' ') }}</strong>
                            кг<br>
                            Нархи: <strong
                                class="h4">{{ number_format($statistics['yearly_price'], 0, '', ' ') }}</strong> сўм
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        document.getElementById('shiftOutputWorkerListFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[user_id]"]').forEach(select => {
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
