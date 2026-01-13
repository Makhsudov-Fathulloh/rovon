<x-backend.layouts.main title="{{ 'Сменалар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <a href="{{ route('shift.create') }}" class="btn btn-primary w-100 w-md-auto">
                            {{ 'Яратиш' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="shiftFilterForm" method="GET" action="{{ route('shift.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('organization_title', 'Филиал') !!}</th>
                                <th>{!! sortLink('section_id', 'Бўлим') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th>Ходимлар</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th class="col-date">Бошланиш</th>
                                <th class="col-date">Тугаш</th>
                                <th></th> {{-- Search btn --}}
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
                                    <select name="filters[title]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($titles as $id => $title)
                                            <option
                                                value="{{ $title }}" {{ request('filters.title') == $title ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
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
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Services\StatusService::getList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.status') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="date" name="filters[created_at]"
                                           value="{{ request('filters.created_at') }}"
                                           class="form-control form-control-sm w-100" placeholder="Кун-Ой-Йил"
                                           style="display: none;"></th>
                                <th><input type="text" name="filters[created_at]"
                                           value="{{ request('filters.created_at') }}"
                                           class="form-control form-control-sm w-100" placeholder="Ой-Йил"
                                           style="display: none;"></th>

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
                            @forelse($shifts as $shift)
                                <tr class="text-center" id="row-{{ $shift->id }}">
                                    <td class="col-id">{{ $shift->id }}</td>
                                    <td>{{ optional($shift->section->organization)->title }}</td>
                                    <td>{{ optional($shift->section)->title }}</td>
                                    <td>{{ $shift->title }}</td>
                                    <td>
                                        @foreach($shift->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </td>
                                    <td style="width: 100px">{{ \App\Services\StatusService::getList()[$shift->status] ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shift->started_at)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shift->ended_at)->format('H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="shift" listRoute="shift-output" :id="$shift->id"
                                            subRoute="outputs"
                                            :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                            createTitle="Смена махсулотини яратиш"
                                            listTitle="Смен махсулотларни кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <select name="filters[title]" class="form-control form-control-sm filter-select2 w-100"
                                    data-placeholder="Смена номини киритинг">
                                <option value="">Барчаси</option>
                                @foreach($titles as $id => $title)
                                    <option
                                        value="{{ $title }}" {{ request('filters.title') == $title ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($shifts as $shift)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong>{{ $shift->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('organization_title', 'Филиал:') !!}</strong>{{ optional($shift->section->organization)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('section_id', 'Бўлим:') !!}</strong>{{ optional($shift->section)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи') !!}</strong>{{ $shift->title }} </p>
                                    <p class="card-text">
                                        <strong>Ходимлар:</strong>
                                        @foreach($shift->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('status', 'Статус:') !!}</strong> {{  \App\Services\StatusService::getList()[$shift->status] ?? '-' }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Бошланиш: </strong> {{ \Carbon\Carbon::parse($shift->started_at)->format('H:i') }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Тугаш: </strong> {{ \Carbon\Carbon::parse($shift->ended_at)->format('H:i') }}
                                    </p>
                                    <x-backend.action
                                        route="shift" listRoute="shift-output" :id="$shift->id"
                                        :add="true" :list="true" :view="true" :edit="true" :delete="true"
                                        subRoute="outputs"
                                        createTitle="Смена махсулотини яратиш"
                                        listTitle="Смена махсулотларини кўриш"
                                        viewClass="btn btn-secondary btn-sm"
                                    />
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
                    {{ $shifts->links('pagination::bootstrap-4') }}
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
                    <div class="col-md-12 mb-3">
                        <div class="card-stats count">
                            <div class="w-100">
                                <p>Сменалар</p>
                                <h5><strong>{{ number_format($shiftCount, 0, '', ' ') }} та</strong></h5>
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
        document.getElementById('shiftFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[title]"], select[name="filters[title]"]').forEach(select => {
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
